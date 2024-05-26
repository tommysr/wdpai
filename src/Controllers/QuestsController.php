<?php

namespace App\Controllers;

use App\Controllers\AppController;
use App\Controllers\Interfaces\IQuestsController;
use App\Middleware\JsonResponse;
use App\Middleware\RedirectResponse;
use App\Models\IQuest;
use App\Models\IQuestion;
use App\Models\Option;
use App\Models\Question;
use App\Models\QuestionTypeUtil;
use App\Request\IFullRequest;
use App\Request\IRequest;
use App\Middleware\IResponse;
use App\Services\Authenticate\AuthenticateService;
use App\Services\Authenticate\IAuthService;
use App\Services\Quests\Builder\IQuestBuilder;
use App\Services\Quests\Builder\IQuestBuilderService;
use App\Services\Quests\Builder\QuestBuilder;
use App\Services\Quests\Builder\QuestBuilderService;
use App\Services\Quests\IQuestService;
use App\Services\Quests\QuestService;

class QuestsController extends AppController implements IQuestsController
{
  private IQuestService $questService;
  private IAuthService $authService;
  private IQuestBuilderService $questBuilderService;

  public function __construct(IFullRequest $request, IQuestService $questService = null, IAuthService $authService = null, IQuestBuilderService $questBuilderService = null)
  {
    parent::__construct($request);
    $this->questService = $questService ?: new QuestService();
    $this->authService = $authService ?: new AuthenticateService($this->sessionService);
    $this->questBuilderService = $questBuilderService ?: new QuestBuilderService(new QuestBuilder());
  }

  /*
      User actions
  */
  public function getIndex(IRequest $request): IResponse
  {
    return $this->getShowQuests($request);
  }

  // shows all quests which are approved and can be played
  public function getShowQuests(IRequest $request): IResponse
  {
    $quests = $this->questService->getQuestsToPlay();

    return $this->render('layout', ['title' => 'quest list', 'quests' => $quests], 'quests');
  }

  /*
    Creator actions
  */
  private function renderEditAndCreateView(IQuest $quest = null): IResponse
  {
    return $this->render('layout', ['title' => 'quest add', 'quest' => $quest], 'createQuest');
  }


  // returns create quest view
  public function getCreateQuest(IRequest $request): IResponse
  {
    return $this->renderEditAndCreateView();
  }

  // returns edit quest view
  public function getEditQuest(IRequest $request, int $questId): IResponse
  {
    $quest = $this->questService->getQuestWithQuestions($questId);

    if (!$quest) {
      return new RedirectResponse('/error/404', 0);
    }

    return $this->renderEditAndCreateView($quest);
  }

  // show created quests list which are not approved yet, but can be edited by creator
  public function getShowCreatedQuests(IRequest $request): IResponse
  {
    $quests = $this->questService->getCreatorQuests($this->authService->getIdentity());

    return $this->render('layout', ['title' => 'created quests', 'quests' => $quests], 'createdQuests');
  }

  public function postCreateQuest(IRequest $request): IResponse
  {
    $formData = $this->request->getBody();
    $parsedData = json_decode($formData, true);
    $creatorId = $this->authService->getIdentity()->getId();
    $parsedData['creatorId'] = $creatorId;
    $quest = $this->questBuilderService->buildQuest($parsedData);
    $questResult = $this->questService->createQuest($quest);

    if (!$questResult->isSuccess()) {
      return new JsonResponse(['messages' => $questResult->getMessages()]);
    } else {
      return new RedirectResponse('/showCreatedQuests');
    }
  }

  public function postEditQuest(IRequest $request, int $questId): IResponse
  {
    $formData = $this->request->getBody();
    $parsedData = json_decode($formData, true);
    $parsedData['questId'] = $questId;
    $quest = $this->questBuilderService->buildQuest($parsedData);
    $questResult = $this->questService->editQuest($quest);

    if (!$questResult->isSuccess()) {
      return new JsonResponse(['messages' => $questResult->getMessages()]);
    } else {
      return new RedirectResponse('/showCreatedQuests');
    }
  }

