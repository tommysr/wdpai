<?php

require_once "AppController.php";
require_once __DIR__ . '/../models/Quest.php';
require_once __DIR__ . '/../repository/QuestRepository.php';
require_once __DIR__ . '/../repository/QuestStatisticsRepository.php';
require_once __DIR__ . '/../repository/QuestionsRepository.php';
require_once __DIR__ . '/../repository/OptionsRepository.php';
require_once __DIR__ . '/../repository/UserRepository.php';
require_once __DIR__ . '/../repository/WalletRepository.php';
require_once __DIR__ . '/../services/QuestAuthorizationService.php';
require_once __DIR__ . '/../services/QuestService.php';
require_once __DIR__ . '/../exceptions/Quests.php';


function compareQuestionsId($a, $b)
{
  if ($a->getQuestionId() == $b->getQuestionId()) {
    return 0; // Objects are equal
  }
  return ($a->getQuestionId() < $b->getQuestionId()) ? -1 : 1;
}

function compareQuestions(Question $q1, Question $q2)
{
  if ($q1->__equals($q2)) {
    return 0;
  }

  return 1;
}



class QuestsController extends AppController
{
  private $questRepository;
  private $walletRepository;
  private $questAuthorizationService;
  private $questionsRepository;
  private $questService;
  private $optionsRepository;
  private $userRepository;

  public function __construct()
  {
    parent::__construct();
    $this->questRepository = new QuestRepository();
    $this->questionsRepository = new QuestionsRepository();
    $this->optionsRepository = new OptionsRepository();
    $this->walletRepository = new WalletRepository();
    $this->questAuthorizationService = new QuestAuthorizationService();
    $this->questService = new QuestService();
    $this->userRepository = new UserRepository();
  }


  // returns quests which are approved by some general admin
  public function index()
  {
    $quests = $this->questRepository->getApprovedQuests();
    $this->render('layout', ['title' => 'quest list', 'quests' => $quests], 'quests');
  }

  // create quest returns quest data given by questId param and checks access to given route
  public function createQuest(?int $questId = null)
  {
    try {
      $requestType = $questId ? QuestAuthorizeRequest::EDIT : QuestAuthorizeRequest::CREATE;

      $this->questAuthorizationService->authorizeQuestAction($requestType, $questId);

      $quest = $this->questService->getQuestWithQuestionsAndOptions($questId);

      $this->render('layout', [
        'title' => 'quest add',
        'quest' => $quest
      ], 'createQuest');
    } catch (NotLoggedInException $e) {
      $this->redirectWithParams('login', ['message' => 'first, you need to log in']);
    } catch (Exception $e) {
      $this->redirectWithParams('error', ['message' => $e->getMessage()]);
    }
  }

  // (string)
  function validateString($value, $maxLength)
  {
    return strlen($value) > 0 && strlen($value) <= $maxLength;
  }

  // (string)
  function validateWallet($value)
  {
    return is_string($value);
  }

  // (integer)
  function validateInteger($value)
  {
    return filter_var($value, FILTER_VALIDATE_INT) !== false;
  }

  // (float)
  function validateFloat($value)
  {
    return filter_var($value, FILTER_VALIDATE_FLOAT) !== false;
  }

  //  (date string)
  function validateDate($value)
  {
    $date = DateTime::createFromFormat('Y-m-d', $value);
    return $date && $date->format('Y-m-d') === $value;
  }

  // (3-letter string)
  function validateToken($value)
  {
    return strlen($value) === 3 && ctype_alpha($value);
  }

  function validateQuizData(array $quizData)
  {
    $errors = [];

    if (!$this->validateString($quizData['title'], 400)) {
      $errors[] = 'Title must be a string with maximum length 400 characters.';
    }
    if (!$this->validateString($quizData['description'], 255)) {
      $errors[] = 'Description must be a string with maximum length 255 characters.';
    }
    if (!$this->validateWallet($quizData['requiredWallet'])) {
      $errors[] = 'Required wallet must be a string.';
    }
    if (!$this->validateInteger($quizData['timeRequired'])) {
      $errors[] = 'Time required must be an integer.';
    }
    if (!$this->validateInteger($quizData['participantsLimit'])) {
      $errors[] = 'Participants limit must be an integer.';
    }
    if (!$this->validateFloat($quizData['poolAmount'])) {
      $errors[] = 'Pool amount must be a valid float.';
    }
    if (!$this->validateDate($quizData['expiryDate'])) {
      $errors[] = 'Expiry date must be a valid date in the format "YYYY-MM-DD".';
    }
    if (!$this->validateToken($quizData['token'])) {
      $errors[] = 'Token must be a 3-letter string.';
    }

    if (!empty($errors)) {
      throw new ValidationException(implode(';', $errors));
    }
  }


