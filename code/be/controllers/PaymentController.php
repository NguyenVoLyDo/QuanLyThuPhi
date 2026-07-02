<?php
use MVC\Controller;

class PaymentController extends Controller
{
    private $paymentModel;
    private $studentModel;
    private $feeTypeModel;
    private $auditLog;

    public function __construct()
    {
        parent::__construct();
        require_once MODELS . 'Payment.php';
        require_once MODELS . 'Student.php';
        require_once MODELS . 'FeeType.php';
        require_once MODELS . 'ClassModel.php';
        require_once MODELS . 'AuditLog.php';
        require_once MODELS . 'PaymentProof.php';
        require_once MODELS . 'Refund.php';

        $this->paymentModel = new Payment();
        $this->studentModel = new Student();
        $this->feeTypeModel = new FeeType();
        $this->auditLog = new AuditLog();
    }

    /**
     * GET /api/payments
     */
    public function index()
    {
        $api_user = $this->auth(['Accountant', 'Teacher', 'Student']);

        $search = $this->request->get('search') ?? '';
        $role_id = $api_user['role_id'] ?? 0;
        $session_student_id = $api_user['student_id'] ?? 0;
        $role_name = $api_user['role_name'] ?? '';

        $is_student = ($role_id == 4 || $session_student_id > 0 || strcasecmp(trim($role_name), 'Student') === 0);

        if ($is_student) {
            $student_id = ($session_student_id > 0) ? $session_student_id : -1;
        } else {
            $student_id = $this->request->get('student_id') ?? '';
        }

        $is_teacher = ($role_id == 3 || strcasecmp(trim($role_name), 'Teacher') === 0);

        $from_date = $this->request->get('from_date') ?? '';
        $to_date = $this->request->get('to_date') ?? '';
        $page = (int)($this->request->get('page') ?? 1);
        $per_page = 10;

        $class_id = $this->request->get('class_id') ?? '';
        $fee_type_id = $this->request->get('fee_type_id') ?? '';

        $teacher_class_filter = null;
        if ($is_teacher) {
            $classModel = new ClassModel();
            $teacherClassIds = $classModel->getClassIdsByTeacher($api_user['user_id']);

            if (!empty($class_id) && in_array((int) $class_id, $teacherClassIds)) {
                $teacher_class_filter = (int) $class_id;
            } else {
                $teacher_class_filter = $teacherClassIds;
                if (empty($teacher_class_filter)) $teacher_class_filter = [-1]; 
            }
        }

        $filters = [
            'search' => $search,
            'student_id' => $student_id,
            'from_date' => $from_date,
            'to_date' => $to_date,
            'class_id' => ($is_teacher) ? '' : $class_id, 
            'fee_type_id' => $fee_type_id,
            'page' => $page,
            'per_page' => $per_page
        ];

        $viewer = [
            'role' => $role_name,
            'student_id' => $api_user['student_id'] ?? null,
            'class_id' => ($is_teacher) ? $teacher_class_filter : null
        ];

        $total = (int) $this->paymentModel->countAll($filters, $viewer);
        $payments = $this->paymentModel->getAll($filters, $viewer);

        $classModel = new ClassModel();
        $classes = $classModel->getAll();

        if (strcasecmp(trim($api_user['role_name']), 'Teacher') === 0) {
            if (!isset($teacherClassIds)) {
                $teacherClassIds = $classModel->getClassIdsByTeacher($api_user['user_id']);
            }
            $classes = array_filter($classes, function ($c) use ($teacherClassIds) {
                return in_array($c['id'], $teacherClassIds);
            });
            $classes = array_values($classes);
        }

        $feeTypes = $this->feeTypeModel->getAll();

        $this->send(200, true, "Thành công", [
            'payments' => $payments,
            'classes' => $classes,
            'feeTypes' => $feeTypes,
            'pagination' => paginate($total, $per_page, $page)
        ]);
    }