  /*
      Admin actions
  */

  // shows list of quests which are not approved yet, but can be approved by admin
  public function getShowQuestsToApproval(IRequest $request): IResponse
  {
    $quests = $this->questService->getQuestsToApproval();

    return $this->render('layout', ['title' => 'quests to approval', 'quests' => $quests], 'questsToApproval');
  }

  // publishes/approves quest
  public function postPublish(IRequest $request, int $questId): IResponse
  {
    $this->questService->publishQuest($questId);

    return new JsonResponse(['message' => 'quest published']);
  }

  public function getShowWalletInput(IRequest $request, int $questId): IResponse
  {
    $identity = $this->authService->getIdentity();
    $wallets = $this->questService->getQuestWallets($identity, $questId);

    return $this->render('showWallets', ['title' => 'enter quest', 'questId' => $questId, 'wallets' => $wallets]);
  }

  // public function postSaveWallet(IRequest $request): IResponse
  // {

  // }

  // public function postStartQuest(IRequest $request, int $questId): IResponse
  // {
  //   $userId = $this->authService->getIdentity()->getId();
  //   $walletSelect = $this->request->getParsedBodyParam('walletSelect');
  //   $walletId = $walletSelect;

  //   if ($walletSelect === 'new') {
  //     $newWalletAddress = $this->request->getParsedBodyParam('newWalletAddress');

  //     if (!$newWalletAddress) {
  //       return new RedirectResponse('/error/404');
  //     }

  //     $walletId = $this->addNewWallet($userId, $questId, $newWalletAddress);
  //   }

  //   // // set user id, quest id, wallet id and score to 0
  //   // $this->questStatisticsRepository->addParticipation($userId, $questId, $walletId);

  //   return new RedirectResponse('gameplay/' . $questId);

  //   //   $this->redirectWithParams('gameplay/' . $questId, ['walletId' => $walletId]);
  //   // } catch (NotLoggedInException $e) {
  //   //   $this->redirectWithParams('login', ['message' => 'first, you need to log in']);
  //   // } catch (GameplayInProgressException $id) {
  //   //   $this->redirectWithParams('gameplay/' . $id->getMessage(), ['message' => 'finish or abandon the quest']);
  //   // } catch (Exception $e) {
  //   //   $this->redirectWithParams('error', ['message' => $e->getMessage()]);
  //   // }
  // }

  // private function addNewWallet($userId, $questId, $walletAddress): int
  // {
  //   $quest = $this->questService->getQuest($questId);
  //   $wallet = new Wallet(0, $userId, $quest->getRequiredWallet(), $walletAddress, date('Y-m-d'), date('Y-m-d'));
  //   $walletId = $this->walletRepository->addWallet($wallet);

  //   return $walletId;
  // }


  // // (string)
  // function validateString($value, $maxLength)
  // {
  //   return strlen($value) > 0 && strlen($value) <= $maxLength;
  // }

  // // (string)
  // function validateWallet($value)
  // {
  //   return is_string($value);
  // }

  // // (integer)
  // function validateInteger($value)
  // {
  //   return filter_var($value, FILTER_VALIDATE_INT) !== false;
  // }

  // // (float)
  // function validateFloat($value)
  // {
  //   return filter_var($value, FILTER_VALIDATE_FLOAT) !== false;
  // }

  // //  (date string)
  // function validateDate($value)
  // {
  //   $date = DateTime::createFromFormat('Y-m-d', $value);
  //   return $date && $date->format('Y-m-d') === $value;
  // }

  // // (3-letter string)
  // function validateToken($value)
  // {
  //   return strlen($value) === 3 && ctype_alpha($value);
  // }

  // function validateQuizData(array $quizData)
  // {
  //   $errors = [];

