<?php

use App\Models\Quest;
use App\Models\Question;
use App\Services\QuestProgress\NotAuthorizedException;
use App\Services\QuestProgress\NotFoundException;
use PHPUnit\Framework\TestCase;
use App\Services\QuestProgress\QuestProgressManager;
use App\Models\QuestProgress;
use App\Models\QuestState;
use App\Repository\IQuestionsRepository;
use App\Repository\QuestProgress\IQuestProgressRepository;
use App\Services\Quests\IQuestProvider;
use App\Services\Quests\IQuestManager;
use App\Services\Session\ISessionService;
use App\Services\QuestProgress\IQuestProgressProvider;

class QuestProgressManagerTest extends TestCase
{
  private $sessionService;
  private $questProgressRepository;
  private $questionsRepository;
  private $questProvider;
  private $questManager;
  private $questProgressProvider;
  private $questProgressManager;

  protected function setUp(): void
  {
    $this->sessionService = $this->createMock(ISessionService::class);
    $this->questProgressRepository = $this->createMock(IQuestProgressRepository::class);
    $this->questionsRepository = $this->createMock(IQuestionsRepository::class);
    $this->questProvider = $this->createMock(IQuestProvider::class);
    $this->questManager = $this->createMock(IQuestManager::class);
    $this->questProgressProvider = $this->createMock(IQuestProgressProvider::class);

    $this->questProgressManager = new QuestProgressManager(
      $this->sessionService,
      $this->questProgressRepository,
      $this->questionsRepository,
      $this->questProvider,
      $this->questManager,
      $this->questProgressProvider
    );
  }

  public function testResetSession()
  {
    $this->sessionService->expects($this->once())
      ->method('delete')
      ->with('questProgress');

    $this->questProgressManager->resetSession();
  }

  public function testStartProgressQuestNotFound()
  {
    $this->questProvider->method('getQuest')->willReturn(null);

    $this->expectException(NotFoundException::class);
    $this->questProgressManager->startProgress(1, 1);
  }

  public function testStartProgressAddParticipantFails()
  {
    $quest = $this->createMock(Quest::class);
    $this->questProvider->method('getQuest')->willReturn($quest);
    $this->questManager->method('addParticipant')->willReturn(false);

    $this->expectException(NotAuthorizedException::class);
    $this->questProgressManager->startProgress(1, 1);
  }

  public function testStartProgressNextQuestionNotFound()
  {
    $quest = $this->createMock(Quest::class);
    $this->questProvider->method('getQuest')->willReturn($quest);
    $this->questManager->method('addParticipant')->willReturn(true);
    $this->questionsRepository->method('getNextQuestion')->willReturn(null);

    $this->expectException(NotFoundException::class);
    $this->questProgressManager->startProgress(1, 1);
  }

  public function testStartProgressSuccess()
  {
    $quest = $this->createMock(Quest::class);
    $question = $this->createMock(Question::class);
    $question->method('getQuestionId')->willReturn(1);

    $this->questProvider->method('getQuest')->willReturn($quest);
    $this->questManager->method('addParticipant')->willReturn(true);
    $this->questionsRepository->method('getNextQuestion')->willReturn($question);

    $this->questProgressRepository->expects($this->once())
      ->method('saveQuestProgress')
      ->with($this->isInstanceOf(QuestProgress::class));

    $this->sessionService->expects($this->once())
      ->method('set')
      ->with('questProgress', $this->isInstanceOf(QuestProgress::class));

    $this->questProgressManager->startProgress(1, 1);
  }

  public function testCompleteQuestNotFound()
  {
    $this->questProgressProvider->method('getCurrentProgress')->willReturn(null);

    $this->expectException(NotFoundException::class);
    $this->questProgressManager->completeQuest();
  }

  public function testCompleteQuestAlreadyCompleted()
  {
    $questProgress = $this->createMock(QuestProgress::class);
    $questProgress->method('getState')->willReturn(QuestState::Unrated);
    $questProgress->method('getCompletionDate')->willReturn(date('Y-m-d H:i:s'));

    $this->questProgressProvider->method('getCurrentProgress')->willReturn($questProgress);

    $this->expectException(NotAuthorizedException::class);
    $this->questProgressManager->completeQuest();
  }