    /**
     * GET /api/payments/export
     */
    public function export()
    {
        $api_user = $this->auth(['Accountant', 'Teacher']);

        $search = $this->request->get('search') ?? '';
        $student_id = $this->request->get('student_id') ?? '';
        $from_date = $this->request->get('from_date') ?? '';
        $to_date = $this->request->get('to_date') ?? '';
        $class_id = $this->request->get('class_id') ?? '';
        $fee_type_id = $this->request->get('fee_type_id') ?? '';

        $role_name = $api_user['role_name'] ?? '';
        $is_teacher = (strcasecmp(trim($role_name), 'Teacher') === 0);

        if ($is_teacher) {
            $classModel = new ClassModel();
            $teacherClassIds = $classModel->getClassIdsByTeacher($api_user['user_id']);
            $validIds = array_map('strval', $teacherClassIds);

            if (!empty($class_id) && in_array((string)$class_id, $validIds)) {
                // Keep class_id
            } else {
                $class_id = $teacherClassIds;
                if (empty($class_id)) $class_id = [-1];
            }
        }

        $filters = [
            'search' => $search,
            'student_id' => $student_id,
            'from_date' => $from_date,
            'to_date' => $to_date,
            'class_id' => $class_id,
            'fee_type_id' => $fee_type_id,
            'page' => 1,
            'per_page' => 100000
        ];

        $viewer = [
            'role' => $role_name,
            'student_id' => $api_user['student_id'] ?? null,
            'class_id' => ($is_teacher) ? $class_id : null
        ];

        $payments = $this->paymentModel->getAll($filters, $viewer);
        $this->send(200, true, "Thành công", ['payments' => $payments]);
    }

    /**
     * GET /api/payments/create
     */
    public function create()
    {
        $api_user = $this->auth(['Accountant', 'Teacher', 'Student']);

        $selected_student_id = $this->request->get('student_id') ?? '';

        if ($api_user['role_name'] === 'Student' && isset($api_user['student_id'])) {
            $selected_student_id = $api_user['student_id'];
        }

        $students = $this->studentModel->getAll('', '', 1, 1000); 
        $fee_types = $this->feeTypeModel->getActive();
        $payment_methods = $this->paymentModel->getPaymentMethods();

        $student_debts = [];
        if ($selected_student_id) {
            $student_debts = $this->studentModel->getDebts($selected_student_id);
        }

        $this->send(200, true, "Thành công", [
            'students' => $students,
            'fee_types' => $fee_types,
            'payment_methods' => $payment_methods,
            'student_debts' => $student_debts,
            'selected_student_id' => $selected_student_id
        ]);
    }

    /**
     * POST /api/payments
     */
    public function store()
    {
        $api_user = $this->auth(['Accountant', 'Teacher', 'Student']);
        $input = $this->request->all();

        if (isset($input['is_multi']) && $input['is_multi']) {
            return $this->apiMultiStore($api_user, $input);
        }

        $this->paymentModel->student_id = $input['student_id'] ?? null;

        if ($api_user['role_name'] === 'Student') {
            if (isset($api_user['student_id'])) {
                $this->paymentModel->student_id = $api_user['student_id'];
            }
            $this->paymentModel->status = 'Pending';
        } else {
            $this->paymentModel->status = 'Completed';
        }

        $this->paymentModel->fee_type_id = $input['fee_type_id'] ?? null;
        $this->paymentModel->amount_paid = floatval($input['amount_paid'] ?? 0);
        $this->paymentModel->payment_date = $input['payment_date'] ?? date('Y-m-d H:i:s');
        $this->paymentModel->payment_method = $input['payment_method'] ?? '';
        $this->paymentModel->collected_by = $api_user['user_id'];
        $this->paymentModel->receipt_number = clean_input($input['receipt_number'] ?? '');
        $this->paymentModel->notes = clean_input($input['notes'] ?? '');
        
        $errors = $this->validatePayment();
        if (!empty($errors)) {
            $this->send(400, false, "Lỗi dữ liệu", ['errors' => $errors]);
        }

        $result = $this->paymentModel->create();

        if ($result['success']) {
            $this->auditLog->log($api_user['user_id'], 'CREATE_PAYMENT', 'payment', $result['id'], "Mã phiếu: {$result['payment_code']}, Số tiền: {$this->paymentModel->amount_paid}");
            $this->send(200, true, $result['message'], ['id' => $result['id']]);
        } else {
            $this->send(500, false, $result['message']);
        }
    }

