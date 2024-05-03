<?php

require_once __DIR__ . '/../services/SessionService.php';


class Request
{
    private $requestMethod;

    public function __construct()
    {
        $this->requestMethod = $_SERVER['REQUEST_METHOD'];
    }

    public function isGet()
    {
        return $this->requestMethod == 'GET';
    }

    public function isPost()
    {
        return $this->requestMethod == 'POST';
    }

    public function get(string $key)
    {
        return $_GET[$key] ?? null;
    }

    public function post(string $key)
    {
        return $_POST[$key] ?? null;
    }
}

class AppController
{
    protected $request;
    protected $sessionService;

    public function __construct()
    {
        $this->sessionService = new SessionService();
        $this->request = new Request();
    }

    protected function redirect(string $url, int $code = 0): void
    {
        $url = "http://$_SERVER[HTTP_HOST]/" . $url;
        header('Location:' . $url, true, $code);
    }


    public function render(string $template, array $variables = [], string $content = null)
    {
        $templatePath = 'public/views/' . $template . '.php';
        $output = 'File not found';

        if (file_exists($templatePath)) {
            extract($variables);

            $user = $this->sessionService->get('user');

            ob_start();
            include $templatePath;
            $output = ob_get_clean();
        }
        print $output;
    }
}