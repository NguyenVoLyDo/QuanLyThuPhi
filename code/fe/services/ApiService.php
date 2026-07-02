<?php
class ApiService
{
    private static $baseUrl = null;

    public static function getBaseUrl()
    {
        if (self::$baseUrl !== null) return self::$baseUrl;

        // Auto-detect base URL
        $protocol = isset($_SERVER['HTTPS']) && $_SERVER['HTTPS'] === 'on' ? 'https' : 'http';
        $host = $_SERVER['HTTP_HOST'];
        
        // Get the project path (assuming /fe/ is where the current script is)
        $scriptPath = $_SERVER['SCRIPT_NAME']; // e.g., /QuanLyThuPhi/fe/index.php
        $projectPath = str_ireplace('/fe/index.php', '', $scriptPath); // e.g., /QuanLyThuPhi
        
        // Remove index.php from the end if it's there
        $projectPath = preg_replace('/\/index\.php$/i', '', $projectPath);
        
        self::$baseUrl = "{$protocol}://{$host}{$projectPath}/be";
        return self::$baseUrl;
    }

    /**
     * Set up basic cURL connection
     */
    private static function setupCurl($url, $method = 'GET', $data = null)
    {
        $ch = curl_init($url);
        
        // Return response instead of outputting
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        
        $headers = [
            'Accept: application/json',
            'Content-Type: application/json'
        ];

        // Attach Token if available
        if (isset($_SESSION['api_token']) && !empty($_SESSION['api_token'])) {
            $headers[] = 'Authorization: Bearer ' . $_SESSION['api_token'];
        }

        curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);

        if ($method === 'POST') {
            curl_setopt($ch, CURLOPT_POST, true);
            if ($data !== null) {
                curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($data));
            }
        } elseif ($method === 'PUT') {
            curl_setopt($ch, CURLOPT_CUSTOMREQUEST, 'PUT');
            if ($data !== null) {
                curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($data));
            }
        } elseif ($method === 'DELETE') {
            curl_setopt($ch, CURLOPT_CUSTOMREQUEST, 'DELETE');
            if ($data !== null) {
                curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($data));
            }
        }

        return $ch;
    }

    /**
     * Execute HTTP Request and process JSON response
     */
    private static function execute($ch)
    {
        $response = curl_exec($ch);
        $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
        $errorStr = curl_error($ch);
        curl_close($ch);

        if ($response === false) {
            throw new Exception("cURL Error: " . $errorStr);
        }

        $decoded = json_decode($response, true);
        
        if (json_last_error() !== JSON_ERROR_NONE) {
            // The BE response is not valid JSON
            throw new Exception("Invalid JSON Response from API. HTTP Code: {$httpCode}. Response: " . substr($response, 0, 100));
        }

        return [
            'http_code' => $httpCode,
            'response' => $decoded
        ];
    }

    public static function get($queryString)
    {
        $url = self::getBaseUrl() . $queryString;
        $ch = self::setupCurl($url, 'GET');
        return self::execute($ch);
    }

    public static function post($queryString, $data)
    {
        $url = self::getBaseUrl() . $queryString;
        $ch = self::setupCurl($url, 'POST', $data);
        return self::execute($ch);
    }
    
    public static function put($queryString, $data)
    {
        $url = self::getBaseUrl() . $queryString;
        $ch = self::setupCurl($url, 'PUT', $data);
        return self::execute($ch);
    }

    public static function delete($queryString, $data = null)
    {
        $url = self::getBaseUrl() . $queryString;
        $ch = self::setupCurl($url, 'DELETE', $data);
        return self::execute($ch);
    }
    
    /**
     * File upload specific proxy logic
     * $files parameter format matching $_FILES
     */
    public static function postFile($queryString, $postData, $filesKey, $filePath, $mimeType, $originalName)
    {
        $url = self::getBaseUrl() . $queryString;
        $ch = curl_init($url);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_POST, true);

        $headers = [];
        if (isset($_SESSION['api_token']) && !empty($_SESSION['api_token'])) {
            $headers[] = 'Authorization: Bearer ' . $_SESSION['api_token'];
        }
        curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);

        $postFields = $postData;
        $postFields[$filesKey] = new CURLFile($filePath, $mimeType, $originalName);
        
        curl_setopt($ch, CURLOPT_POSTFIELDS, $postFields);
        
        return self::execute($ch);
    }
}
