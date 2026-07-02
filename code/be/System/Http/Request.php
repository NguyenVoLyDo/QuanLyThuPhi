<?php
namespace Http;

class Request {

    public $cookie;
    public $request;
    public $files;
    private $params = []; // URL route params (:id, etc.)

    public function __construct() {
        $this->request = $_REQUEST;
        $this->cookie  = $this->clean($_COOKIE);
        $this->files   = $_FILES; // Don't clean FILES - breaks uploads
    }

    public function get($key = '') {
        if ($key != '')
            return isset($_GET[$key]) ? $this->clean($_GET[$key]) : null;
        return $this->clean($_GET);
    }

    public function post($key = '') {
        if ($key != '')
            return isset($_POST[$key]) ? $this->clean($_POST[$key]) : null;
        return $this->clean($_POST);
    }

    /**
     * Read raw JSON body (for PUT/DELETE/JSON POST)
     */
    public function input($key = '') {
        $postdata = file_get_contents("php://input");
        $data = json_decode($postdata, true);

        // Fallback to POST if JSON decode fails
        if ($data === null) {
            $data = $_POST;
        }

        if ($key != '') {
            return isset($data[$key]) ? $data[$key] : null;
        }
        return $data ?? [];
    }

    /**
     * Get all input (JSON body or POST), merged
     */
    public function all() {
        $json = $this->input();
        $post = $_POST;
        return array_merge($post, $json ?? []);
    }

    public function server($key = '') {
        return isset($_SERVER[strtoupper($key)]) ? $this->clean($_SERVER[strtoupper($key)]) : $this->clean($_SERVER);
    }

    public function getMethod() {
        return strtoupper($this->server('REQUEST_METHOD'));
    }

    public function getClientIp() {
        return $this->server('REMOTE_ADDR');
    }

    public function getUrl() {
        return $this->server('REQUEST_URI');
    }

    /**
     * Set route params from Router (e.g. :id)
     */
    public function setParams(array $params) {
        $this->params = $params;
    }

    /**
     * Get a route param by key
     */
    public function param($key) {
        return isset($this->params[$key]) ? $this->params[$key] : null;
    }

    /**
     * Get Bearer Token from Authorization header
     */
    public function getBearerToken() {
        $headers = null;
        if (isset($_SERVER['Authorization'])) {
            $headers = trim($_SERVER['Authorization']);
        } elseif (isset($_SERVER['HTTP_AUTHORIZATION'])) {
            $headers = trim($_SERVER['HTTP_AUTHORIZATION']);
        } elseif (function_exists('apache_request_headers')) {
            $requestHeaders = apache_request_headers();
            $requestHeaders = array_combine(
                array_map('ucwords', array_keys($requestHeaders)),
                array_values($requestHeaders)
            );
            if (isset($requestHeaders['Authorization'])) {
                $headers = trim($requestHeaders['Authorization']);
            }
        }

        if (!empty($headers)) {
            if (preg_match('/Bearer\s(\S+)/', $headers, $matches)) {
                return $matches[1];
            }
        }
        return null;
    }

    private function clean($data) {
        if (is_array($data)) {
            foreach ($data as $key => $value) {
                unset($data[$key]);
                $data[$this->clean($key)] = $this->clean($value);
            }
        } else {
            $data = htmlspecialchars($data, ENT_COMPAT, 'UTF-8');
        }
        return $data;
    }
}
