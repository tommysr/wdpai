<?php

require_once "AppController.php";
require_once __DIR__ . '/../services/GameService.php';
require_once __DIR__ . '/../services/QuestAuthorizationService.php';
require_once __DIR__ . '/../repository/QuestionsRepository.php';
require_once __DIR__ . '/../repository/OptionsRepository.php';
require_once __DIR__ . '/../exceptions/Quests.php';


class GameController extends AppController
{
  private $gameService;
  private $questionsRepository;
  private $optionsRepository;
  private $questAuthorizationService;


  public function __construct()
  {
    parent::__construct();
    $this->gameService = new GameService();
    $this->questionsRepository = new QuestionsRepository();
    $this->questAuthorizationService = new QuestAuthorizationService();
    $this->optionsRepository = new OptionsRepository();
  }

  public function gameplay($questId)
  {
    session_start();

    $userId = $_SESSION['user_id'];
    $currentQuestionId = $_SESSION['current_question'];

    if (!isset($userId)) {
      return $this->redirectToLogin();
    }

    if (!$this->questAuthorizationService->isUserAuthorized($userId, $questId)) {
      return $this->redirectToUnauthorized();
    }

    if (!isset($currentQuestionId)) {
      $_SESSION['current_question'] = 0;
    }



    $questions = $this->questionsRepository->getQuestionsByQuestId($questId);
    $questions_count = count($questions);

    if ($questions_count === 0) {
      throw new NoQuestionsException('no questions in database');
    }

    if (!isset($_SESSION['questions-count'])) {
      $_SESSION['questions-count'] = $questions_count;
    }

    $this->renderQuestion($questions[$currentQuestionId]);
  }

  public function processUserResponse($questionId)
  {
    $optionId = [$_POST['option']];

    if (!isset($_SESSION['userScore'])) {
      $_SESSION['user-score'] = 0;
    }

    $result = $this->gameService->processUserResponse($questionId, $optionId);
    $_SESSION['user-score'] += $result['score'];
    $_SESSION['max-score'] += $result['maxScore'];

    if ($result['correctPercentage'] > 75) {
      $_SESSION['correct-answers'] += 1;
    }


    $this->renderQuestionSummary($result['score'], $result['maxScore'], $result['stars']);
  }

  private function renderQuestionSummary(int $questionScore, int $questionMaxScore, int $stars)
  {
    $overallScore = $_SESSION['user-score'];
    $overallMaxScore = $_SESSION['max-score'];
    $questionsCount = $_SESSION['questions-count'];
    $correctAnswers = $_SESSION['correct-answers'];


    $this->render('questionSummary', [
      'stars' => $stars,
      'score' => $questionScore,
      'maxScore' => $questionMaxScore,
      'overallScore' => $overallScore,
      'overallMaxScore' => $overallMaxScore,
      'correctAnswers' => $correctAnswers,
      'questionsCount' => $questionsCount
    ]);
  }

  private function renderQuestion(Question $question)
  {
    $question_type = $question->getType();
    $options = $this->optionsRepository->getOptionsByQuestionId($question->getQuestionId());

    switch ($question_type->getValue()) {
      case QuestionType::SINGLE_CHOICE:
        $this->renderSingleChoiceQuestion($question, $options);
        break;
      case QuestionType::MULTIPLE_CHOICE:
        $this->renderMultipleChoiceQuestion($question, $options);
        break;
      default:
        $this->renderReadTextQuestion($question);
    }
  }

  private function renderSingleChoiceQuestion($question, $options)
  {
    $this->render('singleChoiceQuestion', ['question' => $question, 'options' => $options]);
  }

  private function renderMultipleChoiceQuestion($question, $options)
  {
    echo "multiple choice";
  }

  private function renderReadTextQuestion($question)
  {
    echo "text read";
  }


  private function redirectToUnauthorized()
  {
    $url = "http://$_SERVER[HTTP_HOST]";
    header("Location: {$url}/unauthorized");
  }


  private function redirectToLogin()
  {
    $url = "http://$_SERVER[HTTP_HOST]";
    header("Location: {$url}/login");
  }
}


