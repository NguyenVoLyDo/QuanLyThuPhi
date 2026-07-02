<?php
use MVC\Controller;

class DebtController extends Controller
{
    private $studentModel;
    private $feeTypeModel;
    private $classModel;
    private $auditLog;

    public function __construct()
    {
        parent::__construct();
        require_once MODELS . 'Student.php';
        require_once MODELS . 'FeeType.php';
        require_once MODELS . 'ClassModel.php';
        require_once MODELS . 'AuditLog.php';

        $this->studentModel = new Student();
        $this->feeTypeModel = new FeeType();
        $this->classModel = new ClassModel();
        $this->auditLog = new AuditLog();
    }

    /**
     * GET /api/debts/batch
     */
    public function createBatch()
    {
        $this->auth(['Accountant']);

        $semesters = $this->feeTypeModel->getSemesters();
        $classes = $this->classModel->getAll();
        $feeTypes = $this->feeTypeModel->getActive();

        $this->send(200, true, "Thành công", [
            'semesters' => $semesters,
            'classes' => $classes,
            'feeTypes' => $feeTypes
        ]);
    }

    /**
     * POST /api/debts/batch
     */
    public function storeBatch()
    {
        $api_user = $this->auth(['Accountant']);
        $input = $this->request->all();

        $year = $input['academic_year'] ?? '';
        $semester = $input['semester'] ?? '';
        $class_id = $input['class_id'] ?? '';
        $specific_fee_id = $input['fee_type_id'] ?? '';

        if (empty($year) || empty($semester)) {
            $this->send(400, false, 'Vui lòng chọn Năm học và Học kỳ!');
        }

        $students = $this->studentModel->getAll('', $class_id, 1, 10000);

        $fees = [];
        if (!empty($specific_fee_id)) {
            $fee = $this->feeTypeModel->getById($specific_fee_id);
            if ($fee) {
                $fees[] = $fee;
            }
        } else {
            $fees = $this->feeTypeModel->getBySemester($year, $semester);
        }

        if (empty($fees)) {
            $this->send(400, false, 'Không tìm thấy khoản thu nào!');
        }

        $conn = (new \MVC\Model())->db;
        $count_success = 0;
        $count_skip = 0;

        foreach ($students as $student) {
            // Lấy thông tin miễn giảm của học sinh
            $exemptionsStmt = $conn->prepare("
                SELECT e.discount_type, e.discount_value 
                FROM student_exemptions se
                INNER JOIN exemptions e ON se.exemption_id = e.id
                WHERE se.student_id = :sid
            ");
            $exemptionsStmt->execute(['sid' => $student['id']]);
            $exemptions = $exemptionsStmt->fetchAll(\PDO::FETCH_ASSOC);

            foreach ($fees as $fee) {
                // Kiểm tra xem đã tồn tại công nợ này chưa
                $check = $conn->prepare("SELECT id FROM student_debts WHERE student_id = :sid AND fee_type_id = :fid");
                $check->execute(['sid' => $student['id'], 'fid' => $fee['id']]);

                if ($check->rowCount() == 0) {
                    $originalAmount = $fee['amount'];
                    $finalAmount = $originalAmount;

                    foreach ($exemptions as $exemption) {
                        if ($exemption['discount_type'] === 'Percent') {
                            $discount = ($originalAmount * $exemption['discount_value']) / 100;
                            $finalAmount -= $discount;
                        } else {
                            $finalAmount -= $exemption['discount_value'];
                        }
                    }

                    $finalAmount = max(0, $finalAmount);

                    $ins = $conn->prepare("INSERT INTO student_debts (student_id, fee_type_id, total_amount, paid_amount, status, due_date) VALUES (:sid, :fid, :amount, 0, 'Unpaid', :due)");
                    $ins->execute([
                        'sid' => $student['id'],
                        'fid' => $fee['id'],
                        'amount' => $finalAmount,
                        'due' => $fee['end_date']
                    ]);
                    $count_success++;
                } else {
                    $count_skip++;
                }
            }
        }

        $this->auditLog->log($api_user['user_id'], 'BATCH_DEBT', 'student_debts', 0, "Tạo $count_success công nợ cho $year - HK$semester (áp dụng miễn giảm)");

        $scopeText = empty($class_id) ? "toàn trường" : "theo lớp đã chọn";
        $this->send(200, true, "Đã tạo thành công $count_success công nợ mới ($scopeText). Bỏ qua $count_skip đã tồn tại.");
    }
}