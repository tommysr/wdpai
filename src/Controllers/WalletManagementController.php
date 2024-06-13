<?php

namespace App\Controllers;

use App\Controllers\AppController;
use App\Controllers\Interfaces\IWalletManagementController;
use App\Middleware\JsonResponse;
use App\Middleware\RedirectResponse;
use App\Request\IFullRequest;
use App\Middleware\IResponse;
use App\Services\Authenticate\IAuthService;
use App\Services\Quests\IQuestProvider;
use App\Services\Session\ISessionService;
use App\Services\Wallets\IWalletService;
use App\View\IViewRenderer;

class WalletManagementController extends AppController implements IWalletManagementController
{
  private IQuestProvider $questService;
  private IAuthService $authService;
  private IWalletService $walletService;

  public function __construct(IFullRequest $request, ISessionService $sessionService, IViewRenderer $viewRenderer, IQuestProvider $questService, IAuthService $authService, IWalletService $walletService)
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

  function validateCryptoAddress($address)
  {
    $validCharacters = '/^[a-km-zA-HJ-NP-Z0-9]+$/';

    $patterns = [
      'bitcoin' => '/^[13][a-km-zA-HJ-NP-Z1-9]{25,34}$|^bc1[ac-hj-np-z02-9]{11,71}$/',
      'ethereum' => '/^0x[a-fA-F0-9]{40}$/',
      'litecoin' => '/^[LM3][a-km-zA-HJ-NP-Z1-9]{26,33}$|^ltc1[ac-hj-np-z02-9]{11,71}$/',
      'solana' => '/^[1-9A-HJ-NP-Za-km-z]{32,44}$/',
    ];

    if (!preg_match($validCharacters, $address)) {
      return false;
    }

    foreach ($patterns as $crypto => $pattern) {
      if (preg_match($pattern, $address)) {
        return true;
      }
    }

    return true;
  }


  public function getShowQuestWallets(IFullRequest $request, int $questId): IResponse
  {
    $identity = $this->authService->getIdentity();
    $quest = $this->questService->getQuest($questId);

    if (!$quest) {
      return new RedirectResponse('/error/404', ['no such quest']);
    }

    $blockchain = $quest->getBlockchain();
    $wallets = $this->walletService->getBlockchainWallets($identity, $blockchain);

    return $this->render('showWallets', ['title' => 'enter quest', 'questId' => $questId, 'wallets' => $wallets, 'chain' => $blockchain]);
  }

  // TODO: add validation
  public function postAddWallet(IFullRequest $request, string $blockchain): IResponse
  {
    $identity = $this->authService->getIdentity();
    $walletAddress = $this->request->getParsedBodyParam('walletAddress');

    if (!$this->validateCryptoAddress($walletAddress)) {
      return new JsonResponse(['errors' => ['invalid address']]);
    }

    $walletId = $this->walletService->createWallet($identity, $blockchain, $walletAddress);

    return new JsonResponse(['walletId' => $walletId, 'walletAddress' => $walletAddress]);
  }
}