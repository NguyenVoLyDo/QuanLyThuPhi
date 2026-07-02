<?php
require_once __DIR__ . '/../services/ApiService.php';

class PaymentController
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

        $allowedParams = ['search', 'from_date', 'to_date', 'class_id', 'fee_type_id', 'page', 'student_id'];
        $queryArr = [];
        foreach ($allowedParams as $param) {
            if (isset($_GET[$param])) {
                $queryArr[] = $param . '=' . urlencode($_GET[$param]);
            }
        }
        $queryString = "/api/payments?" . implode('&', $queryArr);

        try {
            $apiRes = ApiService::get($queryString);
            if ($apiRes['http_code'] === 200 && $apiRes['response']['success']) {
                $data = $apiRes['response']['data'];
                $payments = $data['payments'];
                $classes = $data['classes'];
                $feeTypes = $data['feeTypes'];
                $paginationData = $data['pagination'];
                $pagination = paginate($paginationData['total'], $paginationData['per_page'], $paginationData['current_page']);
                
                require_once __DIR__ . '/../views/payments/index.php';
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
        $student_id = $_GET['student_id'] ?? '';
        try {
            $apiRes = ApiService::get("/api/payments/create?student_id=$student_id");
            if ($apiRes['http_code'] === 200) {
                $students = $apiRes['response']['data']['students'];
                $fee_types = $apiRes['response']['data']['fee_types'];
                $payment_methods = $apiRes['response']['data']['payment_methods'];
                $student_debts = $apiRes['response']['data']['student_debts'];
                $selected_student_id = $apiRes['response']['data']['selected_student_id'];
                
                require_once __DIR__ . '/../views/payments/create.php';
            } else {
                set_flash('error', "Không thể tải trang: " . $apiRes['response']['message'], 'danger');
                header("Location: index.php?controller=payment&action=index");
                exit;
            }
        } catch (Exception $e) {
            set_flash('error', "Lỗi API: " . $e->getMessage(), 'danger');
            header("Location: index.php?controller=payment&action=index");
            exit;
        }
    }

    public function store()
    {
        $this->checkLogin();
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            header('Location: index.php?controller=payment&action=index');
            exit();
        }

        try {
            $apiRes = ApiService::post('/api/payments', $_POST);
            if ($apiRes['http_code'] === 200 && $apiRes['response']['success']) {
                set_flash('success', $apiRes['response']['message'], 'success');
                if (isset($apiRes['response']['data']['id'])) {
                    header("Location: index.php?controller=payment&action=receipt&id=" . $apiRes['response']['data']['id']);
                } elseif (isset($apiRes['response']['data']['student_id'])) {
                    header("Location: index.php?controller=student&action=view&id=" . $apiRes['response']['data']['student_id']);
                } else {
                    header("Location: index.php?controller=payment&action=index");
                }
                exit;
            } else {
                $msg = $apiRes['response']['message'] ?? 'Lỗi không xác định';
                set_flash('error', $msg, 'danger');
                $_SESSION['old'] = $_POST;
                if (isset($apiRes['response']['data']['errors'])) {
                    $_SESSION['errors'] = $apiRes['response']['data']['errors'];
                }
                $redirect_sid = $_POST['student_id'] ?? '';
                header("Location: index.php?controller=payment&action=create&student_id=$redirect_sid");
                exit;
            }
        } catch (Exception $e) {
            set_flash('error', "Lỗi API: " . $e->getMessage(), 'danger');
            header("Location: index.php?controller=payment&action=create");
            exit;
        }
    }

    public function receipt()
    {
        $this->checkLogin();
        $id = $_GET['id'] ?? 0;
        try {
            $apiRes = ApiService::get("/api/payments/$id");
            if ($apiRes['http_code'] === 200 && $apiRes['response']['success']) {
                $payment = $apiRes['response']['data']['payment'];
                require_once __DIR__ . '/../views/payments/receipt.php';
            } else {
                set_flash('error', $apiRes['response']['message'] ?? 'Không tìm thấy phiếu thu', 'danger');
                header("Location: index.php?controller=payment&action=index");
                exit;
            }
        } catch (Exception $e) {
            set_flash('error', "Lỗi API: " . $e->getMessage(), 'danger');
            header("Location: index.php?controller=payment&action=index");
            exit;
        }
    }

    public function view()
    {
        $this->checkLogin();
        $id = $_GET['id'] ?? 0;
        try {
            $apiRes = ApiService::get("/api/payments/$id");
            if ($apiRes['http_code'] === 200 && $apiRes['response']['success']) {
                $payment = $apiRes['response']['data']['payment'];
                require_once __DIR__ . '/../views/payments/view.php';
            } else {
                set_flash('error', $apiRes['response']['message'] ?? 'Không tìm thấy thanh toán', 'danger');
                header("Location: index.php?controller=payment&action=index");
                exit;
            }
        } catch (Exception $e) {
            set_flash('error', "Lỗi API: " . $e->getMessage(), 'danger');
            header("Location: index.php?controller=payment&action=index");
            exit;
        }
    }

    public function delete()
    {
        $this->checkLogin();
        $id = $_GET['id'] ?? 0;
        try {
            $apiRes = ApiService::delete("/api/payments/$id");
            if ($apiRes['http_code'] === 200 && $apiRes['response']['success']) {
                set_flash('success', $apiRes['response']['message'], 'success');
            } else {
                set_flash('error', $apiRes['response']['message'] ?? 'Xoá thất bại', 'danger');
            }
        } catch (Exception $e) {
            set_flash('error', "Lỗi API: " . $e->getMessage(), 'danger');
        }
        header("Location: index.php?controller=payment&action=index");
        exit;
    }

    public function refund()
    {
        $this->checkLogin();

        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            try {
                $apiRes = ApiService::post("/api/payments/" . $_POST['payment_id'] . "/refund", $_POST);
                if ($apiRes['http_code'] === 200 && $apiRes['response']['success']) {
                    set_flash('success', $apiRes['response']['message'], 'success');
                    header('Location: index.php?controller=payment&action=refunds');
                } else {
                    set_flash('error', $apiRes['response']['message'] ?? 'Lỗi không xác định', 'danger');
                    header('Location: index.php?controller=payment&action=refund&id=' . $_POST['payment_id']);
                }
            } catch (Exception $e) {
                set_flash('error', "Lỗi API: " . $e->getMessage(), 'danger');
                header('Location: index.php?controller=payment&action=refunds');
            }
            exit();
        }

        $id = $_GET['id'] ?? 0;
        try {
            $apiRes = ApiService::get("/api/payments/$id");
            if ($apiRes['http_code'] === 200 && $apiRes['response']['success']) {
                $payment = $apiRes['response']['data']['payment'];
                require_once __DIR__ . '/../views/payments/refund.php';
            } else {
                set_flash('error', 'Không tìm thấy thanh toán!', 'danger');
                header('Location: index.php?controller=payment&action=index');
                exit();
            }
        } catch (Exception $e) {
            set_flash('error', "Lỗi API: " . $e->getMessage(), 'danger');
            header('Location: index.php?controller=payment&action=index');
            exit();
        }
    }

    public function refunds()
    {
        $this->checkLogin();
        $page = isset($_GET['page']) ? (int) $_GET['page'] : 1;

        try {
            $apiRes = ApiService::get("/api/payments/refunds?page=$page");
            if ($apiRes['http_code'] === 200 && $apiRes['response']['success']) {
                $refunds = $apiRes['response']['data']['refunds'];
                $paginationData = $apiRes['response']['data']['pagination'];
                $pagination = paginate($paginationData['total'], $paginationData['per_page'], $paginationData['current_page']);
                
                require_once __DIR__ . '/../views/payments/refund_list.php';
            } else {
                set_flash('error', 'Không thể lấy dữ liệu', 'danger');
                header("Location: index.php?action=dashboard");
                exit;
            }
        } catch (Exception $e) {
            set_flash('error', "Lỗi API: " . $e->getMessage(), 'danger');
            header("Location: index.php?action=dashboard");
            exit;
        }
    }

    public function myDebts()
    {
        $this->checkLogin();
        try {
            $apiRes = ApiService::get("/api/payments/my-debts");
            if ($apiRes['http_code'] === 200 && $apiRes['response']['success']) {
                $grouped_debts = $apiRes['response']['data']['grouped_debts'];
                require_once __DIR__ . '/../views/payments/my_debts.php';
            } else {
                set_flash('error', 'Không thể lấy dữ liệu công nợ', 'danger');
                header("Location: index.php?action=dashboard");
                exit;
            }
        } catch (Exception $e) {
            set_flash('error', "Lỗi API: " . $e->getMessage(), 'danger');
            header("Location: index.php?action=dashboard");
            exit;
        }
    }

    public function paymentMethod()
    {
        $this->checkLogin();
        $fee_type_id = $_GET['fee_type_id'] ?? 0;
        $amount = $_GET['amount'] ?? 0;
        $fee_name = urldecode($_GET['fee_name'] ?? '');

        if (!$fee_type_id || !$amount) {
            set_flash('error', 'Thông tin khoản thu không hợp lệ!');
            header('Location: index.php?controller=payment&action=myDebts');
            exit;
        }

        require_once __DIR__ . '/../views/payments/payment_method.php';
    }

    public function uploadProof()
    {
        $this->checkLogin();
        $fee_type_id = $_GET['fee_type_id'] ?? 0;
        $amount = $_GET['amount'] ?? 0;
        $fee_name = urldecode($_GET['fee_name'] ?? '');

        if (!$fee_type_id || !$amount) {
            set_flash('error', 'Thông tin khoản thu không hợp lệ!');
            header('Location: index.php?controller=payment&action=myDebts');
            exit;
        }

        require_once __DIR__ . '/../views/payments/upload_proof.php';
    }

    public function storeProof()
    {
        $this->checkLogin();
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            header('Location: index.php?controller=payment&action=myDebts');
            exit();
        }

        if (isset($_FILES['proof_image']) && $_FILES['proof_image']['error'] == 0) {
            try {
                // Use File proxy from ApiService to transmit the file seamlessly to BE
                $apiRes = ApiService::postFile(
                    '/api/payments/proofs',
                    $_POST,
                    'proof_image',
                    $_FILES['proof_image']['tmp_name'],
                    $_FILES['proof_image']['type'],
                    $_FILES['proof_image']['name']
                );
                
                if ($apiRes['http_code'] === 200 && $apiRes['response']['success']) {
                    set_flash('success', $apiRes['response']['message']);
                    header('Location: index.php?controller=payment&action=myDebts');
                    exit;
                } else {
                    set_flash('error', $apiRes['response']['message'] ?? 'Lỗi không xác định');
                    header('Location: ' . $_SERVER['HTTP_REFERER']);
                    exit;
                }
            } catch (Exception $e) {
                set_flash('error', "Lỗi kết nối API Proxy File: " . $e->getMessage());
                header('Location: ' . $_SERVER['HTTP_REFERER']);
                exit;
            }
        } else {
            set_flash('error', 'Vui lòng chọn file minh chứng!');
            header('Location: ' . $_SERVER['HTTP_REFERER']);
            exit;
        }
    }

    public function manageProofs()
    {
        $this->checkLogin();
        $status = $_GET['status'] ?? 'Pending';
        try {
            $apiRes = ApiService::get("/api/payments/proofs?status=$status");
            if ($apiRes['http_code'] === 200 && $apiRes['response']['success']) {
                $proofs = $apiRes['response']['data']['proofs'];
                require_once __DIR__ . '/../views/payments/manage_proofs.php';
            } else {
                set_flash('error', 'Không thể lấy dữ liệu', 'danger');
                header("Location: index.php?action=dashboard");
                exit;
            }
        } catch (Exception $e) {
            set_flash('error', "Lỗi API: " . $e->getMessage(), 'danger');
            header("Location: index.php?action=dashboard");
            exit;
        }
    }

    public function approveProof()
    {
        $this->checkLogin();
        $id = $_GET['id'] ?? 0;
        try {
            $apiRes = ApiService::post("/api/payments/proofs/$id/approve", ['id' => $id]);
            if ($apiRes['http_code'] === 200 && $apiRes['response']['success']) {
                set_flash('success', $apiRes['response']['message']);
            } else {
                set_flash('error', $apiRes['response']['message'] ?? 'Thất bại');
            }
        } catch (Exception $e) {
            set_flash('error', "Lỗi API: " . $e->getMessage(), 'danger');
        }
        header("Location: index.php?controller=payment&action=manageProofs");
        exit;
    }

    public function rejectProof()
    {
        $this->checkLogin();
        try {
            $apiRes = ApiService::post("/api/payments/proofs/" . $_POST['id'] . "/reject", $_POST);
            if ($apiRes['http_code'] === 200 && $apiRes['response']['success']) {
                set_flash('success', $apiRes['response']['message']);
            } else {
                set_flash('error', $apiRes['response']['message'] ?? 'Thất bại');
            }
        } catch (Exception $e) {
            set_flash('error', "Lỗi API: " . $e->getMessage(), 'danger');
        }
        header("Location: index.php?controller=payment&action=manageProofs");
        exit;
    }

    public function export()
    {
        $this->checkLogin();
        $allowedParams = ['search', 'from_date', 'to_date', 'class_id', 'fee_type_id', 'student_id'];
        $queryArr = [];
        foreach ($allowedParams as $param) {
            if (isset($_GET[$param])) {
                $queryArr[] = $param . '=' . urlencode($_GET[$param]);
            }
        }
        $queryString = "/api/payments/export?" . implode('&', $queryArr);

        try {
            $apiRes = ApiService::get($queryString);
            if ($apiRes['http_code'] === 200 && $apiRes['response']['success']) {
                $payments = $apiRes['response']['data']['payments'];
                
                $filename = 'DanhSachThanhToan_' . date('YmdHis') . '.csv';

                if (ob_get_level()) ob_end_clean();

                header('Content-Type: text/csv; charset=utf-8');
                header('Content-Disposition: attachment; filename=' . $filename);

                $output = fopen('php://output', 'w');
                fputs($output, "\xEF\xBB\xBF");
                fputcsv($output, ['STT', 'Mã phiếu', 'Mã HS', 'Họ tên', 'Lớp', 'Khoản thu', 'Số tiền', 'Ngày đóng', 'Phương thức', 'Người thu', 'Ghi chú']);

                $stt = 1;
                foreach ($payments as $p) {
                    fputcsv($output, [
                        $stt++,
                        $p['payment_code'],
                        $p['student_code'],
                        $p['student_name'],
                        $p['class_name'],
                        $p['fee_name'],
                        $p['amount_paid'],
                        format_date($p['payment_date']),
                        $p['payment_method'],
                        $p['collector_name'],
                        $p['notes']
                    ]);
                }
                fclose($output);
                exit();
            } else {
                set_flash('error', "Không thể xuất dữ liệu: " . ($apiRes['response']['message']), 'danger');
                header("Location: index.php?controller=payment&action=index");
                exit;
            }
        } catch (Exception $e) {
            set_flash('error', "Lỗi kết nối API: " . $e->getMessage(), 'danger');
            header("Location: index.php?controller=payment&action=index");
            exit;
        }
    }

    public function getStudentDebts()
    {
        $this->checkLogin();
        $student_id = $_GET['student_id'] ?? 0;
        
        try {
            // Because this is called via AJAX in frontend
            $apiRes = ApiService::get("/api/student-debts/$student_id");
            header('Content-Type: application/json');
            if ($apiRes['http_code'] === 200) {
                echo json_encode($apiRes['response']);
            } else {
                echo json_encode(['success' => false, 'message' => 'Lỗi API']);
            }
        } catch (Exception $e) {
            header('Content-Type: application/json');
            echo json_encode(['success' => false, 'message' => $e->getMessage()]);
        }
        exit;
    }
}
