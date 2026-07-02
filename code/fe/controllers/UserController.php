<?php
require_once __DIR__ . '/../services/ApiService.php';

class UserController
{
    private function checkLogin() {
        if (!isset($_SESSION['user_id'])) {
            header("Location: index.php?action=login");
            exit;
        }
    }

    public function index()
    {
        $this->checkLogin();
        $search = $_GET['search'] ?? '';
        $page = isset($_GET['page']) ? (int) $_GET['page'] : 1;

        try {
            $apiRes = ApiService::get("/api/users?search=" . urlencode($search) . "&page=$page");
            if ($apiRes['http_code'] === 200 && $apiRes['response']['success']) {
                $data = $apiRes['response']['data'];
                $users = $data['users'];
                $paginationData = $data['pagination'];
                $pagination = paginate($paginationData['total'], $paginationData['per_page'], $paginationData['current_page']);
                
                require_once __DIR__ . '/../views/users/index.php';
            } else {
                set_flash('error', "Không thể lấy dữ liệu", 'danger');
                header("Location: index.php?action=dashboard");
                exit;
            }
        } catch (Exception $e) {
            set_flash('error', "Lỗi kết nối API: " . $e->getMessage(), 'danger');
            header("Location: index.php?action=dashboard");
            exit;
        }
    }

    public function create()
    {
        $this->checkLogin();
        try {
            $apiRes = ApiService::get("/api/users/create");
            if ($apiRes['http_code'] === 200 && $apiRes['response']['success']) {
                $roles = $apiRes['response']['data']['roles'];
                $students = $apiRes['response']['data']['students'];
                $classes = $apiRes['response']['data']['classes'];
                require_once __DIR__ . '/../views/users/create.php';
            } else {
                set_flash('error', 'Lỗi tải trang', 'danger');
                header("Location: index.php?controller=user&action=index");
                exit;
            }
        } catch (Exception $e) {
            set_flash('error', "Lỗi API: " . $e->getMessage(), 'danger');
            header("Location: index.php?controller=user&action=index");
            exit;
        }
    }

