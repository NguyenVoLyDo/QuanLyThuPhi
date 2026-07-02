<?php
require_once __DIR__ . '/../services/ApiService.php';

class AuditLogController {
    private function checkLogin() {
        if (!isset($_SESSION['user_id'])) {
            header("Location: index.php?action=login");
            exit;
        }
    }
    
    public function index() {
        $this->checkLogin();
        
        $search = $_GET['search'] ?? '';
        $user_id = $_GET['user_id'] ?? '';
        $log_action = $_GET['log_action'] ?? '';
        $from_date = $_GET['from_date'] ?? '';
        $to_date = $_GET['to_date'] ?? '';
        $page = isset($_GET['page']) ? (int)$_GET['page'] : 1;
        
        $qs = "/api/audit?search=" . urlencode($search) . "&user_id=" . urlencode($user_id) . "&log_action=" . urlencode($log_action) . "&from_date=" . urlencode($from_date) . "&to_date=" . urlencode($to_date) . "&page=$page";
        
        try {
            $apiRes = ApiService::get($qs);
            if ($apiRes['http_code'] === 200 && $apiRes['response']['success']) {
                $data = $apiRes['response']['data'];
                $logs = $data['logs'];
                $users = $data['users'];
                $actions = $data['actions'];
                $paginationData = $data['pagination'];
                $pagination = paginate($paginationData['total'], $paginationData['per_page'], $paginationData['current_page']);
                
                require_once __DIR__ . '/../views/admin/logs.php';
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
    
    public function export() {
        $this->checkLogin();
        
        $search = $_GET['search'] ?? '';
        $user_id = $_GET['user_id'] ?? '';
        $log_action = $_GET['log_action'] ?? '';
        $from_date = $_GET['from_date'] ?? '';
        $to_date = $_GET['to_date'] ?? '';
        
        $qs = "/api/audit/export?search=" . urlencode($search) . "&user_id=" . urlencode($user_id) . "&log_action=" . urlencode($log_action) . "&from_date=" . urlencode($from_date) . "&to_date=" . urlencode($to_date);
        
        try {
            $apiRes = ApiService::get($qs);
            if ($apiRes['http_code'] === 200 && $apiRes['response']['success']) {
                $logs = $apiRes['response']['data']['logs'];
                
                $filename = 'AuditLogs_' . date('YmdHis') . '.csv';
                if (ob_get_level()) ob_end_clean();
                
                header('Content-Type: text/csv; charset=utf-8');
                header('Content-Disposition: attachment; filename=' . $filename);
                
                $output = fopen('php://output', 'w');
                fputs($output, "\xEF\xBB\xBF");
                fputcsv($output, ['STT', 'Thời gian', 'Người dùng', 'Hành động', 'Loại', 'Bảng', 'ID', 'Chi tiết', 'IP']);
                
                $stt = 1;
                foreach ($logs as $log) {
                    fputcsv($output, [
                        $stt++,
                        format_datetime($log['created_at']),
                        $log['full_name'] ?? 'N/A',
                        $log['action'],
                        $log['target_type'] ?? '',
                        $log['target_table'] ?? '',
                        $log['target_id'] ?? '',
                        $log['details'] ?? '',
                        $log['ip_address'] ?? ''
                    ]);
                }
                
                fclose($output);
                exit();
            } else {
                set_flash('error', "Xuất file thất bại: " . ($apiRes['response']['message'] ?? ''), 'danger');
                header("Location: index.php?controller=auditlog&action=index");
                exit;
            }
        } catch (Exception $e) {
            set_flash('error', "Lỗi kết nối API: " . $e->getMessage(), 'danger');
            header("Location: index.php?controller=auditlog&action=index");
            exit;
        }
    }
}
