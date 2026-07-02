<?php
use MVC\Controller;

class AuthController extends Controller
{
    private $db;

    public function __construct()
    {
        parent::__construct();
        $database = new Database();
        $this->db = $database->getConnection();
    }

    /**
     * POST /api/auth/login
     */
    public function login()
    {
        $input    = $this->request->all();
        $username = trim($input['username'] ?? '');
        $password = $input['password'] ?? '';

        if (empty($username) || empty($password)) {
            $this->send(400, false, 'Vui lòng nhập đầy đủ tài khoản và mật khẩu');
        }

        $stmt = $this->db->prepare(
            "SELECT u.*, r.role_name 
             FROM users u 
             LEFT JOIN roles r ON u.role_id = r.id 
             WHERE u.username = :username AND u.is_active = 1 
             LIMIT 1"
        );
        $stmt->bindParam(':username', $username);
        $stmt->execute();
        $user = $stmt->fetch(PDO::FETCH_ASSOC);

        if ($user && password_verify($password, $user['password'])) {
            $teacher_class_ids = [];
            if (($user['role_name'] ?? '') === 'Teacher') {
                $stmtClass = $this->db->prepare("SELECT id FROM classes WHERE teacher_id = :tid");
                $stmtClass->execute(['tid' => $user['id']]);
                $teacher_class_ids = $stmtClass->fetchAll(PDO::FETCH_COLUMN) ?: [];
            }

            $payload = [
                'user_id'           => $user['id'],
                'username'          => $user['username'],
                'role_id'           => $user['role_id'],
                'role_name'         => $user['role_name'] ?? 'User',
                'full_name'         => $user['full_name'],
                'student_id'        => $user['student_id'],
                'teacher_class_ids' => $teacher_class_ids
            ];

            $token = \JWT::encode($payload);

            $this->send(200, true, 'Đăng nhập thành công', [
                'token' => $token,
                'user'  => $payload
            ]);
        } else {
            $this->send(401, false, 'Sai tên đăng nhập hoặc mật khẩu');
        }
    }

    /**
     * GET /api/dashboard/stats
     */
    public function stats()
    {
        $api_user = $this->auth(); // Must be logged in

        try {
            $stats = [];
            
            // Total Students
            $stats['total_students'] = (int) $this->db->query("SELECT COUNT(*) FROM students WHERE is_active = 1")->fetchColumn();
            
            // Total Fee Types (Active)
            $stats['total_fee_types'] = (int) $this->db->query("SELECT COUNT(*) FROM fee_types WHERE status = 'Active'")->fetchColumn();
            
            // Total Revenue (Completed payments)
            $stats['total_revenue'] = (float) $this->db->query("SELECT SUM(amount_paid) FROM payments WHERE status = 'Completed'")->fetchColumn();
            
            // Total Unpaid Debts (Count of unpaid student debts)
            $stats['total_payments'] = (int) $this->db->query("SELECT COUNT(*) FROM payments WHERE status = 'Completed'")->fetchColumn();
            
            // Unpaid Debt grouped by semester - filtered by class for Teachers
            $stats['unpaid_by_semester'] = [];
            if (in_array($api_user['role_name'], ['Accountant', 'Teacher'])) {
                $sql = "
                    SELECT ft.academic_year, ft.semester, COUNT(DISTINCT sd.student_id) as unpaid_count
                    FROM student_debts sd
                    INNER JOIN fee_types ft ON sd.fee_type_id = ft.id
                    INNER JOIN students s ON sd.student_id = s.id
                    WHERE sd.status != 'Paid'
                ";

                // If teacher, filter by their classes
                if ($api_user['role_name'] === 'Teacher') {
                    $class_ids = $api_user['teacher_class_ids'] ?? [];
                    if (!empty($class_ids)) {
                        $ids_str = implode(',', array_map('intval', $class_ids));
                        $sql .= " AND s.class_id IN ($ids_str)";
                    } else {
                        // If teacher has no classes, ensure they see 0 results
                        $sql .= " AND 1=0";
                    }
                }

                $sql .= " GROUP BY ft.academic_year, ft.semester";
                $stmt = $this->db->query($sql);
                $stats['unpaid_by_semester'] = $stmt->fetchAll(PDO::FETCH_ASSOC);
            }

            // Student personal debt info
            $stats['personal_debts'] = [];
            $stats['personal_total_debt'] = 0;
            if ($api_user['role_name'] === 'Student' && !empty($api_user['student_id'])) {
                $stmt = $this->db->prepare("
                    SELECT sd.*, ft.fee_name, ft.academic_year, ft.semester 
                    FROM student_debts sd 
                    JOIN fee_types ft ON sd.fee_type_id = ft.id 
                    WHERE sd.student_id = :sid
                ");
                $stmt->execute(['sid' => $api_user['student_id']]);
                $debts = $stmt->fetchAll(PDO::FETCH_ASSOC);
                
                $stats['personal_debts'] = $debts;
                foreach ($debts as $debt) {
                    if ($debt['status'] !== 'Paid') {
                        $stats['personal_total_debt'] += ($debt['total_amount'] - $debt['paid_amount']);
                    }
                }
            }

            $this->send(200, true, "Thành công", $stats);
        } catch (Exception $e) {
            $this->send(500, false, "Lỗi khi lấy thống kê: " . $e->getMessage());
        }
    }
}