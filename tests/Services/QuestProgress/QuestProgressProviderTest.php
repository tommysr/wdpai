<?php

use App\Models\Quest;
use PHPUnit\Framework\TestCase;
use App\Services\QuestProgress\QuestProgressProvider;
use App\Models\Interfaces\IQuestProgress;
use App\Models\QuestState;
use App\Repository\QuestProgress\IQuestProgressRepository;
use App\Services\Quests\IQuestProvider;
use App\Services\Session\ISessionService;

class QuestProgressProviderTest extends TestCase
{
  private $sessionService;
  private $questProgressRepository;
  private $questProvider;
  private $questProgressProvider;

  protected function setUp(): void
  {
    $this->sessionService = $this->createMock(ISessionService::class);
    $this->questProgressRepository = $this->createMock(IQuestProgressRepository::class);
    $this->questProvider = $this->createMock(IQuestProvider::class);

    $this->questProgressProvider = new QuestProgressProvider(
      $this->sessionService,
      $this->questProgressRepository,
      $this->questProvider
    );
  }

  public function testIsQuestPlayedReturnsTrue()
  {
    $this->questProgressRepository->method('getQuestProgress')->willReturn($this->createMock(IQuestProgress::class));

    $this->assertTrue($this->questProgressProvider->isQuestPlayed(1, 1));
  }

  public function testIsQuestPlayedReturnsFalse()
  {
    $this->questProgressRepository->method('getQuestProgress')->willReturn(null);

    $this->assertFalse($this->questProgressProvider->isQuestPlayed(1, 1));
  }

  public function testGetResponsesCount()
  {
    $this->questProgressRepository->method('getResponsesCount')->willReturn(5);

    $this->assertEquals(5, $this->questProgressProvider->getResponsesCount(1));
  }

  public function testGetQuestSummaryReturnsEmptyArrayWhenNoProgress()
  {
    $this->questProgressRepository->method('getQuestProgress')->willReturn(null);

    $this->assertEmpty($this->questProgressProvider->getQuestSummary(1, 1));
  }

  public function testGetQuestSummaryReturnsSummary()
  {
    $questProgress = $this->createMock(IQuestProgress::class);
    $questProgress->method('getScore')->willReturn(80);

    $quest = $this->createMock(Quest::class);
    $quest->method('getMaxPoints')->willReturn(100);

    $this->questProgressRepository->method('getQuestProgress')->willReturn($questProgress);
    $this->questProvider->method('getQuest')->willReturn($quest);
    $this->questProgressRepository->method('getPercentileRank')->willReturn(90);

    $summary = $this->questProgressProvider->getQuestSummary(1, 1);

    $this->assertEquals([
      'score' => 80,
      'maxScore' => 100,
      'better_than' => 90,
    ], $summary);
  }

  public function testGetProgressReturnsSessionProgress()
  {
    $questProgress = $this->createMock(IQuestProgress::class);
    $this->sessionService->method('get')->willReturn($questProgress);

    $this->assertSame($questProgress, $this->questProgressProvider->getProgress(1, 1));
  }

  public function testGetProgressReturnsRepositoryProgress()
  {
    $questProgress = $this->createMock(IQuestProgress::class);
    $this->sessionService->method('get')->willReturn(null);
    $this->questProgressRepository->method('getQuestProgress')->willReturn($questProgress);

    $this->assertSame($questProgress, $this->questProgressProvider->getProgress(1, 1));
  }

  public function testGetCurrentProgressReturnsSessionProgress()
  {
    $questProgress = $this->createMock(IQuestProgress::class);
    $this->sessionService->method('get')->willReturn($questProgress);

    $this->assertSame($questProgress, $this->questProgressProvider->getCurrentProgress());
  }

  public function testGetCompletedWallets()
  {
    $progress1 = $this->createMock(IQuestProgress::class);
    $progress1->method('getState')->willReturn(QuestState::Rated);
    $progress1->method('getScore')->willReturn(90);
    $progress1->method('getWalletAddress')->willReturn('wallet1');

    $progress2 = $this->createMock(IQuestProgress::class);
    $progress2->method('getState')->willReturn(QuestState::Rated);
    $progress2->method('getScore')->willReturn(95);
    $progress2->method('getWalletAddress')->willReturn('wallet2');

    $this->questProgressRepository->method('getAllProgresses')->willReturn([$progress1, $progress2]);

    $wallets = $this->questProgressProvider->getCompletedWallets(1);

    $this->assertEquals(['wallet1', 'wallet2'], $wallets);
  }

  public function testGetUserQuests()
  {
    $progress1 = $this->createMock(IQuestProgress::class);
    $quest1 = $this->createMock(Quest::class);

    $progress2 = $this->createMock(IQuestProgress::class);
    $quest2 = $this->createMock(Quest::class);

    $this->questProgressRepository->method('getUserEntries')->willReturn ([$progress1, $progress2]);

    $this->questProvider->method('getQuest')
      ->will($this->returnValueMap([
        [$progress1->getQuestId(), $quest1],
        [$progress2->getQuestId(), $quest2],
      ]));

    $userQuests = $this->questProgressProvider->getUserQuests(1);

    $this->assertCount(2, $userQuests);
    $this->assertEquals($quest1, $userQuests[0]['quest']);
    $this->assertEquals($progress1, $userQuests[0]['progress']);
    $this->assertEquals($quest2, $userQuests[1]['quest']);
    $this->assertEquals($progress2, $userQuests[1]['progress']);
  }
}
