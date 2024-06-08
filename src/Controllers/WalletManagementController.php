<?php

namespace App\Controllers;

use App\Controllers\AppController;
use App\Controllers\Interfaces\IWalletManagementController;
use App\Middleware\JsonResponse;
use App\Middleware\RedirectResponse;
use App\Models\Wallet;
use App\Request\IFullRequest;
use App\Middleware\IResponse;
use App\Services\Authenticate\IAuthService;
use App\Services\Quests\IQuestService;
use App\Services\Session\ISessionService;
use App\Services\Wallets\IWalletService;
use App\View\IViewRenderer;

class WalletManagementController extends AppController implements IWalletManagementController
{
  private IQuestService $questService;
  private IAuthService $authService;
  private IWalletService $walletService;

  public function __construct(IFullRequest $request, ISessionService $sessionService, IViewRenderer $viewRenderer, IQuestService $questService, IAuthService $authService, IWalletService $walletService)
  {
    parent::__construct($request, $sessionService, $viewRenderer);
    $this->questService = $questService;
    $this->authService = $authService;
    $this->walletService = $walletService;
  }

  public function getIndex(IFullRequest $request): IResponse
  {
    return new RedirectResponse('/error404');
  }

  public function getShowQuestWallets(IFullRequest $request, int $questId): IResponse
  {
    $identity = $this->authService->getIdentity();
    $blockchain = $this->questService->getQuestBlockchain($questId);
    $wallets = $this->walletService->getBlockchainWallets($identity, $blockchain);

    return $this->render('showWallets', ['title' => 'enter quest', 'questId' => $questId, 'wallets' => $wallets, 'chain' => $blockchain]);
  }

  public function postAddWallet(IFullRequest $request, string $blockchain): IResponse
  {
    $userId = $this->authService->getIdentity()->getId();
    $walletAddress = $this->request->getParsedBodyParam('walletAddress');
    $wallet = new Wallet(0, $userId, $blockchain, $walletAddress, date('Y-m-d'), date('Y-m-d'));
    $walletId = $this->walletService->createWallet($wallet);

    return new JsonResponse(['walletId' => $walletId, 'walletAddress' => $walletAddress]);
  }
}