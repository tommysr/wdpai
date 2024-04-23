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
    $currentQuestion = $_SESSION['current_question'] ? $_SESSION['current_question'] + 1 : 0;

    if (!isset($userId)) {
      return $this->redirectToLogin();
    }

    if (!$this->questAuthorizationService->isUserAuthorized($userId, $questId)) {
      return $this->redirectToUnauthorized();
    }

    $questions = $this->questionsRepository->getQuestionsByQuestId($questId);

    if (count($questions) === 0) {
      throw new NoQuestionsException('no questions in database');
    }

    $this->renderQuestion($questions[$currentQuestion]);
  }

  public function processUserResponse($questionID, $selectedOptions)
  {
    // // Check user's response against the correct answer (if applicable)
    // // Update user's score and store their response

    // // Determine next question (e.g., get next question from database)
    // $nextQuestion = $this->questionRepository->getNextQuestion($questionID);

    // if ($nextQuestion) {
    //     // Render next question view
    //     $this->renderQuestion($nextQuestion);
    // } else {
    //     // End quiz
    //     $this->endQuiz();
    // }
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
    $this->render('singleChoiceQuestion', ['question' => $question,'options'=> $options]);
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


