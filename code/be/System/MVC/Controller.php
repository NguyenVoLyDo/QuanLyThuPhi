<?php
namespace MVC;

class Controller {

    /** @var \Http\Request */
    public $request;

    /** @var \Http\Response */
    public $response;

    public function __construct() {
        $this->request  = $GLOBALS['request'];
        $this->response = $GLOBALS['response'];
    }

    /**
     * Load a Model class
     */
    public function model($model) {
        $file = MODELS . ucfirst($model) . '.php';

        if (file_exists($file)) {
            require_once $file;
            $modelClass = ucfirst($model);
            if (class_exists($modelClass))
                return new $modelClass();
            else
                throw new \Exception(sprintf('Model class { %s } not found', $modelClass));
        } else {
            throw new \Exception(sprintf('Model file { %s } not found', $file));
        }
    }

    /**
     * Shortcut: send JSON and exit
     */
    public function send(int $status, bool $success, string $message, $data = null) {
        $this->response->sendJson($status, $success, $message, $data);
    }

    /**
     * Verify JWT token from request and return the payload.
     * If invalid or missing, send 401 and exit.
     */
    public function auth(array $allowedRoles = []) {
        $token = $this->request->getBearerToken();

        if (!$token) {
            $this->response->sendJson(401, false, 'Unauthorized - No Token Provided');
        }

        $payload = \JWT::decode($token);
        if (!$payload) {
            $this->response->sendJson(401, false, 'Unauthorized - Invalid or Expired Token');
        }

        $userRole = $payload['role_name'] ?? '';
        if (!empty($allowedRoles) && !in_array($userRole, $allowedRoles)) {
            $this->response->sendJson(403, false, 'Forbidden - Insufficient permissions');
        }

        return $payload;
    }
}
