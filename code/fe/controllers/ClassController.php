<?php
require_once __DIR__ . '/../services/ApiService.php';

class ClassController {
    private function checkLogin() {
        if (!isset($_SESSION['user_id'])) {
            header("Location: index.php?action=login");
            exit;
        }
    }
    
    public function index() {
        $this->checkLogin();
        $search = $_GET['search'] ?? '';

        try {
            $apiRes = ApiService::get("/api/classes?search=" . urlencode($search));
            if ($apiRes['http_code'] === 200 && $apiRes['response']['success']) {
                $classes = $apiRes['response']['data']['classes'];
                require_once __DIR__ . '/../views/classes/index.php';
            } else {
                set_flash('error', "Không thể lấy dữ liệu: " . ($apiRes['response']['message'] ?? 'Unknown Error'), 'danger');
                header("Location: index.php?action=dashboard");
                exit;
            }
        } catch (Exception $e) {
            set_flash('error', "Lỗi kết nối API: " . $e->getMessage(), 'danger');
            header("Location: index.php?action=dashboard");
            exit;
        }
    }
    
    public function create() {
        $this->checkLogin();
        try {
            $apiRes = ApiService::get("/api/classes/create");
            if ($apiRes['http_code'] === 200 && $apiRes['response']['success']) {
                $teachers = $apiRes['response']['data']['teachers'];
                require_once __DIR__ . '/../views/classes/create.php';
            } else {
                set_flash('error', 'Lỗi tải trang', 'danger');
                header("Location: index.php?controller=class&action=index");
                exit;
            }
        } catch (Exception $e) {
            set_flash('error', "Lỗi API: " . $e->getMessage(), 'danger');
            header("Location: index.php?controller=class&action=index");
            exit;
        }
    }
    
    public function store() {
        $this->checkLogin();
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            try {
                $apiRes = ApiService::post("/api/classes", $_POST);
                if ($apiRes['http_code'] === 200 && $apiRes['response']['success']) {
                    set_flash('success', $apiRes['response']['message']);
                    header("Location: index.php?controller=class&action=index");
                    exit;
                } else {
                    set_flash('error', $apiRes['response']['message'] ?? 'Có lỗi xảy ra!');
                    header("Location: index.php?controller=class&action=create");
                    exit;
                }
            } catch (Exception $e) {
                set_flash('error', "Lỗi API: " . $e->getMessage(), 'danger');
                header("Location: index.php?controller=class&action=create");
                exit;
            }
        }
    }
    
    public function edit() {
        $this->checkLogin();
        $id = $_GET['id'] ?? null;
        if (!$id) {
            header("Location: index.php?controller=class&action=index");
            exit;
        }
        
        try {
            $apiRes = ApiService::get("/api/classes/$id/edit");
            if ($apiRes['http_code'] === 200 && $apiRes['response']['success']) {
                $class = $apiRes['response']['data']['class'];
                $teachers = $apiRes['response']['data']['teachers'];
                require_once __DIR__ . '/../views/classes/edit.php';
            } else {
                set_flash('error', $apiRes['response']['message'] ?? 'Không tìm thấy lớp học');
                header("Location: index.php?controller=class&action=index");
                exit;
            }
        } catch (Exception $e) {
            set_flash('error', "Lỗi API: " . $e->getMessage(), 'danger');
            header("Location: index.php?controller=class&action=index");
            exit;
        }
    }
    
    public function update() {
        $this->checkLogin();
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            try {
                $apiRes = ApiService::put("/api/classes/" . $_POST['id'], $_POST);
                if ($apiRes['http_code'] === 200 && $apiRes['response']['success']) {
                    set_flash('success', $apiRes['response']['message']);
                    header("Location: index.php?controller=class&action=index");
                    exit;
                } else {
                    set_flash('error', $apiRes['response']['message'] ?? 'Có lỗi xảy ra!');
                    header("Location: index.php?controller=class&action=edit&id=" . $_POST['id']);
                    exit;
                }
            } catch (Exception $e) {
                set_flash('error', "Lỗi API: " . $e->getMessage(), 'danger');
                header("Location: index.php?controller=class&action=edit&id=" . $_POST['id']);
                exit;
            }
        }
    }
    
    public function delete() {
        $this->checkLogin();
        $id = $_GET['id'] ?? null;
        try {
            $apiRes = ApiService::delete("/api/classes/$id");
            if ($apiRes['http_code'] === 200 && $apiRes['response']['success']) {
                set_flash('success', $apiRes['response']['message']);
            } else {
                set_flash('error', $apiRes['response']['message'] ?? 'Có lỗi xảy ra!');
            }
        } catch (Exception $e) {
            set_flash('error', "Lỗi kết nối: " . $e->getMessage());
        }
        header("Location: index.php?controller=class&action=index");
        exit;
    }
}
