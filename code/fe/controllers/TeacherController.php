<?php
require_once __DIR__ . '/../services/ApiService.php';

class TeacherController
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
            $apiRes = ApiService::get("/api/teachers?search=" . urlencode($search) . "&page=$page");
            if ($apiRes['http_code'] === 200 && $apiRes['response']['success']) {
                $data = $apiRes['response']['data'];
                $teachers = $data['teachers'];
                $paginationData = $data['pagination'];
                $pagination = paginate($paginationData['total'], $paginationData['per_page'], $paginationData['current_page']);
                
                require_once __DIR__ . '/../views/teachers/index.php';
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
            $apiRes = ApiService::get("/api/teachers/create");
            if ($apiRes['http_code'] === 200 && $apiRes['response']['success']) {
                $classes = $apiRes['response']['data']['classes'];
                require_once __DIR__ . '/../views/teachers/create.php';
            } else {
                set_flash('error', 'Lỗi tải trang', 'danger');
                header("Location: index.php?controller=teacher&action=index");
                exit;
            }
        } catch (Exception $e) {
            set_flash('error', "Lỗi API: " . $e->getMessage(), 'danger');
            header("Location: index.php?controller=teacher&action=index");
            exit;
        }
    }

    public function store()
    {
        $this->checkLogin();
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            try {
                $apiRes = ApiService::post("/api/teachers", $_POST);
                if ($apiRes['http_code'] === 200 && $apiRes['response']['success']) {
                    set_flash('success', $apiRes['response']['message']);
                    header("Location: index.php?controller=teacher&action=index");
                    exit;
                } else {
                    set_flash('error', $apiRes['response']['message'] ?? 'Có lỗi xảy ra!');
                    $_SESSION['old'] = $_POST;
                    header("Location: index.php?controller=teacher&action=create");
                    exit;
                }
            } catch (Exception $e) {
                set_flash('error', "Lỗi API: " . $e->getMessage(), 'danger');
                header("Location: index.php?controller=teacher&action=create");
                exit;
            }
        }
    }

    public function edit()
    {
        $this->checkLogin();
        $id = $_GET['id'] ?? null;
        if (!$id) {
            header("Location: index.php?controller=teacher&action=index");
            exit;
        }
        
        try {
            $apiRes = ApiService::get("/api/teachers/$id/edit");
            if ($apiRes['http_code'] === 200 && $apiRes['response']['success']) {
                $teacher = $apiRes['response']['data']['teacher'];
                $classes = $apiRes['response']['data']['classes'];
                $assigned_classes = $apiRes['response']['data']['assigned_classes'];
                require_once __DIR__ . '/../views/teachers/edit.php';
            } else {
                set_flash('error', $apiRes['response']['message'] ?? 'Không tìm thấy giáo viên');
                header("Location: index.php?controller=teacher&action=index");
                exit;
            }
        } catch (Exception $e) {
            set_flash('error', "Lỗi API: " . $e->getMessage(), 'danger');
            header("Location: index.php?controller=teacher&action=index");
            exit;
        }
    }

    public function update()
    {
        $this->checkLogin();
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            try {
                $apiRes = ApiService::put("/api/teachers/" . $_POST['id'], $_POST);
                if ($apiRes['http_code'] === 200 && $apiRes['response']['success']) {
                    set_flash('success', $apiRes['response']['message']);
                    header("Location: index.php?controller=teacher&action=index");
                    exit;
                } else {
                    set_flash('error', $apiRes['response']['message'] ?? 'Có lỗi xảy ra!');
                    $_SESSION['old'] = $_POST;
                    header("Location: index.php?controller=teacher&action=edit&id=" . $_POST['id']);
                    exit;
                }
            } catch (Exception $e) {
                set_flash('error', "Lỗi API: " . $e->getMessage(), 'danger');
                header("Location: index.php?controller=teacher&action=edit&id=" . $_POST['id']);
                exit;
            }
        }
    }

    public function delete()
    {
        $this->checkLogin();
        $id = $_GET['id'] ?? null;
        try {
            $apiRes = ApiService::delete("/api/teachers/$id");
            if ($apiRes['http_code'] === 200 && $apiRes['response']['success']) {
                set_flash('success', $apiRes['response']['message']);
            } else {
                set_flash('error', $apiRes['response']['message'] ?? 'Có lỗi xảy ra!');
            }
        } catch (Exception $e) {
            set_flash('error', "Lỗi kết nối: " . $e->getMessage());
        }
        header("Location: index.php?controller=teacher&action=index");
        exit;
    }
}
