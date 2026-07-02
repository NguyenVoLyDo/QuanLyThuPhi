<?php
use MVC\Controller;

class ExemptionController extends Controller
{
    private $exemptionModel;
    private $studentModel;

    public function __construct()
    {
        parent::__construct();
        require_once MODELS . 'Exemption.php';
        require_once MODELS . 'ClassModel.php';
        require_once MODELS . 'Student.php';
        $this->exemptionModel = new Exemption();
        $this->studentModel = new Student();
    }

    /**
     * GET /api/exemptions
     */
    public function index()
    {
        $this->auth(['Accountant', 'Teacher']);

        $search = $this->request->get('search') ?? '';
        $page = (int)($this->request->get('page') ?? 1);
        $per_page = 10;

        $total = (int) $this->exemptionModel->countAll($search);
        $exemptions = $this->exemptionModel->getAll($search, $page, $per_page);

        $this->send(200, true, "Thành công", [
            'exemptions' => $exemptions,
            'pagination' => paginate($total, $per_page, $page)
        ]);
    }

    /**
     * GET /api/exemptions/create
     */
    public function create()
    {
        $this->auth(['Accountant']);
        $this->send(200, true, "Thành công");
    }

    /**
     * POST /api/exemptions
     */
    public function store()
    {
        $this->auth(['Accountant']);
        $input = $this->request->all();

        $this->exemptionModel->name = clean_input($input['name'] ?? '');
        $this->exemptionModel->discount_type = $input['discount_type'] ?? 'Fixed';
        $this->exemptionModel->discount_value = floatval($input['discount_value'] ?? 0);
        $this->exemptionModel->description = clean_input($input['description'] ?? '');

        if (empty($this->exemptionModel->name) || empty($this->exemptionModel->discount_value)) {
            $this->send(400, false, 'Vui lòng nhập tên và giá trị!');
        }

        $res = $this->exemptionModel->create();
        if ($res['success']) {
            $this->send(200, true, 'Tạo phân bổ miễn giảm thành công!');
        } else {
            $this->send(500, false, $res['message']);
        }
    }

    /**
     * GET /api/exemptions/:id/edit
     */
    public function edit($params)
    {
        $this->auth(['Accountant']);
        $id = $params['id'] ?? 0;
        $exemption = $this->exemptionModel->getById($id);

        if (!$exemption) {
            $this->send(404, false, 'Không tìm thấy chính sách!');
        }
        $this->send(200, true, "Thành công", ['exemption' => $exemption]);
    }

    /**
     * PUT /api/exemptions/:id
     */
    public function update($params)
    {
        $this->auth(['Accountant']);
        $input = $this->request->all();
        $id = $params['id'] ?? 0;

        $this->exemptionModel->id = $id;
        $this->exemptionModel->name = clean_input($input['name'] ?? '');
        $this->exemptionModel->discount_type = $input['discount_type'] ?? 'Fixed';
        $this->exemptionModel->discount_value = floatval($input['discount_value'] ?? 0);
        $this->exemptionModel->description = clean_input($input['description'] ?? '');

        $res = $this->exemptionModel->update();
        if ($res['success']) {
            $this->send(200, true, 'Sửa miễn giảm thành công!');
        } else {
            $this->send(500, false, $res['message']);
        }
    }

    /**
     * DELETE /api/exemptions/:id
     */
    public function delete($params)
    {
        $this->auth(['Accountant']);
        $id = $params['id'] ?? 0;

        $res = $this->exemptionModel->delete($id);

        if ($res['success']) {
            $this->send(200, true, 'Xóa thành công!');
        } else {
            $this->send(500, false, $res['message']);
        }
    }

    /**
     * POST /api/exemptions/assign
     */
    public function assign()
    {
        $this->auth(['Accountant']);
        $input = $this->request->all();

        $student_id = $input['student_id'] ?? 0;
        $exemption_id = $input['exemption_id'] ?? 0;

        $res = $this->exemptionModel->assignToStudent($student_id, $exemption_id);

        if ($res['success']) {
            $this->recalculateStudentDebts($student_id);
            $this->send(200, true, 'Đã gán miễn giảm thành công! Các khoản nợ đã được cập nhật.');
        } else {
            $this->send(500, false, $res['message']);
        }
    }

    private function recalculateStudentDebts($student_id)
    {
        $conn = (new \MVC\Model())->db;
        
        $exemptionsStmt = $conn->prepare("
            SELECT e.discount_type, e.discount_value 
            FROM student_exemptions se
            INNER JOIN exemptions e ON se.exemption_id = e.id
            WHERE se.student_id = :sid
        ");
        $exemptionsStmt->execute(['sid' => $student_id]);
        $exemptions = $exemptionsStmt->fetchAll(\PDO::FETCH_ASSOC);
        
        $debtsStmt = $conn->prepare("
            SELECT sd.id, sd.fee_type_id, sd.paid_amount, ft.amount as original_amount
            FROM student_debts sd
            INNER JOIN fee_types ft ON sd.fee_type_id = ft.id
            WHERE sd.student_id = :sid
        ");
        $debtsStmt->execute(['sid' => $student_id]);
        $debts = $debtsStmt->fetchAll(\PDO::FETCH_ASSOC);
        
        foreach ($debts as $debt) {
            $originalAmount = $debt['original_amount'];
            $finalAmount = $originalAmount;
            
            if ($debt['paid_amount'] < $originalAmount) {
                foreach ($exemptions as $exemption) {
                    if ($exemption['discount_type'] === 'Percent') {
                        $discount = ($originalAmount * $exemption['discount_value']) / 100;
                        $finalAmount -= $discount;
                    } else {
                        $finalAmount -= $exemption['discount_value'];
                    }
                }
            }
            
            $finalAmount = max(0, $finalAmount);
            
            $paidAmount = $debt['paid_amount'];
            $newStatus = 'Unpaid';
            if ($paidAmount >= $finalAmount) {
                $newStatus = 'Paid';
            } elseif ($paidAmount > 0) {
                $newStatus = 'Partial';
            }
            
            $updateStmt = $conn->prepare("
                UPDATE student_debts 
                SET total_amount = :total, status = :status 
                WHERE id = :id
            ");
            $updateStmt->execute([
                'total' => $finalAmount,
                'status' => $newStatus,
                'id' => $debt['id']
            ]);
        }
    }

    /**
     * POST /api/exemptions/revoke
     */
    public function revoke()
    {
        $this->auth(['Accountant', 'Teacher']);
        $input = $this->request->all();

        $student_id = $input['student_id'] ?? 0;
        $exemption_id = $input['exemption_id'] ?? 0;

        $res = $this->exemptionModel->revokeFromStudent($student_id, $exemption_id);

        if ($res['success']) {
            $this->recalculateStudentDebts($student_id);
            $this->send(200, true, 'Hủy miễn giảm thành công');
        } else {
            $this->send(500, false, $res['message']);
        }
    }
}