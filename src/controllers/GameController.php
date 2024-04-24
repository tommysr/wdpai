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

  public function gameplay()
  {
    session_start();

    $userId = $_SESSION['user_id'];
    $questId = $_SESSION['quest_id'];
    $currentQuestionId = $_SESSION['current_question'];

    if (!isset($userId)) {
      return $this->redirectToLogin();
    }

    if (!$this->questAuthorizationService->isUserAuthorized($userId, $questId)) {
      return $this->redirectToUnauthorized();
    }

    $questions = $this->questionsRepository->getQuestionsByQuestId($questId);
    $questions_count = count($questions);

    if ($questions_count === 0) {
      throw new NoQuestionsException('no questions in database');
    }

    if (!isset($_SESSION['questions_count'])) {
      $_SESSION['questions_count'] = $questions_count;
    }

    $this->renderQuestion($questions[$currentQuestionId]);
  }

  public function processUserResponse($questionId)
  {
    $optionId = [$_POST['option']] ?? [];

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
    $overallMaxScore = $_SESSION['max_score'];
    $questionsCount = $_SESSION['questions_count'];
    $correctAnswers = $_SESSION['correct_answers'];


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

  public function nextQuestion()
  {
    session_start();

    $questId = $_SESSION['quest_id'];
    $currentQuestionId = $_SESSION['current_question'];
    $questions = $this->questionsRepository->getQuestionsByQuestId($questId);
    $questions_count = count($questions);

    if ($currentQuestionId + 1 === $questions_count) {
      return $this->renderQuestSummary();
    }

    $_SESSION['current_question'] = $currentQuestionId + 1;
    $this->renderQuestion($questions[$currentQuestionId + 1]);
  }


  private function renderQuestSummary()
  {
    $this->render('questSummary');
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
    $this->render('multipleChoiceQuestion', ['question' => $question, 'options' => $options]);
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


