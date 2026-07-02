<?php
require_once __DIR__ . '/../services/ApiService.php';

class DebtController
{
    private function checkLogin() {
        if (!isset($_SESSION['user_id'])) {
            header("Location: index.php?action=login");
            exit;
        }
    }

    public function createBatch()
    {
        $this->checkLogin();

        try {
            $apiRes = ApiService::get("/api/debts/batch");
            if ($apiRes['http_code'] === 200 && $apiRes['response']['success']) {
                $semesters = $apiRes['response']['data']['semesters'];
                $classes = $apiRes['response']['data']['classes'];
                $feeTypes = $apiRes['response']['data']['feeTypes'];
                
                require_once __DIR__ . '/../views/debts/create_batch.php';
            } else {
                set_flash('error', "Không thể tải trang: " . ($apiRes['response']['message'] ?? 'Lỗi'), 'danger');
                header("Location: index.php?action=dashboard");
                exit;
            }
        } catch (Exception $e) {
            set_flash('error', "Lỗi API: " . $e->getMessage(), 'danger');
            header("Location: index.php?action=dashboard");
            exit;
        }
    }

    public function storeBatch()
    {
        $this->checkLogin();

        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            header('Location: index.php?controller=debt&action=createBatch');
            exit;
        }

        try {
            $apiRes = ApiService::post("/api/debts/batch", $_POST);
            if ($apiRes['http_code'] === 200 && $apiRes['response']['success']) {
                set_flash('success', $apiRes['response']['message']);
            } else {
                set_flash('error', $apiRes['response']['message'] ?? 'Có lỗi xảy ra!');
            }
        } catch (Exception $e) {
            set_flash('error', "Lỗi kết nối API: " . $e->getMessage(), 'danger');
        }
        
        header('Location: index.php?controller=debt&action=createBatch');
        exit;
    }
}