    private function apiMultiStore($api_user, $input)
    {
        $student_id = $input['student_id'] ?? null;
        $fee_type_ids = $input['fee_type_ids'] ?? [];
        $amounts = $input['amounts'] ?? [];

        if (empty($fee_type_ids)) {
            $this->send(400, false, 'Vui lòng chọn ít nhất một khoản thu!');
        }

        $payments_data = [];
        foreach ($fee_type_ids as $ft_id) {
            if (isset($amounts[$ft_id]) && $amounts[$ft_id] > 0) {
                $payments_data[] = [
                    'fee_type_id' => $ft_id,
                    'amount_paid' => floatval($amounts[$ft_id])
                ];
            }
        }

        $common_data = [
            'payment_date' => $input['payment_date'] ?? date('Y-m-d H:i:s'),
            'payment_method' => $input['payment_method'] ?? '',
            'collected_by' => $api_user['user_id'],
            'receipt_number' => clean_input($input['receipt_number'] ?? ''),
            'notes' => clean_input($input['notes'] ?? ''),
            'status' => 'Completed'
        ];

        $result = $this->paymentModel->createMultiple($student_id, $payments_data, $common_data);

        if ($result['success']) {
            $this->auditLog->log($api_user['user_id'], 'CREATE_MULTI_PAYMENT', 'student', $student_id, "Thu " . count($payments_data) . " khoản phí");
            $this->send(200, true, $result['message'], ['student_id' => $student_id]);
        } else {
            $this->send(500, false, $result['message']);
        }
    }

    /**
     * GET /api/payments/:id
     */
    public function view($params)
    {
        $api_user = $this->auth(['Accountant', 'Teacher', 'Student']);
        $id = $params['id'] ?? 0;
        $payment = $this->paymentModel->getById($id);

        if (!$payment) {
            $this->send(404, false, 'Không tìm thấy phiếu thu!');
        }

        if (strcasecmp(trim($api_user['role_name']), 'Student') === 0 && $payment['student_id'] != $api_user['student_id']) {
            $this->send(403, false, 'Bạn không có quyền xem biên lai này!');
        }

        $this->send(200, true, "Thành công", ['payment' => $payment]);
    }

    /**
     * DELETE /api/payments/:id
     */
    public function delete($params)
    {
        $api_user = $this->auth(['Accountant']);
        $id = $params['id'] ?? 0;

        $result = $this->paymentModel->delete($id);

        if ($result['success']) {
            $this->auditLog->log($api_user['user_id'], 'DELETE_PAYMENT', 'payment', $id, "Xóa thanh toán ID: $id");
            $this->send(200, true, $result['message']);
        } else {
            $this->send(500, false, $result['message']);
        }
    }

    /**
     * POST /api/payments/:id/refund
     */
    public function refundStore($params)
    {
        $api_user = $this->auth(['Accountant']);
        $input = $this->request->all();
        $payment_id = $params['id'] ?? 0;

        $refundModel = new Refund();
        $amount = floatval($input['amount'] ?? 0);
        $reason = clean_input($input['reason'] ?? '');
        $refunded_by = $api_user['user_id'];

        $result = $refundModel->create($payment_id, $amount, $reason, $refunded_by);

        if ($result['success']) {
            $this->auditLog->log($api_user['user_id'], 'REFUND_PAYMENT', 'payment', $payment_id, "Hoàn tiền số tiền: $amount, Lý do: $reason");
            $this->send(200, true, $result['message']);
        } else {
            $this->send(500, false, $result['message']);
        }
    }

