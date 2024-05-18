<?php
require_once __DIR__ . '/../services/SessionService.php';
require_once __DIR__ . '/../utils/Request.php';
require_once __DIR__ . '/../utils/GlobalVariablesManager.php';

class AppController
{
    protected $request;
    protected $sessionService;

    public function __construct(IRequest $request = null, ISessionService $sessionService = null)
    {
        $this->request = $request ?: new Request();
        $this->sessionService = $sessionService ?: new SessionService();
    }

    public function render(string $template, array $variables = [], string $content = null)
    {
        $globalVariables = GlobalVariablesManager::getGlobalVariables($this->sessionService);
        $queryParams = $this->request->queries();
        $templatePath = 'public/views/' . $template . '.php';
        $output = 'File not found';

        if (file_exists($templatePath)) {
            extract($variables);
            extract($queryParams);
            ob_start();
            include $templatePath;
            $output = ob_get_clean();
        }
        print $output;
    }
}