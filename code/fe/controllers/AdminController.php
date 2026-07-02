<?php
require_once __DIR__ . '/../services/ApiService.php';

class AdminController {
    private function checkLogin() {
        if (!isset($_SESSION['user_id'])) {
            header("Location: index.php?action=login");
            exit;
        }
    }

    public function systemSettings() {
        $this->checkLogin();
        
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            try {
                $apiRes = ApiService::post("/api/admin/settings", $_POST);
                if ($apiRes['http_code'] === 200 && $apiRes['response']['success']) {
                    set_flash('success', $apiRes['response']['message']);
                } else {
                    set_flash('error', $apiRes['response']['message'] ?? 'Thất bại');
                }
            } catch (Exception $e) {
                set_flash('error', "Lỗi API: " . $e->getMessage());
            }
            header("Location: index.php?controller=admin&action=systemSettings");
            exit;
        }
        
        try {
            $apiRes = ApiService::get("/api/admin/settings");
            if ($apiRes['http_code'] === 200 && $apiRes['response']['success']) {
                $settings = $apiRes['response']['data']['settings'];
                require_once __DIR__ . '/../views/admin/system_settings.php';
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

    public function backupPage() {
        $this->checkLogin();
        require_once __DIR__ . '/../views/admin/backup.php';
    }

    public function backup() {
        $this->checkLogin();
        try {
            $apiRes = ApiService::get("/api/admin/backup");
            if ($apiRes['http_code'] === 200 && $apiRes['response']['success']) {
                $download_url = $apiRes['response']['data']['download_url'];
                $filename = $apiRes['response']['data']['filename'];

                // Get the file content through API service to pass Authorization header
                $ch = curl_init(ApiService::getBaseUrl() . $download_url);
                curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
                $headers = ['Authorization: Bearer ' . ($_SESSION['api_token'] ?? '')];
                curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
                
                $fileContent = curl_exec($ch);
                $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
                curl_close($ch);

                if ($httpCode === 200) {
                    header('Content-Type: application/octet-stream');
                    header('Content-Disposition: attachment; filename="' . $filename . '"');
                    echo $fileContent;
                    exit;
                } else {
                    set_flash('error', 'Không thể tải file từ máy chủ API (HTTP ' . $httpCode . ')');
                }
            } else {
                set_flash('error', $apiRes['response']['message'] ?? 'Sao lưu thất bại');
            }
        } catch (Exception $e) {
            set_flash('error', "Lỗi API: " . $e->getMessage());
        }
        header("Location: index.php?controller=admin&action=backupPage");
        exit;
    }
}
