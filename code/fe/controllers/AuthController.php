<?php
require_once __DIR__ . '/../services/ApiService.php';

class AuthController
{
    public function showLogin()
    {
        if (isset($_SESSION['user_id'])) {
            header("Location: index.php?action=dashboard");
            exit;
        }
        require_once __DIR__ . '/../views/auth/login.php';
    }

    public function login()
    {
        if (isset($_SESSION['user_id'])) {
            header("Location: index.php?action=dashboard");
            exit;
        }

        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $username = trim($_POST['username'] ?? '');
            $password = $_POST['password'] ?? '';
            
            try {
                $apiRes = ApiService::post('/api/auth/login', [
                    'username' => $username,
                    'password' => $password
                ]);

                if ($apiRes['http_code'] === 200 && isset($apiRes['response']['success']) && $apiRes['response']['success'] === true) {
                    $userData = $apiRes['response']['data']['user'];
                    $_SESSION['api_token'] = $apiRes['response']['data']['token'];
                    $_SESSION['user_id'] = $userData['user_id'];
                    $_SESSION['username'] = $userData['username'];
                    $_SESSION['role_id'] = $userData['role_id'];
                    $_SESSION['role'] = $userData['role_name'];
                    $_SESSION['role_name'] = $userData['role_name'];
                    $_SESSION['full_name'] = $userData['full_name'];
                    if (!empty($userData['student_id'])) {
                        $_SESSION['student_id'] = $userData['student_id'];
                    }
                    if (!empty($userData['teacher_class_ids'])) {
                        $_SESSION['teacher_class_ids'] = $userData['teacher_class_ids'];
                        $_SESSION['teacher_class_id'] = $userData['teacher_class_ids'][0] ?? null;
                    }

                    header("Location: index.php?action=dashboard");
                    exit;
                } else {
                    $error = $apiRes['response']['message'] ?? "Lỗi đăng nhập từ máy chủ (HTTP: {$apiRes['http_code']})";
                    require_once __DIR__ . '/../views/auth/login.php';
                }
            } catch (Exception $e) {
                $error = "Lỗi kết nối API: " . $e->getMessage();
                require_once __DIR__ . '/../views/auth/login.php';
            }
        } else {
            $this->showLogin();
        }
    }

    public function dashboard()
    {
        if (!isset($_SESSION['user_id'])) {
            header("Location: index.php?action=login");
            exit;
        }

        // Ideally fetch stats via ApiService
        $stats = [
            'total_students' => 0,
            'total_fee_types' => 0,
            'total_revenue' => 0,
            'unpaid_by_semester' => [],
            'total_payments' => 0,
            'personal_total_debt' => 0,
            'personal_debts' => []
        ];

        try {
            $apiRes = ApiService::get('/api/dashboard/stats');
            if ($apiRes['http_code'] === 200 && isset($apiRes['response']['success']) && $apiRes['response']['success'] === true) {
                $stats = $apiRes['response']['data'];
            }
        } catch (Exception $e) {
            // Log error if needed, defaults are already set to 0
        }

        require_once __DIR__ . '/../views/dashboard.php';
    }

    public function logout()
    {
        session_destroy();
        header("Location: index.php?action=login");
        exit;
    }
}
