<?php

namespace App\Controllers;

use App\Controllers\Interfaces\IProfileController;
use App\Middleware\IResponse;
use App\Middleware\JsonResponse;
use App\Request\IFullRequest;
use App\Request\IRequest;
use App\Services\Authenticate\IAuthService;
use App\Services\QuestProgress\IQuestProgressRetrievalService;
use App\Services\QuestProgress\IQuestProgressService;
use App\Services\Quests\IQuestService;
use App\Services\Session\ISessionService;
use App\Services\User\IUserService;
use App\View\IViewRenderer;


class ProfileController extends AppController implements IProfileController
{
    private IUserService $userService;
    private IAuthService $authService;
    private IQuestProgressRetrievalService $questProgressService;
    private IQuestService $questsService;

    public function __construct(
        IFullRequest $request,
        ISessionService $sessionService,
        IViewRenderer $viewRenderer,
        IUserService $userService,
        IAuthService $authService,
        IQuestProgressRetrievalService $questProgressService
    ) {
        parent::__construct($request, $sessionService, $viewRenderer);

        $this->userService = $userService;
        $this->authService = $authService;
        $this->questProgressService = $questProgressService;
    }

    public function getIndex(IRequest $request): IResponse
    {
        return $this->getShowProfile($request);
    }

    public function getShowProfile(IFullRequest $request): IResponse
    {
        $userId = $this->authService->getIdentity()->getId();
        $user = $this->userService->getUserById($userId);
        $joinDate = \DateTime::createFromFormat('Y-m-d', $user->getJoinDate())->format('F Y');
        $stats = $this->questProgressService->getUserQuests($userId);
        return $this->render('layout', ['title' => 'dashboard', 'username' => $user->getName(), 'joinDate' => $joinDate, 'points' => sizeof($stats), 'stats' => $stats], 'dashboard');
    }

    public function postChangePassword(IFullRequest $request): IResponse
    {
        $userId = $this->authService->getIdentity()->getId();

        $newPassword = $request->getParsedBodyParam('new-password');
        $currentPassword = $request->getParsedBodyParam('current-password');
        $confirmPassword = $request->getParsedBodyParam('confirm-password');

        if (empty($currentPassword)) {
            return new JsonResponse(['errors' => 'Current password is required'], 400);
        }

        if (strlen($newPassword) < 8 || strlen($newPassword) > 255) {
            return new JsonResponse(['errors' => ['Password must be between 8 and 255 characters long']], 400);
        }

        if ($newPassword !== $confirmPassword) {
            return new JsonResponse(['errors' => ['Passwords do not match']], 400);
        }

        if (!$this->userService->verifyPassword($userId, $currentPassword)) {
            return new JsonResponse(['errors' => ['Current password is incorrect']], 400);
        }

        $this->userService->changePassword($userId, $newPassword);

        return new JsonResponse([]);
    }
}