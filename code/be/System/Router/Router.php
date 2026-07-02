<?php
namespace Router;

class Router {

    private $router      = [];
    private $matchRouter = [];
    private $url;
    private $method;
    private $params      = [];
    private $response;

    public function __construct(string $url, string $method) {
        $this->url      = rtrim($url, '/');
        $this->method   = $method;
        $this->response = $GLOBALS['response'];
    }

    public function get($pattern, $callback) {
        $this->addRoute('GET', $pattern, $callback);
    }

    public function post($pattern, $callback) {
        $this->addRoute('POST', $pattern, $callback);
    }

    public function put($pattern, $callback) {
        $this->addRoute('PUT', $pattern, $callback);
    }

    public function delete($pattern, $callback) {
        $this->addRoute('DELETE', $pattern, $callback);
    }

    public function patch($pattern, $callback) {
        $this->addRoute('PATCH', $pattern, $callback);
    }

    public function addRoute($method, $pattern, $callback) {
        array_push($this->router, new Route($method, $pattern, $callback));
    }

    private function getMatchRoutersByRequestMethod() {
        // Support method override via X-HTTP-Method-Override or _method
        $method = $this->method;
        if (isset($_SERVER['HTTP_X_HTTP_METHOD_OVERRIDE'])) {
            $method = strtoupper($_SERVER['HTTP_X_HTTP_METHOD_OVERRIDE']);
        } elseif (isset($_POST['_method'])) {
            $method = strtoupper($_POST['_method']);
        }

        foreach ($this->router as $value) {
            if (strtoupper($method) == $value->getMethod())
                array_push($this->matchRouter, $value);
        }
    }

    private function getMatchRoutersByPattern($pattern) {
        $this->matchRouter = [];
        foreach ($pattern as $value) {
            if ($this->dispatch(cleanUrl($this->url), $value->getPattern()))
                array_push($this->matchRouter, $value);
        }
    }

    public function dispatch($uri, $pattern) {
        $parsUrl = explode('?', $uri);
        $url = $parsUrl[0];

        preg_match_all('@:([\w]+)@', $pattern, $params, PREG_PATTERN_ORDER);
        $patternAsRegex = preg_replace_callback('@:([\w]+)@', [$this, 'convertPatternToRegex'], $pattern);

        if (substr($pattern, -1) === '/') {
            $patternAsRegex = $patternAsRegex . '?';
        }
        $patternAsRegex = '@^' . $patternAsRegex . '$@';

        if (preg_match($patternAsRegex, $url, $paramsValue)) {
            array_shift($paramsValue);
            foreach ($params[0] as $key => $value) {
                $val = substr($value, 1);
                if (isset($paramsValue[$val])) {
                    $this->setParams($val, urldecode($paramsValue[$val]));
                }
            }
            return true;
        }
        return false;
    }

    public function getRouter() {
        return $this->router;
    }

    private function setParams($key, $value) {
        $this->params[$key] = $value;
    }

    private function convertPatternToRegex($matches) {
        $key = str_replace(':', '', $matches[0]);
        return '(?P<' . $key . '>[a-zA-Z0-9_\-\.\!\~\*\\\'\(\)\:\@\&\=\$\+,%]+)';
    }

    public function run() {
        if (!is_array($this->router) || empty($this->router))
            throw new \Exception('No Routes Defined');

        $this->getMatchRoutersByRequestMethod();
        $this->getMatchRoutersByPattern($this->matchRouter);

        if (!$this->matchRouter || empty($this->matchRouter)) {
            $this->sendNotFound();
        } else {
            // Set params into request object
            if (isset($GLOBALS['request'])) {
                $GLOBALS['request']->setParams($this->params);
            }

            $callback = $this->matchRouter[0]->getCallback();
            if (is_callable($callback)) {
                call_user_func($callback, $this->params);
            } else {
                $this->runController($callback, $this->params);
            }
        }
    }

    private function runController($controller, $params) {
        $parts = explode('@', $controller);
        $controllerName = ucfirst($parts[0]) . 'Controller';
        $file = CONTROLLERS . $controllerName . '.php';

        if (file_exists($file)) {
            require_once $file;

            // Load JWT config
            require_once SCRIPT . 'config/jwt.php';

            // Class name: e.g. StudentController
            $controllerClass = $controllerName;

            if (class_exists($controllerClass)) {
                $controllerObj = new $controllerClass();
            } else {
                $this->sendNotFound();
                return;
            }

            $method = isset($parts[1]) ? $parts[1] : 'index';

            if (!method_exists($controllerObj, $method)) {
                $this->sendNotFound();
                return;
            }

            if (is_callable([$controllerObj, $method])) {
                return call_user_func([$controllerObj, $method], $params);
            }
        } else {
            $this->sendNotFound();
        }
    }

    private function sendNotFound() {
        $this->response->sendJson(404, false, 'Route not found');
    }
}
