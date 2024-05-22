<?php
namespace App\Controllers;

// interfaces
use App\Middleware\BaseResponse;
use App\Middleware\IHandler;
use App\Request\IFullRequest;
use App\Request\IRequest;
use App\Request\Request;

// concrete default implementations
use App\Services\Authenticate\UserIdentity;
use App\Services\Session\SessionService;
use App\Services\Session\ISessionService;
use App\View\IViewRenderer;
use App\View\ViewRenderer;
use App\Middleware\IResponse;

class AppController implements IHandler
{
    protected IFullRequest $request;
    protected ISessionService $sessionService;
    protected IViewRenderer $viewRenderer;

    public function __construct(IFullRequest $request, ISessionService $sessionService = null, IViewRenderer $viewRenderer = null)
    {
        $this->request = $request;
        $this->sessionService = $sessionService ?: new SessionService();
        $this->viewRenderer = $viewRenderer ?: new ViewRenderer('public/views');
    }

    public function handle(IRequest $request): IResponse
    {
        $actionMethod = $this->getActionMethod();
        if (!method_exists($this, $actionMethod)) {
            return new BaseResponse(404, [], 'Not Found');
        }
        $params = $this->request->getAttribute('params', []);
        return call_user_func_array([$this, $actionMethod], array_merge([$request], $params));
    }

    private function getActionMethod(): string
    {
        $action = $this->request->getAttribute('action', 'index');
        $method = strtolower($this->request->getMethod());
        return $method . ucfirst($action);
    }

    public function render(string $template, array $variables = [], string $content = null): IResponse
    {
        $identityString = $this->sessionService->get('identity');

        $globalVariables = [];

        if ($identityString) {
            $identity = UserIdentity::fromString($identityString);
            $globalVariables['userId'] = $identity->getId();
            $globalVariables['userRole'] = $identity->getRole()->getName();
        }

        $variables = array_merge($variables, $globalVariables);
        $content = $this->viewRenderer->render($template, $variables, $content);
        return new BaseResponse(200, [], $content);
    }
}