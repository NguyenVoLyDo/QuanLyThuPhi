<?php
use MVC\Controller;

class ReportController extends Controller
{
    private $paymentModel;
    private $studentModel;

    public function __construct()
    {
        parent::__construct();
        require_once MODELS . 'Payment.php';
        require_once MODELS . 'Student.php';
        $this->paymentModel = new Payment();
        $this->studentModel = new Student();
    }

    /**
     * GET /api/reports
     */
    public function index()
    {
        $this->auth(['Accountant', 'Teacher']);

        $from_date = $this->request->get('from_date') ?? date('Y-m-01');
        $to_date = $this->request->get('to_date') ?? date('Y-m-d');

        $conn = (new \MVC\Model())->db;

        $revenue = $this->paymentModel->getTotalRevenue($from_date, $to_date);

        $stmt = $conn->prepare("
            SELECT ft.fee_category, ft.fee_name,
                   COUNT(p.id) as payment_count,
                   SUM(p.amount_paid) as total_amount
            FROM payments p
            INNER JOIN fee_types ft ON p.fee_type_id = ft.id
            WHERE p.payment_date BETWEEN :from_date AND :to_date
            AND p.status = 'Completed'
            GROUP BY ft.fee_category, ft.fee_name
            ORDER BY total_amount DESC
        ");
        $stmt->execute(['from_date' => $from_date, 'to_date' => $to_date]);
        $revenue_by_category = $stmt->fetchAll();

        $stmt = $conn->prepare("
            SELECT payment_method,
                   COUNT(*) as payment_count,
                   SUM(amount_paid) as total_amount
            FROM payments
            WHERE payment_date BETWEEN :from_date AND :to_date
            AND status = 'Completed'
            GROUP BY payment_method
        ");
        $stmt->execute(['from_date' => $from_date, 'to_date' => $to_date]);
        $revenue_by_method = $stmt->fetchAll();

        $debt_search = $this->request->get('debt_search') ?? '';
        $query = "
            SELECT s.student_code, s.full_name, c.class_name,
                   SUM(sd.total_amount - sd.paid_amount) as total_debt
            FROM student_debts sd
            INNER JOIN students s ON sd.student_id = s.id
            INNER JOIN classes c ON s.class_id = c.id
            WHERE sd.status != 'Paid'
        ";

        if ($debt_search) {
            $query .= " AND (s.full_name LIKE :debt_search OR s.student_code LIKE :debt_search)";
        }

        $query .= " GROUP BY s.id HAVING total_debt > 0 ORDER BY total_debt DESC LIMIT 50";

        $stmt = $conn->prepare($query);
        if ($debt_search) {
            $stmt->execute(['debt_search' => "%$debt_search%"]);
        } else {
            $stmt->execute();
        }
        $students_with_debt = $stmt->fetchAll();

        $this->send(200, true, "Thành công", [
            'revenue' => $revenue,
            'revenue_by_category' => $revenue_by_category,
            'revenue_by_method' => $revenue_by_method,
            'students_with_debt' => $students_with_debt
        ]);
    }

    /**
     * GET /api/reports/payments
     */
    public function exportPayments()
    {
        $api_user = $this->auth(['Accountant', 'Teacher']);

        $from_date = $this->request->get('from_date') ?? '';
        $to_date = $this->request->get('to_date') ?? '';

        $teacher_class_ids = null;
        if ($api_user['role_name'] === 'Teacher') {
            require_once MODELS . 'ClassModel.php';
            $classModel = new ClassModel();
            $teacher_class_ids = $classModel->getClassIdsByTeacher($api_user['user_id']);
            if (empty($teacher_class_ids)) {
                $teacher_class_ids = [-1];
            }
        }

        $filters = [
            'search' => '',
            'student_id' => '',
            'from_date' => $from_date,
            'to_date' => $to_date,
            'class_id' => '',
            'fee_type_id' => '',
            'page' => 1,
            'per_page' => 100000
        ];

        $viewer = [
            'role' => $api_user['role_name'],
            'student_id' => null,
            'class_id' => $teacher_class_ids
        ];

        $payments = $this->paymentModel->getAll($filters, $viewer);

        $this->send(200, true, "Thành công", ['payments' => $payments]);
    }

    /**
     * GET /api/reports/debts
     */
    public function exportDebts()
    {
        $api_user = $this->auth(['Accountant', 'Teacher']);

        $conn = (new \MVC\Model())->db;

        $query = "
            SELECT s.student_code, s.full_name, c.class_name,
                   ft.fee_name, ft.fee_category,
                   sd.total_amount, sd.paid_amount, (sd.total_amount - sd.paid_amount) as remaining_amount,
                   sd.due_date, sd.status
            FROM student_debts sd
            INNER JOIN students s ON sd.student_id = s.id
            INNER JOIN classes c ON s.class_id = c.id
            INNER JOIN fee_types ft ON sd.fee_type_id = ft.id
            WHERE sd.status != 'Paid'
        ";

        $params = [];
        if ($api_user['role_name'] === 'Teacher') {
            require_once MODELS . 'ClassModel.php';
            $classModel = new ClassModel();
            $class_ids = $classModel->getClassIdsByTeacher($api_user['user_id']);
            if (empty($class_ids))
                $class_ids = [-1];

            $inQuery = implode(',', array_map(function ($k) {
                return ":class_id_$k";
            }, array_keys($class_ids)));
            $query .= " AND s.class_id IN ($inQuery)";

            foreach ($class_ids as $k => $id) {
                $params[":class_id_$k"] = $id;
            }
        }

        $query .= " ORDER BY s.class_id, s.student_code";

        $stmt = $conn->prepare($query);
        $stmt->execute($params);
        $debts = $stmt->fetchAll();

        $this->send(200, true, "Thành công", ['debts' => $debts]);
    }
}