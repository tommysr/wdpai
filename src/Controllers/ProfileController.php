<?php

namespace App\Controllers;

use App\Controllers\Interfaces\IProfileController;

use App\Middleware\IResponse;
use App\Middleware\JsonResponse;
use App\Models\Interfaces\IUser;
use App\Request\IFullRequest;
use App\Request\IRequest;
use App\Repository\IUserRepository;
use App\Services\Authenticate\AuthenticateService;
use App\Services\Authenticate\IAuthService;
use App\Services\QuestProgress\IQuestProgressService;
use App\Services\QuestProgress\QuestProgressService;
use App\Services\User\IUserService;
use App\Services\User\UserService;


class ProfileController extends AppController implements IProfileController
{
    private IUserService $userService;
    private IAuthService $authService;
    private IQuestProgressService $questProgressService;

    public function __construct(IFullRequest $request, IUserService $userService = null, IAuthService $authService = null, IQuestProgressService $questProgressService = null)
    {
        parent::__construct($request);
        $this->userService = $userService ?: new UserService();
        $this->authService = $authService ?: new AuthenticateService($this->sessionService);
        $this->questProgressService = $questProgressService ?: new QuestProgressService($this->sessionService);
    }

    public function getIndex(IRequest $request): IResponse
    {
        return $this->getShowProfile($request);
    }

    public function getShowProfile(IRequest $request): IResponse
    {
        $userId = $this->authService->getIdentity()->getId();
        $user = $this->userService->getUserById($userId);
        $joinDate = \DateTime::createFromFormat('Y-m-d', $user->getJoinDate())->format('F Y');
        $stats = $this->questProgressService->getUserQuests($userId);
        return $this->render('layout', ['title' => 'dashboard', 'username' => $user->getName(), 'joinDate' => $joinDate, 'points' => sizeof($stats), 'stats' => $stats], 'dashboard');
    }

    public function postChangePassword(IRequest $request): IResponse
    {
        $userId = $this->authService->getIdentity()->getId();
        $newPassword = $this->request->getParsedBodyParam('new-password');
        $currentPassword = $this->request->getParsedBodyParam('current-password');
        $confirmPassword = $this->request->getParsedBodyParam('confirm-password');

        if (empty($currentPassword)) {
            return new JsonResponse(['errors' => 'Current password is required'], 400);
        }

        if (strlen($newPassword) < 8 || strlen($newPassword) > 255) {
            return new JsonResponse(['errors' => 'Password must be between 8 and 255 characters long'], 400);
        }

        if ($newPassword !== $confirmPassword) {
            return new JsonResponse(['errors' => 'Passwords do not match'], 400);
        }

        if (!$this->userService->verifyPassword($userId, $currentPassword)) {
            return new JsonResponse(['errors' => 'Current password is incorrect'], 400);
        }

        $this->userService->changePassword($userId, $newPassword);

        return new JsonResponse([]);
    }
}