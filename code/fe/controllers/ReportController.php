<?php
require_once __DIR__ . '/../services/ApiService.php';

class ReportController
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

        $from_date = $_GET['from_date'] ?? date('Y-m-01');
        $to_date = $_GET['to_date'] ?? date('Y-m-d');
        $debt_search = $_GET['debt_search'] ?? '';

        $queryString = "/api/reports?from_date=" . urlencode($from_date) . "&to_date=" . urlencode($to_date) . "&debt_search=" . urlencode($debt_search);

        try {
            $apiRes = ApiService::get($queryString);
            if ($apiRes['http_code'] === 200 && $apiRes['response']['success']) {
                $data = $apiRes['response']['data'];
                $revenue = $data['revenue'];
                $revenue_by_category = $data['revenue_by_category'];
                $revenue_by_method = $data['revenue_by_method'];
                $students_with_debt = $data['students_with_debt'];
                
                require_once __DIR__ . '/../views/reports/index.php';
            } else {
                set_flash('error', "Không thể lấy dữ liệu: " . ($apiRes['response']['message'] ?? 'Lỗi'), 'danger');
                header("Location: index.php?action=dashboard");
                exit;
            }
        } catch (Exception $e) {
            set_flash('error', "Lỗi API: " . $e->getMessage(), 'danger');
            header("Location: index.php?action=dashboard");
            exit;
        }
    }

    public function exportPayments()
    {
        $this->checkLogin();

        $from_date = $_GET['from_date'] ?? '';
        $to_date = $_GET['to_date'] ?? '';

        $queryString = "/api/reports/payments?from_date=" . urlencode($from_date) . "&to_date=" . urlencode($to_date);

        try {
            $apiRes = ApiService::get($queryString);
            if ($apiRes['http_code'] === 200 && $apiRes['response']['success']) {
                $payments = $apiRes['response']['data']['payments'];
                
                $filename = 'BaoCaoThanhToan_' . date('YmdHis') . '.csv';

                if (ob_get_level()) ob_end_clean();

                header('Content-Type: text/csv; charset=utf-8');
                header('Content-Disposition: attachment; filename=' . $filename);

                $output = fopen('php://output', 'w');
                fputs($output, "\xEF\xBB\xBF");
                fputcsv($output, ['STT', 'Mã phiếu', 'Mã HS', 'Họ tên', 'Lớp', 'Khoản thu', 'Số tiền', 'Ngày đóng', 'Phương thức', 'Người thu']);

                $stt = 1;
                foreach ($payments as $payment) {
                    fputcsv($output, [
                        $stt++,
                        $payment['payment_code'],
                        $payment['student_code'],
                        $payment['student_name'],
                        $payment['class_name'],
                        $payment['fee_name'],
                        $payment['amount_paid'],
                        format_date($payment['payment_date']),
                        $payment['payment_method'],
                        $payment['collector_name']
                    ]);
                }
                fclose($output);
                exit();
            } else {
                set_flash('error', "Xuất file thất bại: " . ($apiRes['response']['message'] ?? ''), 'danger');
                header("Location: index.php?controller=report&action=index");
                exit;
            }
        } catch (Exception $e) {
            set_flash('error', "Lỗi kết nối API: " . $e->getMessage(), 'danger');
            header("Location: index.php?controller=report&action=index");
            exit;
        }
    }

    public function exportDebts()
    {
        $this->checkLogin();

        try {
            $apiRes = ApiService::get("/api/reports/debts");
            if ($apiRes['http_code'] === 200 && $apiRes['response']['success']) {
                $debts = $apiRes['response']['data']['debts'];
                
                $filename = 'BaoCaoCongNo_' . date('YmdHis') . '.csv';

                if (ob_get_level()) ob_end_clean();

                header('Content-Type: text/csv; charset=utf-8');
                header('Content-Disposition: attachment; filename=' . $filename);

                $output = fopen('php://output', 'w');
                fputs($output, "\xEF\xBB\xBF");
                fputcsv($output, ['STT', 'Mã HS', 'Họ tên', 'Lớp', 'Khoản thu', 'Loại phí', 'Tổng tiền', 'Đã đóng', 'Còn nợ', 'Hạn đóng']);

                $stt = 1;
                foreach ($debts as $debt) {
                    fputcsv($output, [
                        $stt++,
                        $debt['student_code'],
                        $debt['full_name'],
                        $debt['class_name'],
                        $debt['fee_name'],
                        $debt['fee_category'],
                        $debt['total_amount'],
                        $debt['paid_amount'],
                        $debt['remaining_amount'],
                        format_date($debt['due_date'])
                    ]);
                }
                fclose($output);
                exit();
            } else {
                set_flash('error', "Xuất file thất bại: " . ($apiRes['response']['message'] ?? ''), 'danger');
                header("Location: index.php?controller=report&action=index");
                exit;
            }
        } catch (Exception $e) {
            set_flash('error', "Lỗi kết nối API: " . $e->getMessage(), 'danger');
            header("Location: index.php?controller=report&action=index");
            exit;
        }
    }
}
