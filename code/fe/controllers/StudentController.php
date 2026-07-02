<?php
require_once __DIR__ . '/../services/ApiService.php';

class StudentController
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
        $search = trim($_GET['search'] ?? '');
        $class_id = $_GET['class_id'] ?? '';
        $page = isset($_GET['page']) ? (int) $_GET['page'] : 1;
        $queryString = "/api/students?search=" . urlencode($search) . "&class_id=" . urlencode(is_array($class_id)?'':$class_id) . "&page=" . $page;

        try {
            $apiRes = ApiService::get($queryString);
            if ($apiRes['http_code'] === 200 && $apiRes['response']['success']) {
                $data = $apiRes['response']['data'];
                $students = $data['students'];
                $classes = $data['classes'];
                $paginationData = $data['pagination'];
                $pagination = paginate($paginationData['total'], $paginationData['per_page'], $paginationData['current_page']);
                require_once __DIR__ . '/../views/students/index.php';
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

    public function create()
    {
        $this->checkLogin();
        try {
            $apiRes = ApiService::get('/api/students/create');
            if ($apiRes['http_code'] === 200) {
                $classes = $apiRes['response']['data']['classes'];
                require_once __DIR__ . '/../views/students/create.php';
            } else {
                set_flash('error', "Không thể tải trang: " . $apiRes['response']['message'], 'danger');
                header("Location: index.php?controller=student&action=index");
                exit;
            }
        } catch (Exception $e) {
            set_flash('error', "Lỗi API: " . $e->getMessage(), 'danger');
            header("Location: index.php?controller=student&action=index");
            exit;
        }
    }

    public function store()
    {
        $this->checkLogin();
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            header('Location: index.php?controller=student&action=index');
            exit();
        }

        try {
            // Forward entire $_POST to API proxy
            $apiRes = ApiService::post('/api/students', $_POST);
            if ($apiRes['http_code'] === 200 && $apiRes['response']['success'] === true) {
                set_flash('success', $apiRes['response']['message'], 'success');
                header("Location: index.php?controller=student&action=index");
                exit;
            } else {
                $msg = $apiRes['response']['message'] ?? 'Lỗi không xác định';
                set_flash('error', $msg, 'danger');
                $_SESSION['old'] = $_POST;
                if (isset($apiRes['response']['data']['errors'])) {
                    $_SESSION['errors'] = $apiRes['response']['data']['errors'];
                }
                header("Location: index.php?controller=student&action=create");
                exit;
            }
        } catch (Exception $e) {
            set_flash('error', "Lỗi API: " . $e->getMessage(), 'danger');
            header("Location: index.php?controller=student&action=create");
            exit;
        }
    }

    public function edit()
    {
        $this->checkLogin();
        $id = $_GET['id'] ?? 0;
        try {
            $apiRes = ApiService::get("/api/students/$id/edit");
            if ($apiRes['http_code'] === 200 && $apiRes['response']['success']) {
                $student = $apiRes['response']['data']['student'];
                $classes = $apiRes['response']['data']['classes'];
                require_once __DIR__ . '/../views/students/edit.php';
            } else {
                set_flash('error', $apiRes['response']['message'] ?? 'Không tìm thấy học sinh', 'danger');
                header("Location: index.php?controller=student&action=index");
                exit;
            }
        } catch (Exception $e) {
            set_flash('error', "Lỗi API: " . $e->getMessage(), 'danger');
            header("Location: index.php?controller=student&action=index");
            exit;
        }
    }

    public function update()
    {
        $this->checkLogin();
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            header('Location: index.php?controller=student&action=index');
            exit();
        }

        try {
            $apiRes = ApiService::put("/api/students/" . $_POST['id'], $_POST);
            if ($apiRes['http_code'] === 200 && $apiRes['response']['success'] === true) {
                set_flash('success', $apiRes['response']['message'], 'success');
                header("Location: index.php?controller=student&action=view&id=" . $_POST['id']);
                exit;
            } else {
                $msg = $apiRes['response']['message'] ?? 'Lỗi không xác định';
                set_flash('error', $msg, 'danger');
                $_SESSION['old'] = $_POST;
                if (isset($apiRes['response']['data']['errors'])) {
                    $_SESSION['errors'] = $apiRes['response']['data']['errors'];
                }
                header("Location: index.php?controller=student&action=edit&id=" . $_POST['id']);
                exit;
            }
        } catch (Exception $e) {
            set_flash('error', "Lỗi API: " . $e->getMessage(), 'danger');
            header("Location: index.php?controller=student&action=edit&id=" . $_POST['id']);
            exit;
        }
    }

    public function delete()
    {
        $this->checkLogin();
        $id = $_GET['id'] ?? 0;
        try {
            $apiRes = ApiService::delete("/api/students/$id");
            if ($apiRes['http_code'] === 200 && $apiRes['response']['success']) {
                set_flash('success', $apiRes['response']['message'], 'success');
            } else {
                set_flash('error', $apiRes['response']['message'] ?? 'Xoá thất bại', 'danger');
            }
        } catch (Exception $e) {
            set_flash('error', "Lỗi API: " . $e->getMessage(), 'danger');
        }
        header("Location: index.php?controller=student&action=index");
        exit;
    }

    public function view()
    {
        $this->checkLogin();
        $id = $_GET['id'] ?? 0;
        try {
            $apiRes = ApiService::get("/api/students/$id");
            if ($apiRes['http_code'] === 200 && $apiRes['response']['success']) {
                $student = $apiRes['response']['data']['student'];
                $debts = $apiRes['response']['data']['debts'];
                $payments = $apiRes['response']['data']['payments'];
                $studentExemptions = $apiRes['response']['data']['studentExemptions'];
                $allExemptions = $apiRes['response']['data']['allExemptions'];
                require_once __DIR__ . '/../views/students/view.php';
            } else {
                set_flash('error', $apiRes['response']['message'] ?? 'Không tìm thấy học sinh', 'danger');
                header("Location: index.php?controller=student&action=index");
                exit;
            }
        } catch (Exception $e) {
            set_flash('error', "Lỗi API: " . $e->getMessage(), 'danger');
            header("Location: index.php?controller=student&action=index");
            exit;
        }
    }

    public function update_note()
    {
        $this->checkLogin();
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            header('Location: index.php?controller=student&action=index');
            exit();
        }

        try {
            $apiRes = ApiService::post('/api/students/' . $_POST['id'] . '/note', [
                'id' => $_POST['id'],
                'notes' => $_POST['notes']
            ]);
            if ($apiRes['http_code'] === 200 && $apiRes['response']['success']) {
                set_flash('success', $apiRes['response']['message']);
            } else {
                set_flash('error', $apiRes['response']['message'] ?? 'Cập nhật thất bại');
            }
        } catch (Exception $e) {
            set_flash('error', "Lỗi API: " . $e->getMessage(), 'danger');
        }
        header("Location: index.php?controller=student&action=view&id=" . $_POST['id']);
        exit;
    }

    public function export()
    {
        $this->checkLogin();
        $search = $_GET['search'] ?? '';
        $class_id = $_GET['class_id'] ?? '';
        
        $queryString = "/api/students/export?search=" . urlencode($search) . "&class_id=" . urlencode(is_array($class_id)?'':$class_id);

        try {
            $apiRes = ApiService::get($queryString);
            if ($apiRes['http_code'] === 200 && $apiRes['response']['success']) {
                $students = $apiRes['response']['data']['students'];
                
                $filename = 'DanhSachHocSinh_' . date('YmdHis') . '.csv';

                if (ob_get_level()) ob_end_clean();

                header('Content-Type: text/csv; charset=utf-8');
                header('Content-Disposition: attachment; filename=' . $filename);

                $output = fopen('php://output', 'w');
                fputs($output, "\xEF\xBB\xBF"); 
                fputcsv($output, ['STT', 'Mã HS', 'Họ tên', 'Ngày sinh', 'Giới tính', 'Lớp', 'Phụ huynh', 'SĐT', 'Trạng thái']);

                $stt = 1;
                foreach ($students as $s) {
                    fputcsv($output, [
                        $stt++,
                        $s['student_code'],
                        $s['full_name'],
                        format_date($s['date_of_birth']),
                        $s['gender'] == 'Male' ? 'Nam' : 'Nữ',
                        $s['class_name'],
                        $s['parent_name'],
                        $s['parent_phone'],
                        $s['is_active'] ? 'Đang học' : 'Nghỉ học'
                    ]);
                }
                fclose($output);
                exit();
            } else {
                set_flash('error', "Không thể xuất dữ liệu: " . ($apiRes['response']['message']), 'danger');
                header("Location: index.php?controller=student&action=index");
                exit;
            }
        } catch (Exception $e) {
            set_flash('error', "Lỗi kết nối API: " . $e->getMessage(), 'danger');
            header("Location: index.php?controller=student&action=index");
            exit;
        }
    }

    public function import()
    {
        $this->checkLogin();
        require_once __DIR__ . '/../views/students/import.php';
    }

    public function processImport()
    {
        $this->checkLogin();
        if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_FILES['file'])) {
            try {
                $file = $_FILES['file'];
                $apiRes = ApiService::postFile(
                    "/api/students/import",
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
        header("Location: index.php?controller=student&action=import");
        exit;
    }
}