  public function editQuest(?int $questId = null)
  {
    try {
      $requestType = $questId ? QuestAuthorizeRequest::EDIT : QuestAuthorizeRequest::CREATE;

      $this->questAuthorizationService->authorizeQuestAction($requestType, $questId);
      $questions = [];

      if ($this->request->post('questions')) {
        foreach ($this->request->post('questions') as $questionId => $questionData) {
          $options = [];
          $correctOptionsCount = 0;
          $questionType = QuestionType::UNKNOWN;

          if (isset($this->request->post('options')[$questionId])) {
            foreach ($this->request->post('options')[$questionId] as $optionIndex => $optionData) {
              $isCorrect = isset($optionData['isCorrect']);

              if ($isCorrect) {
                $correctOptionsCount++;
              }

              $options[] = new Option($optionIndex, $questionId, $optionData['text'], $isCorrect);
            }

            if ($correctOptionsCount == 1) {
              $questionType = QuestionType::SINGLE_CHOICE;
            } else {
              $questionType = QuestionType::MULTIPLE_CHOICE;
            }
          } else {
            $questionType = QuestionType::READ_TEXT;
          }

          $question = new Question($questionId, $questId, $questionData['text'], $questionType);
          $question->setOptions($options);
          $questions[] = $question;
        }
      }


      $quizData = array(
        "title" => $this->request->post("quizTitle"),
        "description" => $this->request->post("quizDescription"),
        "requiredWallet" => $this->request->post("requiredWallet"),
        "timeRequired" => $this->request->post("timeRequired"),
        "expiryDate" => $this->request->post("expiryDate"),
        "participantsLimit" => $this->request->post("participantsLimit"),
        "poolAmount" => $this->request->post("poolAmount"),
        "token" => $this->request->post("token"),
      );

      $this->validateQuizData($quizData);

      $quest = new Quest(
        $questId ?? 0,
        $quizData['title'],
        $quizData['description'],
        0,
        $quizData['requiredWallet'],
        $quizData['timeRequired'],
        $quizData['expiryDate'],
        0,
        $quizData['participantsLimit'],
        $quizData['poolAmount'],
        $quizData['token'],
        0,
        SessionService::get('user')['id'],
        false
      );

      $quest->setQuestions($questions);

      if ($questId) {
        $this->questRepository->updateQuest($quest);
        $currentQuestions = $this->questionsRepository->getQuestionsByQuestId($questId);

        $questionsToUpdate = [];
        $questionsToDelete = [];
        $questionsToAdd = [];



        $currentQuestionMap = array_reduce($currentQuestions, function ($acc, $question) {
          $acc[$question->getQuestionId()] = $question;
          return $acc;
        }, []);

        foreach ($questions as $question) {
          if ($question->getQuestionId() < 200) {
            $questionsToAdd[] = $question;
            continue;
          }

          $currentQuestion = $currentQuestionMap[$question->getQuestionId()] ?? null;
          unset($currentQuestionMap[$question->getQuestionId()]);


          if (!$currentQuestion->__equals($question)) {
            $questionsToUpdate[] = $question;
          }
        }

        $questionsToDelete = array_values($currentQuestionMap);


        foreach ($questionsToDelete as $question) {
          $this->optionsRepository->deleteAllOptions($question->getQuestionId());
        }

        $this->questionsRepository->deleteQuestions($questionsToDelete);
        $this->questionsRepository->updateQuestions($questionsToUpdate);

        foreach ($questionsToAdd as $question) {
          $options = $question->getOptions();
          $questionId = $this->questionsRepository->saveQuestion($question);

          $this->optionsRepository->saveNewOptions($questionId, $options);
        }

        foreach ($questionsToUpdate as $question) {
          $optionsToAdd = [];
          $optionsToDelete = [];
          $optionsToUpdate = [];

          $currentOptions = $this->optionsRepository->getOptionsByQuestionId($question->getQuestionId());
          $options = $question->getOptions();

          $currentOptionsMap = array_reduce($currentOptions, function ($acc, $option) {
            $acc[$option->getOptionId()] = $option;
            return $acc;
          }, []);


          foreach ($options as $option) {
            $optionId = $option->getOptionId();

            if ($optionId < 400) {
              $optionsToAdd[] = $option;
              continue;
            }

            $currentOption = $currentOptionsMap[$optionId] ?? null;
            unset($currentOptionsMap[$optionId]);

            if (!$currentOption->__equals($option)) {
              $optionsToUpdate[] = $option;
            }
          }

          $optionsToDelete = array_values($currentOptionsMap);

          $this->optionsRepository->saveNewOptions($questionId, $optionsToAdd);
          $this->optionsRepository->deleteOptions($optionsToDelete);
          $this->optionsRepository->updateOptions($optionsToUpdate);
        }
      } else {
        $id = $this->questRepository->saveQuest($quest);

        foreach ($quest->getQuestions() as $question) {
          $question->setQuestId($id);
          $questionId = $this->questionsRepository->saveQuestion($question);
          $options = $question->getOptions();
          $this->optionsRepository->saveNewOptions($questionId, $options);
        }
      }
    } catch (NotLoggedInException $e) {
      $this->redirectWithParams('login', ['message' => 'first, you need to log in']);
    } catch (ValidationException $e) {
      $this->redirectWithParams('createQuest/' . $questId, ['messages' => explode(';', $e->getMessage())]);
    }

    $this->redirect('createQuest/' . $questId);
  }