    /**
     * GET /api/payments/refunds
     */
    public function refunds()
    {
        $this->auth(['Accountant']);
        $refundModel = new Refund();

        $page = (int)($this->request->get('page') ?? 1);
        $per_page = 10;

        $total = (int) $refundModel->countAll();
        $refunds = $refundModel->getAll($page, $per_page);

        $this->send(200, true, "Thành công", [
            'refunds' => $refunds,
            'pagination' => paginate($total, $per_page, $page)
        ]);
    }

    /**
     * GET /api/payments/my-debts
     */
    public function myDebts()
    {
        $api_user = $this->auth(['Student']);
        $student_id = $api_user['student_id'] ?? 0;

        $database = new \Database(); // Re-use be/config/database.php if needed, but System\MVC\Model already has PDO
        $conn = (new \MVC\Model())->db;

        $stmt = $conn->prepare("
            SELECT sd.*, ft.fee_name, ft.fee_category, ft.academic_year, ft.semester,
                   (sd.total_amount - sd.paid_amount) as remaining_amount,
                   (SELECT COUNT(*) FROM payment_proofs pp 
                    WHERE pp.student_id = sd.student_id 
                    AND pp.fee_type_id = sd.fee_type_id 
                    AND pp.status = 'Pending') as has_pending_proof
            FROM student_debts sd
            INNER JOIN fee_types ft ON sd.fee_type_id = ft.id
            WHERE sd.student_id = :student_id AND sd.status != 'Paid'
            ORDER BY ft.academic_year DESC, ft.semester DESC, sd.due_date ASC
        ");
        $stmt->execute(['student_id' => $student_id]);
        $raw_debts = $stmt->fetchAll(\PDO::FETCH_ASSOC);

        $grouped_debts = [];
        foreach ($raw_debts as $debt) {
            $year = $debt['academic_year'] ?: 'Năm học khác';
            $sem = $debt['semester'] ? 'Học kỳ ' . $debt['semester'] : 'Học kỳ khác';

            if (!isset($grouped_debts[$year])) $grouped_debts[$year] = [];
            if (!isset($grouped_debts[$year][$sem])) $grouped_debts[$year][$sem] = [];
            $grouped_debts[$year][$sem][] = $debt;
        }

        $this->send(200, true, "Thành công", ['grouped_debts' => $grouped_debts]);
    }

    /**
     * GET /api/student-debts/:id
     */
    public function getStudentDebts($params)
    {
        $this->auth(['Accountant']);
        $student_id = $params['id'] ?? 0;

        if (empty($student_id)) {
            $this->send(400, false, "Không có student_id");
        }

        $debts = $this->studentModel->getDebts($student_id);
        $this->send(200, true, "Thành công", ['debts' => $debts]);
    }

    /**
     * POST /api/payments/proofs
     */
    public function storeProof()
    {
        $api_user = $this->auth(['Student']);
        $input = $this->request->all();
        
        $fee_type_id = $input['fee_type_id'] ?? 0;
        $amount = $input['amount'] ?? 0;

        if (!$fee_type_id || !$amount) {
            $this->send(400, false, "Dữ liệu không hợp lệ! Vui lòng thử lại.");
        }

        $proofModel = new PaymentProof();
        $files = $this->request->files;

        if (isset($files['proof_image']) && $files['proof_image']['error'] == 0) {
            $allowed = ['jpg', 'jpeg', 'png', 'pdf'];
            $filename = $files['proof_image']['name'];
            $ext = pathinfo($filename, PATHINFO_EXTENSION);
            
            if (!in_array(strtolower($ext), $allowed)) {
                $this->send(400, false, "Chỉ chấp nhận file ảnh (JPG, PNG) hoặc PDF!");
            }

            $upload_dir = SCRIPT . 'uploads/proofs/';
            if (!file_exists($upload_dir)) {
                mkdir($upload_dir, 0777, true);
            }

            $new_filename = 'proof_' . time() . '_' . $api_user['student_id'] . '.' . $ext;
            $destination = $upload_dir . $new_filename;

            if (move_uploaded_file($files['proof_image']['tmp_name'], $destination)) {
                $proofModel->student_id = $api_user['student_id'];
                $proofModel->fee_type_id = $fee_type_id;
                $proofModel->amount = $amount;
                $proofModel->image_path = 'uploads/proofs/' . $new_filename;

                $result = $proofModel->create();

                if ($result['success']) {
                    $this->send(200, true, "Đã gửi minh chứng thành công! Kế toán sẽ duyệt sớm.");
                } else {
                    $this->send(500, false, $result['message']);
                }
            } else {
                $this->send(500, false, "Lỗi khi upload file!");
            }
        } else {
            $this->send(400, false, "Vui lòng chọn file minh chứng!");
        }
    }

    /**
     * GET /api/payments/proofs
     */
    public function manageProofs()
    {
        $this->auth(['Accountant']);
        $proofModel = new PaymentProof();

        $status = $this->request->get('status') ?? 'Pending';
        $proofs = $proofModel->getAll('', $status);

        $this->send(200, true, "Thành công", ['proofs' => $proofs]);
    }

    /**
     * POST /api/payments/proofs/:id/approve
     */
    public function approveProof($params)
    {
        $api_user = $this->auth(['Accountant']);
        $id = $params['id'] ?? 0;

        $proofModel = new PaymentProof();
        $proof = $proofModel->getById($id);

        if (!$proof || $proof['status'] != 'Pending') {
            $this->send(404, false, "Minh chứng không tồn tại hoặc đã được xử lý!");
        }

        $this->paymentModel->student_id = $proof['student_id'];
        $this->paymentModel->fee_type_id = $proof['fee_type_id'];
        $this->paymentModel->amount_paid = $proof['amount'];
        $this->paymentModel->payment_date = date('Y-m-d H:i:s');
        $this->paymentModel->payment_method = 'Transfer'; 
        $this->paymentModel->collected_by = $api_user['user_id'];
        $this->paymentModel->receipt_number = 'CK-' . $proof['id']; 
        $this->paymentModel->notes = 'Duyệt minh chứng chuyển khoản #' . $proof['id'];
        $this->paymentModel->status = 'Completed';

        $result = $this->paymentModel->create();

        if ($result['success']) {
            $proofModel->updateStatus($id, 'Approved', 'Đã duyệt và tạo phiếu thu: ' . $result['payment_code']);
            $this->auditLog->log($api_user['user_id'], 'APPROVE_PROOF', 'payment_proof', $id, "Duyệt minh chứng #$id, tạo phiếu thu " . $result['payment_code']);
            $this->send(200, true, "Đã duyệt minh chứng và tạo phiếu thu thành công!");
        } else {
            $this->send(500, false, "Lỗi khi tạo phiếu thu: " . $result['message']);
        }
    }

    /**
     * POST /api/payments/proofs/:id/reject
     */
    public function rejectProof($params)
    {
        $api_user = $this->auth(['Accountant']);
        $id = $params['id'] ?? 0;
        $input = $this->request->all();
        $reason = clean_input($input['reason'] ?? '');

        $proofModel = new PaymentProof();
        $proofModel->updateStatus($id, 'Rejected', $reason);
        $this->auditLog->log($api_user['user_id'], 'REJECT_PROOF', 'payment_proof', $id, "Từ chối minh chứng #$id. Lý do: $reason");

        $this->send(200, true, "Đã từ chối minh chứng!");
    }

    private function validatePayment()
    {
        $errors = [];
        if (empty($this->paymentModel->student_id)) $errors['student_id'] = 'Vui lòng chọn học sinh!';
        if (empty($this->paymentModel->fee_type_id)) $errors['fee_type_id'] = 'Vui lòng chọn khoản thu!';
        if (empty($this->paymentModel->amount_paid) || $this->paymentModel->amount_paid <= 0) $errors['amount_paid'] = 'Vui lòng nhập số tiền hợp lệ!';
        if (empty($this->paymentModel->payment_date)) $errors['payment_date'] = 'Vui lòng chọn ngày thanh toán!';
        if (empty($this->paymentModel->payment_method)) $errors['payment_method'] = 'Vui lòng chọn phương thức thanh toán!';
        return $errors;
    }
}