  public function testCompleteQuestSuccess()
  {
    $questProgress = $this->createMock(QuestProgress::class);
    $questProgress->method('getState')->willReturn(QuestState::InProgress);
    $questProgress->method('getCompletionDate')->willReturn(null);

    $this->questProgressProvider->method('getCurrentProgress')->willReturn($questProgress);

    $questProgress->expects($this->once())->method('setCompletionDateToNow');
    $this->questProgressRepository->expects($this->once())->method('updateQuestProgress')->with($questProgress);
    $this->sessionService->expects($this->once())->method('delete')->with('questProgress');

    $this->questProgressManager->completeQuest();
  }

  public function testSetRatedNotFound()
  {
    $this->questProgressProvider->method('getCurrentProgress')->willReturn(null);

    $this->expectException(NotFoundException::class);
    $this->questProgressManager->setRated();
  }

  public function testSetRatedSuccess()
  {
    $questProgress = $this->createMock(QuestProgress::class);

    $this->questProgressProvider->method('getCurrentProgress')->willReturn($questProgress);

    $questProgress->expects($this->once())->method('setState')->with(QuestState::Rated);
    $this->sessionService->expects($this->once())->method('set')->with('questProgress', $questProgress);
    $this->questProgressRepository->expects($this->once())->method('updateQuestProgress')->with($questProgress);

    $this->questProgressManager->setRated();
  }

  public function testAddPointsNotFound()
  {
    $this->questProgressProvider->method('getCurrentProgress')->willReturn(null);

    $this->expectException(NotFoundException::class);
    $this->questProgressManager->addPoints(10);
  }

  public function testAddPointsSuccess()
  {
    $questProgress = $this->createMock(QuestProgress::class);
    $questProgress->method('getScore')->willReturn(10);

    $this->questProgressProvider->method('getCurrentProgress')->willReturn($questProgress);

    $questProgress->expects($this->once())->method('setScore')->with(20);
    $this->sessionService->expects($this->once())->method('set')->with('questProgress', $questProgress);
    $this->questProgressRepository->expects($this->once())->method('updateQuestProgress')->with($questProgress);

    $this->questProgressManager->addPoints(10);
  }

  public function testRecordResponsesSuccess()
  {
    $userId = 1;
    $selectedOptionsIds = [1, 2, 3];

    $this->questProgressRepository->expects($this->once())
      ->method('saveResponses')
      ->with($userId, $selectedOptionsIds);

    $this->questProgressManager->recordResponses($userId, $selectedOptionsIds);
  }

  public function testChangeProgressNotFound()
  {
    $this->questProgressProvider->method('getCurrentProgress')->willReturn(null);

    $this->expectException(NotFoundException::class);
    $this->questProgressManager->changeProgress(1);
  }

  public function testChangeProgressSuccess()
  {
    $questProgress = $this->createMock(QuestProgress::class);
    $questProgress->method('getQuestId')->willReturn(1);
    $questProgress->method('getLastQuestionId')->willReturn(1);

    $this->questProgressProvider->method('getCurrentProgress')->willReturn($questProgress);
    $this->questionsRepository->method('getNextQuestionId')->willReturn(2);

    $questProgress->expects($this->once())->method('setNextQuestionId')->with(2);
    $this->sessionService->expects($this->once())->method('set')->with('questProgress', $questProgress);
    $this->questProgressRepository->expects($this->once())->method('updateQuestProgress')->with($questProgress);

    $this->questProgressManager->changeProgress(1);
  }

  public function testChangeProgressNoNextQuestion()
  {
    $questProgress = $this->createMock(QuestProgress::class);
    $questProgress->method('getQuestId')->willReturn(1);
    $questProgress->method('getLastQuestionId')->willReturn(1);

    $this->questProgressProvider->method('getCurrentProgress')->willReturn($questProgress);
    $this->questionsRepository->method('getNextQuestionId')->willReturn(null);

    $questProgress->expects($this->once())->method('setState')->with(QuestState::Unrated);
    $this->sessionService->expects($this->once())->method('set')->with('questProgress', $questProgress);
    $this->questProgressRepository->expects($this->once())->method('updateQuestProgress')->with($questProgress);

    $this->questProgressManager->changeProgress(1);
  }

  public function testAbandonQuest()
  {
    $questProgress = $this->createMock(QuestProgress::class);

    $this->questProgressProvider->method('getCurrentProgress')->willReturn($questProgress);

    $questProgress->expects($this->once())->method('setState')->with(QuestState::Abandoned);
    $this->questProgressRepository->expects($this->once())->method('updateQuestProgress')->with($questProgress);
    $this->sessionService->expects($this->once())->method('delete')->with('questProgress');

    $this->questProgressManager->abandonQuest();
  }
}
