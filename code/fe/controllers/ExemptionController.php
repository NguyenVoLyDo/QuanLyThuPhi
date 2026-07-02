<?php
require_once __DIR__ . '/../services/ApiService.php';

class ExemptionController
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
            $apiRes = ApiService::get("/api/exemptions?search=" . urlencode($search) . "&page=$page");
            if ($apiRes['http_code'] === 200 && $apiRes['response']['success']) {
                $data = $apiRes['response']['data'];
                $exemptions = $data['exemptions'];
                $paginationData = $data['pagination'];
                $pagination = paginate($paginationData['total'], $paginationData['per_page'], $paginationData['current_page']);
                
                require_once __DIR__ . '/../views/exemptions/index.php';
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
        require_once __DIR__ . '/../views/exemptions/create.php';
    }

    public function store()
    {
        $this->checkLogin();
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            try {
                $apiRes = ApiService::post("/api/exemptions", $_POST);
                if ($apiRes['http_code'] === 200 && $apiRes['response']['success']) {
                    set_flash('success', $apiRes['response']['message']);
                    header("Location: index.php?controller=exemption&action=index");
                    exit;
                } else {
                    set_flash('error', $apiRes['response']['message'] ?? 'Có lỗi xảy ra!');
                    header("Location: index.php?controller=exemption&action=create");
                    exit;
                }
            } catch (Exception $e) {
                set_flash('error', "Lỗi API: " . $e->getMessage(), 'danger');
                header("Location: index.php?controller=exemption&action=create");
                exit;
            }
        }
    }

    public function edit()
    {
        $this->checkLogin();
        $id = $_GET['id'] ?? null;
        if (!$id) {
            header("Location: index.php?controller=exemption&action=index");
            exit;
        }
        
        try {
            $apiRes = ApiService::get("/api/exemptions/$id/edit");
            if ($apiRes['http_code'] === 200 && $apiRes['response']['success']) {
                $exemption = $apiRes['response']['data']['exemption'];
                require_once __DIR__ . '/../views/exemptions/edit.php';
            } else {
                set_flash('error', $apiRes['response']['message'] ?? 'Không tìm thấy chính sách');
                header("Location: index.php?controller=exemption&action=index");
                exit;
            }
        } catch (Exception $e) {
            set_flash('error', "Lỗi API: " . $e->getMessage(), 'danger');
            header("Location: index.php?controller=exemption&action=index");
            exit;
        }
    }

    public function update()
    {
        $this->checkLogin();
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            try {
                $apiRes = ApiService::put("/api/exemptions/" . $_POST['id'], $_POST);
                if ($apiRes['http_code'] === 200 && $apiRes['response']['success']) {
                    set_flash('success', $apiRes['response']['message']);
                    header("Location: index.php?controller=exemption&action=index");
                    exit;
                } else {
                    set_flash('error', $apiRes['response']['message'] ?? 'Có lỗi xảy ra!');
                    header("Location: index.php?controller=exemption&action=edit&id=" . $_POST['id']);
                    exit;
                }
            } catch (Exception $e) {
                set_flash('error', "Lỗi API: " . $e->getMessage(), 'danger');
                header("Location: index.php?controller=exemption&action=edit&id=" . $_POST['id']);
                exit;
            }
        }
    }

    public function delete()
    {
        $this->checkLogin();
        $id = $_GET['id'] ?? null;
        try {
            $apiRes = ApiService::delete("/api/exemptions/$id");
            if ($apiRes['http_code'] === 200 && $apiRes['response']['success']) {
                set_flash('success', $apiRes['response']['message']);
            } else {
                set_flash('error', $apiRes['response']['message'] ?? 'Có lỗi xảy ra!');
            }
        } catch (Exception $e) {
            set_flash('error', "Lỗi kết nối: " . $e->getMessage());
        }
        header("Location: index.php?controller=exemption&action=index");
        exit;
    }

    public function assign()
    {
        $this->checkLogin();
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            try {
                $apiRes = ApiService::post("/api/exemptions/assign", $_POST);
                if ($apiRes['http_code'] === 200 && $apiRes['response']['success']) {
                    set_flash('success', $apiRes['response']['message']);
                } else {
                    set_flash('error', $apiRes['response']['message'] ?? 'Có lỗi xảy ra!');
                }
            } catch (Exception $e) {
                set_flash('error', "Lỗi kết nối: " . $e->getMessage());
            }
            header('Location: index.php?controller=student&action=view&id=' . $_POST['student_id']);
            exit;
        }
    }

    public function revoke()
    {
        $this->checkLogin();
        $student_id = $_GET['student_id'] ?? 0;
        $exemption_id = $_GET['exemption_id'] ?? 0;

        try {
            $apiRes = ApiService::post("/api/exemptions/revoke", ['student_id' => $student_id, 'exemption_id' => $exemption_id]);
            if ($apiRes['http_code'] === 200 && $apiRes['response']['success']) {
                set_flash('success', $apiRes['response']['message']);
            } else {
                set_flash('error', $apiRes['response']['message'] ?? 'Có lỗi xảy ra!');
            }
        } catch (Exception $e) {
            set_flash('error', "Lỗi kết nối: " . $e->getMessage());
        }
        header('Location: index.php?controller=student&action=view&id=' . $student_id);
        exit;
    }
}