  //   if (!$this->validateString($quizData['title'], 400)) {
  //     $errors[] = 'Title must be a string with maximum length 400 characters.';
  //   }
  //   if (!$this->validateString($quizData['description'], 255)) {
  //     $errors[] = 'Description must be a string with maximum length 255 characters.';
  //   }
  //   if (!$this->validateWallet($quizData['requiredWallet'])) {
  //     $errors[] = 'Required wallet must be a string.';
  //   }
  //   if (!$this->validateInteger($quizData['timeRequired'])) {
  //     $errors[] = 'Time required must be an integer.';
  //   }
  //   if (!$this->validateInteger($quizData['participantsLimit'])) {
  //     $errors[] = 'Participants limit must be an integer.';
  //   }
  //   if (!$this->validateFloat($quizData['poolAmount'])) {
  //     $errors[] = 'Pool amount must be a valid float.';
  //   }
  //   if (!$this->validateDate($quizData['expiryDate'])) {
  //     $errors[] = 'Expiry date must be a valid date in the format "YYYY-MM-DD".';
  //   }
  //   if (!$this->validateToken($quizData['token'])) {
  //     $errors[] = 'Token must be a 3-letter string.';
  //   }

  //   if (!empty($errors)) {
  //     throw new ValidationException(implode(';', $errors));
  //   }
  // }


  // public function dashboard()
  // {
  //   try {
  //     $id = $this->questAuthorizationService->authorizeQuestRole(QuestRole::NORMAL);
  //     $user = $this->userRepository->getUserById($id);
  //     $joinDate = DateTime::createFromFormat('Y-m-d', $user->getJoinDate())->format('F Y');

  //     $this->render('layout', ['title' => 'dashboard', 'username' => $user->getName(), 'joinDate' => $joinDate, 'points' => 4525], 'dashboard');
  //   } catch (NotLoggedInException $e) {
  //     $this->redirectWithParams('login', ['message' => 'first, you need to log in']);
  //   } catch (Exception $e) {
  //     $this->redirectWithParams('error', ['message' => $e->getMessage()]);
  //   }
  // }





  // public function startQuest(int $questId)
  // {
  //   try {
  //     $userId = $this->questAuthorizationService->authorizeQuestAction(QuestAuthorizeRequest::ENTER, $questId);
  //     $walletSelect = $this->request->post('walletSelect');
  //     $walletId = $walletSelect;

  //     if ($walletSelect === 'new') {
  //       $newWalletAddress = $this->request->post('newWalletAddress');

  //       if (!$newWalletAddress) {
  //         $this->redirectWithParams('error', ['message' => 'something went wrong', 'code' => 404]);
  //       }

  //       $walletId = $this->addNewWallet($userId, $questId, $newWalletAddress);
  //     }

  //     // // set user id, quest id, wallet id and score to 0
  //     // $this->questStatisticsRepository->addParticipation($userId, $questId, $walletId);

  //     $this->redirectWithParams('gameplay/' . $questId, ['walletId' => $walletId]);
  //   } catch (NotLoggedInException $e) {
  //     $this->redirectWithParams('login', ['message' => 'first, you need to log in']);
  //   } catch (GameplayInProgressException $id) {
  //     $this->redirectWithParams('gameplay/' . $id->getMessage(), ['message' => 'finish or abandon the quest']);
  //   } catch (Exception $e) {
  //     $this->redirectWithParams('error', ['message' => $e->getMessage()]);
  //   }
  // }

  // private function addNewWallet($userId, $questId, $walletAddress): int
  // {
  //   $quest = $this->questRepository->getQuestById($questId);
  //   $wallet = new Wallet(0, $userId, $quest->getRequiredWallet(), $walletAddress, date('Y-m-d'), date('Y-m-d'));
  //   $walletId = $this->walletRepository->addWallet($wallet);

  //   return $walletId;
  // }
}