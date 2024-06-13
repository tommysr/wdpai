<?php

use App\Models\IQuestion;
use PHPUnit\Framework\TestCase;
use App\Services\Quests\QuestProvider;
use App\Repository\IQuestRepository;
use App\Services\Question\IQuestionService;
use App\Repository\IWalletRepository;
use App\Models\IQuest;
use App\Services\Authenticate\IIdentity;

class QuestProviderTest extends TestCase
{
  private $questRepository;
  private $questionService;
  private $walletRepository;
  private $questProvider;

  protected function setUp(): void
  {
    $this->questRepository = $this->createMock(IQuestRepository::class);
    $this->questionService = $this->createMock(IQuestionService::class);
    $this->walletRepository = $this->createMock(IWalletRepository::class);

    $this->questProvider = new QuestProvider(
      $this->questRepository,
      $this->questionService,
      $this->walletRepository
    );
  }

  public function testGetQuestsToApproval()
  {
    $quests = [$this->createMock(IQuest::class)];

    $this->questRepository->expects($this->once())
      ->method('getQuestToApprove')
      ->willReturn($quests);

    $this->assertSame($quests, $this->questProvider->getQuestsToApproval());
  }

  public function testGetApprovedQuests()
  {
    $quests = [$this->createMock(IQuest::class)];

    $this->questRepository->expects($this->once())
      ->method('getApprovedQuests')
      ->willReturn($quests);

    $this->assertSame($quests, $this->questProvider->getApprovedQuests());
  }

  public function testGetQuestsByIdsWithEmptyArray()
  {
    $questIds = [];

    $this->questRepository->expects($this->never())
      ->method('getQuestById');

    $result = $this->questProvider->getQuestsByIds($questIds);

    $this->assertEquals([], $result);
  }

  public function testGetQuestsToPlay()
  {
    $quest1 = $this->createMock(IQuest::class);
    $quest1->method('getParticipantsCount')->willReturn(5);
    $quest1->method('getParticipantsLimit')->willReturn(10);
    $quest1->method('getExpiryDateString')->willReturn((new \DateTime('+1 day'))->format('Y-m-d'));

    $quest2 = $this->createMock(IQuest::class);
    $quest2->method('getParticipantsCount')->willReturn(15);
    $quest2->method('getParticipantsLimit')->willReturn(10);
    $quest2->method('getExpiryDateString')->willReturn((new \DateTime('+1 day'))->format('Y-m-d'));

    $this->questRepository->method('getApprovedQuests')->willReturn([$quest1, $quest2]);

    $this->assertSame([$quest1], $this->questProvider->getQuestsToPlay());
  }

  public function testGetTopRatedQuests()
  {
    $quest1 = $this->createMock(IQuest::class);
    $quest1->method('getAvgRating')->willReturn(4.5);

    $quest2 = $this->createMock(IQuest::class);
    $quest2->method('getAvgRating')->willReturn(4.7);

    $this->questRepository->method('getApprovedQuests')->willReturn([$quest1, $quest2]);

    $this->assertSame([$quest2, $quest1], $this->questProvider->getTopRatedQuests());
  }

  public function testGetCreatorQuests()
  {
    $identity = $this->createMock(IIdentity::class);
    $identity->method('getId')->willReturn(1);

    $quests = [$this->createMock(IQuest::class)];

    $this->questRepository->expects($this->once())
      ->method('getCreatorQuests')
      ->with(1)
      ->willReturn($quests);

    $this->assertSame($quests, $this->questProvider->getCreatorQuests($identity));
  }

  public function testGetQuestWithQuestions()
  {
    $quest = $this->createMock(IQuest::class);
    $questions = [$this->createMock(IQuestion::class)];

    $this->questRepository->method('getQuestById')->willReturn($quest);
    $this->questionService->method('fetchQuestions')->willReturn($questions);

    $quest->expects($this->once())->method('setQuestions')->with($questions);

    $this->assertSame($quest, $this->questProvider->getQuestWithQuestions(1));
  }

  public function testGetQuestWithQuestionsReturnsNullIfQuestNotFound()
  {
    $this->questRepository->method('getQuestById')->willReturn(null);

    $this->assertNull($this->questProvider->getQuestWithQuestions(1));
  }

  public function testGetQuest()
  {
    $quest = $this->createMock(IQuest::class);

    $this->questRepository->expects($this->once())
      ->method('getQuestById')
      ->with(1)
      ->willReturn($quest);

    $this->assertSame($quest, $this->questProvider->getQuest(1));
  }
}
