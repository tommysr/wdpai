<?php
namespace App\Controllers;

// interfaces
use App\Middleware\BaseResponse;
use App\Middleware\IHandler;
use App\Request\IFullRequest;
use App\Request\IRequest;
use App\Request\Request;

// concrete default implementations
use App\Services\Session\SessionService;
use App\Services\Session\ISessionService;
use App\Utils\GlobalVariablesManager;
use App\View\IViewRenderer;
use App\View\ViewRenderer;
use App\Middleware\IResponse;

class AppController implements IHandler
{
    protected IFullRequest $request;
    protected ISessionService $sessionService;
    protected IViewRenderer $viewRenderer;

    public function __construct(IFullRequest $request = null, ISessionService $sessionService = null, IViewRenderer $viewRenderer = null)
    {
        $this->request = $request ?: new Request();
        $this->sessionService = $sessionService ?: new SessionService();
        $this->viewRenderer = $viewRenderer ?: new ViewRenderer('public/views');
    }

    public function handle(IRequest $request): IResponse
    {
        $actionMethod = $this->getActionMethod();
        return $this->$actionMethod($request);
    }

    private function getActionMethod(): string
    {
        return $this->request->getAttribute('action', 'index');
    }

    public function render(string $template, array $variables = [], string $content = null): IResponse
    {
        $globalVariables = GlobalVariablesManager::getGlobalVariables($this->sessionService);
        $variables = array_merge($variables, $globalVariables);
        $content = $this->viewRenderer->render($template, $variables, $content);
        return new BaseResponse(200, body: $content);
    }
}