    public function store()
    {
        $this->checkLogin();
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            try {
                $apiRes = ApiService::post("/api/users", $_POST);
                if ($apiRes['http_code'] === 200 && $apiRes['response']['success']) {
                    set_flash('success', $apiRes['response']['message']);
                    header("Location: index.php?controller=user&action=index");
                    exit;
                } else {
                    set_flash('error', $apiRes['response']['message'] ?? 'Có lỗi xảy ra!');
                    $_SESSION['old'] = $_POST;
                    if (isset($apiRes['response']['data']['errors'])) {
                        $_SESSION['errors'] = $apiRes['response']['data']['errors'];
                    }
                    header("Location: index.php?controller=user&action=create");
                    exit;
                }
            } catch (Exception $e) {
                set_flash('error', "Lỗi API: " . $e->getMessage(), 'danger');
                header("Location: index.php?controller=user&action=create");
                exit;
            }
        }
    }

    public function edit()
    {
        $this->checkLogin();
        $id = $_GET['id'] ?? null;
        if (!$id) {
            header("Location: index.php?controller=user&action=index");
            exit;
        }
        
        try {
            $apiRes = ApiService::get("/api/users/$id/edit");
            if ($apiRes['http_code'] === 200 && $apiRes['response']['success']) {
                $user = $apiRes['response']['data']['user'];
                $roles = $apiRes['response']['data']['roles'];
                $students = $apiRes['response']['data']['students'];
                $classes = $apiRes['response']['data']['classes'];
                $current_class_id = $apiRes['response']['data']['current_class_id'];
                require_once __DIR__ . '/../views/users/edit.php';
            } else {
                set_flash('error', $apiRes['response']['message'] ?? 'Không tìm thấy user');
                header("Location: index.php?controller=user&action=index");
                exit;
            }
        } catch (Exception $e) {
            set_flash('error', "Lỗi API: " . $e->getMessage(), 'danger');
            header("Location: index.php?controller=user&action=index");
            exit;
        }
    }

    public function update()
    {
        $this->checkLogin();
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            try {
                $apiRes = ApiService::put("/api/users/" . $_POST['id'], $_POST);
                if ($apiRes['http_code'] === 200 && $apiRes['response']['success']) {
                    set_flash('success', $apiRes['response']['message']);
                    header("Location: index.php?controller=user&action=index");
                    exit;
                } else {
                    set_flash('error', $apiRes['response']['message'] ?? 'Có lỗi xảy ra!');
                    $_SESSION['old'] = $_POST;
                    if (isset($apiRes['response']['data']['errors'])) {
                        $_SESSION['errors'] = $apiRes['response']['data']['errors'];
                    }
                    header("Location: index.php?controller=user&action=edit&id=" . $_POST['id']);
                    exit;
                }
            } catch (Exception $e) {
                set_flash('error', "Lỗi API: " . $e->getMessage(), 'danger');
                header("Location: index.php?controller=user&action=edit&id=" . $_POST['id']);
                exit;
            }
        }
    }

    public function delete()
    {
        $this->checkLogin();
        $id = $_GET['id'] ?? null;
        try {
            $apiRes = ApiService::delete("/api/users/$id");
            if ($apiRes['http_code'] === 200 && $apiRes['response']['success']) {
                set_flash('success', $apiRes['response']['message']);
            } else {
                set_flash('error', $apiRes['response']['message'] ?? 'Có lỗi xảy ra!');
            }
        } catch (Exception $e) {
            set_flash('error', "Lỗi kết nối: " . $e->getMessage());
        }
        header("Location: index.php?controller=user&action=index");
        exit;
    }

    public function profile()
    {
        $this->checkLogin();
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            try {
                $apiRes = ApiService::put("/api/profile", $_POST);
                if ($apiRes['http_code'] === 200 && $apiRes['response']['success']) {
                    set_flash('success', $apiRes['response']['message']);
                    // Update user session explicitly since profile changed
                    $_SESSION['full_name'] = $_POST['full_name'] ?? $_SESSION['full_name'];
                } else {
                    set_flash('error', $apiRes['response']['message'] ?? 'Thất bại');
                }
            } catch (Exception $e) {
                set_flash('error', "Lỗi API: " . $e->getMessage(), 'danger');
            }
            header("Location: index.php?controller=user&action=profile");
            exit;
        }

        try {
            $apiRes = ApiService::get("/api/profile");
            if ($apiRes['http_code'] === 200 && $apiRes['response']['success']) {
                $user = $apiRes['response']['data']['user'];
                $student = $apiRes['response']['data']['student'];
                require_once __DIR__ . '/../views/users/profile.php';
            } else {
                set_flash('error', 'Lỗi tải trang', 'danger');
                header("Location: index.php?action=dashboard");
                exit;
            }
        } catch (Exception $e) {
            set_flash('error', "Lỗi API: " . $e->getMessage(), 'danger');
            header("Location: index.php?action=dashboard");
            exit;
        }
    }

    public function changePassword()
    {
        $this->checkLogin();
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            try {
                $apiRes = ApiService::post("/api/change-password", $_POST);
                if ($apiRes['http_code'] === 200 && $apiRes['response']['success']) {
                    set_flash('success', $apiRes['response']['message']);
                } else {
                    set_flash('error', $apiRes['response']['message'] ?? 'Thất bại');
                }
            } catch (Exception $e) {
                set_flash('error', "Lỗi API: " . $e->getMessage(), 'danger');
            }
            header("Location: index.php?controller=user&action=changePassword");
            exit;
        }

        require_once __DIR__ . '/../views/users/change_password.php';
    }

    public function resetPassword()
    {
        $this->checkLogin();
        $id = $_GET['id'] ?? 0;

        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            try {
                $_POST['id'] = $id;
                $apiRes = ApiService::post("/api/users/$id/reset-password", $_POST);
                if ($apiRes['http_code'] === 200 && $apiRes['response']['success']) {
                    set_flash('success', $apiRes['response']['message']);
                    header("Location: index.php?controller=user&action=index");
                    exit;
                } else {
                    set_flash('error', $apiRes['response']['message'] ?? 'Thất bại');
                }
            } catch (Exception $e) {
                set_flash('error', "Lỗi API: " . $e->getMessage(), 'danger');
            }
            header("Location: index.php?controller=user&action=reset_password&id=$id");
            exit;
        }

        try {
            $apiRes = ApiService::get("/api/users/$id/edit");
            if ($apiRes['http_code'] === 200 && $apiRes['response']['success']) {
                $user = $apiRes['response']['data']['user'];
                require_once __DIR__ . '/../views/users/reset_password.php';
            } else {
                set_flash('error', 'Lỗi tải trang', 'danger');
                header("Location: index.php?controller=user&action=index");
                exit;
            }
        } catch (Exception $e) {
            set_flash('error', "Lỗi API: " . $e->getMessage(), 'danger');
            header("Location: index.php?controller=user&action=index");
            exit;
        }
    }
}