  public function enterQuest($questId)
  {
    try {
      $this->questAuthorizationService->authorizeQuestAction(QuestAuthorizeRequest::ENTER, $questId);

      $userId = $this->questAuthorizationService->getSignedUserId();

      if ($this->request->isGet()) {
        $this->renderStartQuest($userId, $questId);
      } else {
        $this->startQuest($userId, $questId);
      }

    } catch (NotLoggedInException $e) {
      $this->redirectWithParams('login', ['message' => 'first, you need to log in']);
    } catch (Exception $e) {
      $this->redirectWithParams('error', ['message' => $e->getMessage()]);
    }
  }

  public function dashboard()
  {
    try {
      // $this->questAuthorizationService->authorizeQuestAction(QuestAuthorizeRequest::DASHBOARD);

      // $quests = $this->questRepository->getApprovedQuests();

      $id = $this->questAuthorizationService->getSignedUserId();
      $user = $this->userRepository->getUserById($id);

      $joinDate = DateTime::createFromFormat('Y-m-d', $user->getJoinDate())->format('F Y');

      $this->render('layout', ['title' => 'dashboard', 'username' => $user->getName(), 'joinDate' => $joinDate, 'points' => 4525], 'dashboard');

      $joinDate = $this->userRepository;//->//($id);
    } catch (NotLoggedInException $e) {
      $this->redirectWithParams('login', ['message' => 'first, you need to log in']);
    } catch (Exception $e) {
      $this->redirectWithParams('error', ['message' => $e->getMessage()]);
    }
  }

  public function createdQuests()
  {
    try {
      $this->questAuthorizationService->authorizeQuestAction(QuestAuthorizeRequest::CREATE);

      $creatorId = $this->questAuthorizationService->getSignedUserId();
      ;

      if (!$creatorId) {
        throw new NotFoundException('temporary');
      }
      $quests = $this->questRepository->getCreatorQuests($creatorId);


      array_filter($quests, function ($quest) use ($creatorId) {
        return !$quest->isApproved();
      });

      $this->render('layout', ['quests' => $quests], 'createdQuests');
    } catch (NotLoggedInException $e) {
      $this->redirectWithParams('login', ['message' => 'first, you need to log in']);
    } catch (Exception $e) {
      $this->redirectWithParams('error', ['message' => $e->getMessage()]);
    }
  }

  public function publish(int $questId)
  {
    try {
      $this->questAuthorizationService->authorizeQuestAction(QuestAuthorizeRequest::PUBLISH, $questId);

      $this->questRepository->approve($questId);
    } catch (NotLoggedInException $e) {
      $this->redirectWithParams('login', ['message' => 'first, you need to log in']);
    } catch (Exception $e) {
      $this->redirectWithParams('error', ['message' => $e->getMessage()]);
    }
  }

  private function renderStartQuest($userId, $questId)
  {
    $quest = $this->questRepository->getQuestById($questId);
    $wallets = $this->walletRepository->getBlockchainWallets($userId, $quest->getRequiredWallet());

    $this->render('enterQuest', ['title' => 'enter quest', 'wallets' => $wallets]);
  }

  private function startQuest($userId, $questId)
  {
    $walletSelect = $this->request->post('walletSelect');

    if (!$walletSelect) {
      $this->redirectWithParams('error', ['message' => 'something went wrong', 'code' => 404]);
    }

    $walletId = $walletSelect;

    if ($walletSelect === 'new') {
      $newWalletAddress = $this->request->post('newWalletAddress');

      if (!$newWalletAddress) {
        $this->redirectWithParams('error', ['message' => 'something went wrong', 'code' => 404]);
      }

      $walletId = $this->addNewWallet($userId, $questId, $newWalletAddress);
    }

    $this->startQuestWithWallet($walletId, $questId);
  }


  private function startQuestWithWallet($walletId, $questId)
  {
    $quest = $this->questRepository->getQuestById($questId);

    SessionService::set('walletId', $walletId);
    SessionService::set('questId', $questId);
    SessionService::set('questPoints', $quest->getPoints());

    $this->redirect('gameplay');
  }

  private function addNewWallet($userId, $questId, $walletAddress): int
  {
    $quest = $this->questRepository->getQuestById($questId);
    $wallet = new Wallet(0, $userId, $quest->getRequiredWallet(), $walletAddress, date('Y-m-d'), date('Y-m-d'));
    $walletId = $this->walletRepository->addWallet($wallet);

    return $walletId;
  }
}