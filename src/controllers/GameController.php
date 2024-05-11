<?php

require_once "AppController.php";
require_once __DIR__ . '/../services/GameService.php';
require_once __DIR__ . '/../services/QuestAuthorizationService.php';
require_once __DIR__ . '/../services/SessionService.php';
require_once __DIR__ . '/../repository/QuestionsRepository.php';
require_once __DIR__ . '/../repository/QuestStatisticsRepository.php';
require_once __DIR__ . '/../repository/OptionsRepository.php';
require_once __DIR__ . '/../exceptions/Quests.php';


class GameController extends AppController
{
  private $gameService;
  private $questionsRepository;
  private $optionsRepository;
  private $questAuthorizationService;
  private $questStatisticsRepository;

  public function __construct()
  {
    parent::__construct();
    $this->gameService = new GameService();
    $this->questionsRepository = new QuestionsRepository();
    $this->questAuthorizationService = new QuestAuthorizationService();
    $this->optionsRepository = new OptionsRepository();
    $this->questStatisticsRepository = new QuestStatisticsRepository();
  }

  private function getStateFromSession(): ?QuestStatistics
  {
    $stateFromSession = SessionService::get('gameplayState');

    if (!$stateFromSession) {
      return null;
    }

    return QuestStatistics::fromArray($stateFromSession);
  }

  private function getUserGameplayState(int $userId, int $questId): ?QuestStatistics
  {
    $sessionState = $this->getStateFromSession();

    if ($sessionState) {
      return $sessionState;
    }

    return $this->questStatisticsRepository->getQuestStatistic($userId, $questId);
  }

  public function gameplay(int $questId)
  {
    try {
      $userId = $this->questAuthorizationService->authorizeQuestAction(QuestAuthorizeRequest::PLAY, $questId);

      $gameplayState = $this->getUserGameplayState($userId, $questId);

      if ($gameplayState) {
        $this->resumeGameplay($userId, $gameplayState);
      } else {
        $walletId = $this->request->query('walletId');

        if (!$walletId) {
          throw new Exception('Wallet ID is required to start a new gameplay');
        }

        $this->startNewGameplay($userId, $questId, $walletId);
      }
    } catch (NotLoggedInException $e) {
      $this->redirectWithParams('login', ['message' => 'You need to be logged in to play']);
    } catch (GameplayInProgressException $id) {
      $this->redirect('gameplay/' . $id->getMessage());
    } catch (Exception $e) {
      $this->redirectWithParams('error', ['message' => $e->getMessage()]);
    }
  }

  private function resumeGameplay(int $userId, QuestStatistics $gameplayState)
  {
    $questId = $gameplayState->getQuestId();
    $lastQuestionId = $gameplayState->getLastQuestionId();
    $questions = $this->questionsRepository->getQuestionsByQuestId($questId);

    $this->renderQuestion($questions[$lastQuestionId]);
  }

  private function startNewGameplay(int $userId, int $questId, int $walletId)
  {
    $this->questStatisticsRepository->addParticipation($userId, $questId, $walletId);
    $this->redirect('gameplay/' . $questId);
  }

  public function updateGameplayState(int $userId, int $questId, int $lastQuestionId, int $score)
  {
    // $this->questStatisticsRepository->updateGameplayState($userId, $questId, $lastQuestionId, $score);

    // Update user gameplay state in the database
    // Example query: UPDATE user_gameplay_state SET last_question_id = $lastQuestionId, score = $score WHERE user_id = $userId AND quest_id = $questId
  }



  //   $currentQuestionId = SessionService::get("currentQuestion") ?? 0;
  //   SessionService::set("currentQuestion", $currentQuestionId);

  //   $questions = $this->questionsRepository->getQuestionsByQuestId($questId);
  //   $questions_count = count($questions);

  //   if ($questions_count === 0) {
  //     throw new NoQuestionsException('no questions in database');
  //   }

  //   $this->renderQuestion($questions[$currentQuestionId]);
  // }

  public function processUserResponse($questionId)
  {
    if (!$_SESSION['awaiting_response']) {
      return;
    }

    $optionId = $_POST['option'] ? [$_POST['option']] : [];

    foreach ($_POST as $key => $value) {
      if (strpos($key, 'option') === 0) {
        $optionId[] = $value;
      }
    }

    if (!isset($_SESSION['user_score'])) {
      $_SESSION['user_score'] = 0;
    }

    $result = $this->gameService->processUserResponse($questionId, $optionId);

    $_SESSION['user_score'] += $result['score'];
    $_SESSION['max_score'] += $result['maxScore'];

    if ($result['correctPercentage'] > 75) {
      $_SESSION['correct_answers'] += 1;
    }

    $this->renderQuestionSummary($result['score'], $result['maxScore'], $result['stars']);
  }

  private function renderQuestionSummary(int $questionScore, int $questionMaxScore, int $stars)
  {
    $overallScore = $_SESSION['user_score'];
    $maxScoreUntilNow = $_SESSION['max_score'];
    $overallMaxScore = $_SESSION['quest_points'];

    $this->render('questionSummary', [
      'stars' => $stars,
      'score' => $questionScore,
      'maxScore' => $questionMaxScore,
      'overallScore' => $overallScore,
      'overallMaxScore' => $overallMaxScore,
      'maxScoreUntilNow' => $maxScoreUntilNow
    ]);
  }

  // public function nextQuestion()
  // {
  //   $questId = SessionService::get('questId');
  //   $currentQuestion = SessionService::get('currentQuestion');
  //   $nextQuestion = $currentQuestion + 1;


  //   $questions = $this->questionsRepository->getQuestionsByQuestId($questId);
  //   $questions_count = count($questions);

  //   if ($nextQuestion === $questions_count) {
  //     return $this->render('questSummary');
  //   }

  //   SessionService::set('currentQuestion', $nextQuestion);
  //   $this->renderQuestion($questions[$nextQuestion]);
  // }


  private function renderQuestion(Question $question)
  {
    $question_type = $question->getType();
    $options = $this->optionsRepository->getOptionsByQuestionId($question->getQuestionId());
    // $_SESSION['awaiting_response'] = true;


    switch ($question_type) {
      case QuestionType::SINGLE_CHOICE:
        $this->renderSingleChoiceQuestion($question, $options);
        break;
      case QuestionType::MULTIPLE_CHOICE:
        $this->renderMultipleChoiceQuestion($question, $options);
        break;
      default:
        // $_SESSION['awaiting_response'] = false;
        $this->renderReadTextQuestion($question);
    }
  }

  private function renderSingleChoiceQuestion($question, $options)
  {
    $this->render('singleChoiceQuestion', ['question' => $question, 'options' => $options]);
  }

  private function renderMultipleChoiceQuestion($question, $options)
  {
    $this->render('multipleChoiceQuestion', ['question' => $question, 'options' => $options]);
  }

  private function renderReadTextQuestion($question)
  {
    $this->render('readText', ['question' => $question]);
  }
}


