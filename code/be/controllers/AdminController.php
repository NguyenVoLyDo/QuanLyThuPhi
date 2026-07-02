<?php
use MVC\Controller;

class AdminController extends Controller
{
    /**
     * GET /api/admin/settings
     */
    public function systemSettings()
    {
        $this->auth(['Admin']);
        
        $config_path = SCRIPT . 'config/settings.json';
        if (file_exists($config_path)) {
            $settings = json_decode(file_get_contents($config_path), true);
        } else {
            $settings = [
                'academic_year' => '2025-2026',
                'semester' => 'HK1',
                'school_name' => 'Trường THPT ABC',
                'school_address' => ''
            ];
        }

        $this->send(200, true, "Thành công", ['settings' => $settings]);
    }

    /**
     * POST /api/admin/settings
     */
    public function updateSettings()
    {
        $this->auth(['Admin']);
        $input = $this->request->all();
        
        $config_path = SCRIPT . 'config/settings.json';
        $settings = [];
        if (file_exists($config_path)) {
            $settings = json_decode(file_get_contents($config_path), true);
        }

        $settings['academic_year'] = clean_input($input['academic_year'] ?? '2025-2026');
        $settings['semester'] = clean_input($input['semester'] ?? 'HK1');
        $settings['school_name'] = clean_input($input['school_name'] ?? '');
        $settings['school_address'] = clean_input($input['school_address'] ?? '');
        $settings['updated_at'] = date('Y-m-d H:i:s');
        
        file_put_contents($config_path, json_encode($settings, JSON_UNESCAPED_UNICODE | JSON_PRETTY_PRINT));
        
        $this->send(200, true, 'Đã lưu cài đặt hệ thống!', ['settings' => $settings]);
    }

    /**
     * GET /api/admin/backup
     */
    public function backup()
    {
        $api_user = $this->auth(['Admin']);
        
        $conn = (new \MVC\Model())->db;
        
        $filename = 'backup_' . date('Y-m-d_H-i-s') . '.sql';
        $backup_dir = SCRIPT . 'uploads/backups/';
        if (!file_exists($backup_dir)) {
            mkdir($backup_dir, 0777, true);
        }

        $filepath = $backup_dir . $filename;
        $out = fopen($filepath, 'w');
        
        $tables = [];
        $query = $conn->query('SHOW TABLES');
        while ($row = $query->fetch(\PDO::FETCH_NUM)) {
            $tables[] = $row[0];
        }
        
        fwrite($out, "-- Database Backup\n");
        fwrite($out, "-- Generated: " . date('Y-m-d H:i:s') . "\n\n");
        fwrite($out, "SET FOREIGN_KEY_CHECKS=0;\n\n");
        
        foreach ($tables as $table) {
            $result = $conn->query('SELECT * FROM ' . $table);
            $num_fields = $result->columnCount();
            
            fwrite($out, "DROP TABLE IF EXISTS `" . $table . "`;\n");
            $row2 = $conn->query('SHOW CREATE TABLE `' . $table . '`')->fetch(\PDO::FETCH_NUM);
            fwrite($out, "\n" . $row2[1] . ";\n\n");
            
            while ($row = $result->fetch(\PDO::FETCH_NUM)) {
                fwrite($out, "INSERT INTO `" . $table . "` VALUES(");
                for ($j = 0; $j < $num_fields; $j++) {
                    if (isset($row[$j])) {
                        $row_val = addslashes($row[$j]);
                        $row_val = str_replace("\n", "\\n", $row_val);
                        fwrite($out, '"' . $row_val . '"');
                    } else {
                        fwrite($out, 'NULL');
                    }
                    if ($j < ($num_fields - 1)) {
                        fwrite($out, ',');
                    }
                }
                fwrite($out, ");\n");
            }
            fwrite($out, "\n\n");
        }
        
        fwrite($out, "SET FOREIGN_KEY_CHECKS=1;\n");
        fclose($out);
        
        require_once MODELS . 'AuditLog.php';
        $auditLog = new AuditLog();
        $auditLog->log($api_user['user_id'], 'BACKUP_DATABASE', 'System', null, 'Created database backup: ' . $filename);
        
        $this->send(200, true, "Đã tạo backup thành công", [
            'filename' => $filename,
            'download_url' => '/api/admin/backup/download?file=' . urlencode($filename)
        ]);
    }

    /**
     * GET /api/admin/backup/download
     */
    public function downloadBackup()
    {
        $this->auth(['Admin']);
        $filename = $this->request->get('file') ?? '';

        if (empty($filename)) {
            $this->send(400, false, 'Thiếu tên file!');
        }

        // Security check: only allow .sql files and check directory traversal
        if (!preg_match('/^backup_.*\.sql$/', $filename)) {
            $this->send(403, false, 'Yêu cầu không hợp lệ!');
        }

        $filepath = SCRIPT . 'uploads/backups/' . $filename;

        if (!file_exists($filepath)) {
            $this->send(404, false, 'Không tìm thấy file backup!');
        }

        header('Content-Description: File Transfer');
        header('Content-Type: application/octet-stream');
        header('Content-Disposition: attachment; filename="' . basename($filepath) . '"');
        header('Expires: 0');
        header('Cache-Control: must-revalidate');
        header('Pragma: public');
        header('Content-Length: ' . filesize($filepath));
        readfile($filepath);
        exit;
    }
}
