<?php
namespace App\Controllers;

// interfaces
use App\Request\IRequest;
use App\Request\Request;

// concrete default implementations
use App\Services\Session\SessionService;
use App\Services\Session\ISessionService;
use App\Utils\GlobalVariablesManager;
use App\View\IViewRenderer;
use App\View\ViewRenderer;

class AppController
{
    protected IRequest $request;
    protected ISessionService $sessionService;
    protected IViewRenderer $viewRenderer;

    public function __construct(IRequest $request = null, ISessionService $sessionService = null, IViewRenderer $viewRenderer = null)
    {
        $this->request = $request ?: new Request();
        $this->sessionService = $sessionService ?: new SessionService();
        $this->viewRenderer = $viewRenderer ?: new ViewRenderer('public/views');
    }

    public function render(string $template, array $variables = [], string $content = null): string
    {
        $globalVariables = GlobalVariablesManager::getGlobalVariables($this->sessionService);
        $variables = array_merge($variables, $globalVariables);
        return $this->viewRenderer->render($template, $variables, $content);
    }
}