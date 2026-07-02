<?php
require_once __DIR__ . '/../services/ApiService.php';

class FeeTypeController
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
        $category = $_GET['category'] ?? '';
        $year = $_GET['year'] ?? '';
        $page = isset($_GET['page']) ? (int) $_GET['page'] : 1;

        $queryString = "/api/fee-types?search=" . urlencode($search) . "&category=" . urlencode($category) . "&year=" . urlencode($year) . "&page=" . $page;

        try {
            $apiRes = ApiService::get($queryString);
            if ($apiRes['http_code'] === 200 && $apiRes['response']['success']) {
                $data = $apiRes['response']['data'];
                $fee_types = $data['fee_types'];
                $categories = $data['categories'];
                $paginationData = $data['pagination'];
                $pagination = paginate($paginationData['total'], $paginationData['per_page'], $paginationData['current_page']);
                
                require_once __DIR__ . '/../views/fee_types/index.php';
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
            $apiRes = ApiService::get('/api/fee-types/create');
            if ($apiRes['http_code'] === 200) {
                $categories = $apiRes['response']['data']['categories'];
                require_once __DIR__ . '/../views/fee_types/create.php';
            } else {
                set_flash('error', "Không thể tải trang", 'danger');
                header("Location: index.php?controller=feetype&action=index");
                exit;
            }
        } catch (Exception $e) {
            set_flash('error', "Lỗi API: " . $e->getMessage(), 'danger');
            header("Location: index.php?controller=feetype&action=index");
            exit;
        }
    }

    public function store()
    {
        $this->checkLogin();
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            header('Location: index.php?controller=feetype&action=index');
            exit();
        }

        try {
            $apiRes = ApiService::post('/api/fee-types', $_POST);
            if ($apiRes['http_code'] === 200 && $apiRes['response']['success']) {
                set_flash('success', $apiRes['response']['message'], 'success');
                header("Location: index.php?controller=feetype&action=index");
                exit;
            } else {
                $msg = $apiRes['response']['message'] ?? 'Lỗi không xác định';
                set_flash('error', $msg, 'danger');
                $_SESSION['old'] = $_POST;
                if (isset($apiRes['response']['data']['errors'])) {
                    $_SESSION['errors'] = $apiRes['response']['data']['errors'];
                }
                header("Location: index.php?controller=feetype&action=create");
                exit;
            }
        } catch (Exception $e) {
            set_flash('error', "Lỗi API: " . $e->getMessage(), 'danger');
            header("Location: index.php?controller=feetype&action=create");
            exit;
        }
    }

    public function edit()
    {
        $this->checkLogin();
        $id = $_GET['id'] ?? 0;
        try {
            $apiRes = ApiService::get("/api/fee-types/$id/edit");
            if ($apiRes['http_code'] === 200 && $apiRes['response']['success']) {
                $fee_type = $apiRes['response']['data']['fee_type'];
                $categories = $apiRes['response']['data']['categories'];
                require_once __DIR__ . '/../views/fee_types/edit.php';
            } else {
                set_flash('error', 'Không tìm thấy khoản thu!', 'danger');
                header("Location: index.php?controller=feetype&action=index");
                exit;
            }
        } catch (Exception $e) {
            set_flash('error', "Lỗi API: " . $e->getMessage(), 'danger');
            header("Location: index.php?controller=feetype&action=index");
            exit;
        }
    }

    public function update()
    {
        $this->checkLogin();
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            header('Location: index.php?controller=feetype&action=index');
            exit();
        }

        try {
            $apiRes = ApiService::put("/api/fee-types/" . $_POST['id'], $_POST);
            if ($apiRes['http_code'] === 200 && $apiRes['response']['success']) {
                set_flash('success', $apiRes['response']['message'], 'success');
                header("Location: index.php?controller=feetype&action=index");
                exit;
            } else {
                $msg = $apiRes['response']['message'] ?? 'Lỗi không xác định';
                set_flash('error', $msg, 'danger');
                $_SESSION['old'] = $_POST;
                if (isset($apiRes['response']['data']['errors'])) {
                    $_SESSION['errors'] = $apiRes['response']['data']['errors'];
                }
                header("Location: index.php?controller=feetype&action=edit&id=" . $_POST['id']);
                exit;
            }
        } catch (Exception $e) {
            set_flash('error', "Lỗi API: " . $e->getMessage(), 'danger');
            header("Location: index.php?controller=feetype&action=edit&id=" . $_POST['id']);
            exit;
        }
    }

    public function delete()
    {
        $this->checkLogin();
        $id = $_GET['id'] ?? 0;
        try {
            $apiRes = ApiService::delete("/api/fee-types/$id");
            if ($apiRes['http_code'] === 200 && $apiRes['response']['success']) {
                set_flash('success', $apiRes['response']['message'], 'success');
            } else {
                set_flash('error', $apiRes['response']['message'] ?? 'Xoá thất bại', 'danger');
            }
        } catch (Exception $e) {
            set_flash('error', "Lỗi API: " . $e->getMessage(), 'danger');
        }
        header("Location: index.php?controller=feetype&action=index");
        exit;
    }
    
    public function import()
    {
        $this->checkLogin();
        require_once __DIR__ . '/../views/fee_types/import.php';
    }

    public function processImport()
    {
        $this->checkLogin();
        if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_FILES['file'])) {
            try {
                $file = $_FILES['file'];
                $apiRes = ApiService::postFile(
                    "/api/fee-types/import",
                    [],
                    'file',
                    $file['tmp_name'],
                    $file['type'],
                    $file['name']
                );

                if ($apiRes['http_code'] === 200 && $apiRes['response']['success']) {
                    set_flash('success', $apiRes['response']['message']);
                    if (!empty($apiRes['response']['data']['errors'])) {
                        $_SESSION['import_errors'] = $apiRes['response']['data']['errors'];
                    }
                } else {
                    set_flash('error', $apiRes['response']['message'] ?? 'Import thất bại');
                }
            } catch (Exception $e) {
                set_flash('error', "Lỗi API: " . $e->getMessage());
            }
        }
        header("Location: index.php?controller=feetype&action=import");
        exit;
    }
}
