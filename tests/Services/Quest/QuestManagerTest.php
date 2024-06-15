<?php

use App\Models\Quest;
use PHPUnit\Framework\TestCase;
use App\Services\Quests\QuestManager;
use App\Services\Question\IQuestionService;
use App\Repository\IQuestRepository;
use App\Models\IQuest;

class QuestManagerTest extends TestCase
{
  public function testEditQuest()
  {
    // Create mock objects for dependencies
    $questRepository = $this->createMock(IQuestRepository::class);
    $questionService = $this->createMock(IQuestionService::class);

    // Set up expectations for the mock objects
    $questRepository->expects($this->once())
      ->method('updateQuest')
      ->with($this->isInstanceOf(IQuest::class));
    $questionService->expects($this->once())
      ->method('updateQuestions')
      ->with($this->isInstanceOf(IQuest::class));

    // Create an instance of QuestManager with the mock objects
    $questManager = new QuestManager($questRepository, $questionService);

    // Call the method being tested
    $quest = $this->createMock(IQuest::class);
    $questManager->editQuest($quest);
  }

  public function testCreateQuest()
  {
    // Create mock objects for dependencies
    $questRepository = $this->createMock(IQuestRepository::class);
    $questionService = $this->createMock(IQuestionService::class);

    // Set up expectations for the mock objects
    $questRepository->expects($this->once())
      ->method('saveQuest')
      ->willReturn(123); // Return a quest ID
    $questionService->expects($this->once())
      ->method('updateQuestions')
      ->with($this->isInstanceOf(IQuest::class));

    // Create an instance of QuestManager with the mock objects
    $questManager = new QuestManager($questRepository, $questionService);

    // Call the method being tested
    $quest = $this->createMock(IQuest::class);

    $quest = new Quest(0, '', '', 0, '', 0, '', 0, 0, 0, '', 0, false, '', 0, '', '');

    $questManager->createQuest($quest);

    // Assert that the quest ID is set correctly
    $this->assertEquals(123, $quest->getQuestID());
  }

  public function testAddParticipant()
  {
    // Create a mock object for the quest repository
    $questRepository = $this->createMock(IQuestRepository::class);

    // Create an instance of QuestManager with the mock object
    $questManager = new QuestManager($questRepository, $this->createMock(IQuestionService::class));

    $quest = $this->createMock(IQuest::class);

    $quest->expects($this->once())
      ->method('getParticipantsCount')
      ->willReturn(1);

    $quest->expects($this->once())
      ->method('getParticipantsLimit')
      ->willReturn(4);

    // Set up expectations for the mock object
    $questRepository->expects($this->once())
      ->method('getQuestById')
      ->with(123)
      ->willReturn($quest);

    // Call the method being tested
    $result = $questManager->addParticipant(123);

    // Assert that the result is true
    $this->assertTrue($result);
  }

  public function testPublishQuest()
  {
    // Create a mock object for the quest repository
    $questRepository = $this->createMock(IQuestRepository::class);

    // Create an instance of QuestManager with the mock object
    $questManager = new QuestManager($questRepository, $this->createMock(IQuestionService::class));

    // Set up expectations for the mock object
    $questRepository->expects($this->once())
      ->method('changeApproved')
      ->with(123, true);

    // Call the method being tested
    $questManager->publishQuest(123);
  }

  public function testUnpublishQuest()
  {
    // Create a mock object for the quest repository
    $questRepository = $this->createMock(IQuestRepository::class);

    // Create an instance of QuestManager with the mock object
    $questManager = new QuestManager($questRepository, $this->createMock(IQuestionService::class));

    // Set up expectations for the mock object
    $questRepository->expects($this->once())
      ->method('changeApproved')
      ->with(123, false);

    // Call the method being tested
    $questManager->unpublishQuest(123);
  }
}