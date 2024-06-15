<?php

use PHPUnit\Framework\TestCase;
use App\Services\Quests\Builder\QuestBuilder;
use App\Models\Quest;
use App\Models\IQuest;
use App\Models\IQuestion;

class QuestBuilderTest extends TestCase
{
  private $questBuilder;

  protected function setUp(): void
  {
    $this->questBuilder = new QuestBuilder();
  }

  public function testReset()
  {
    $this->questBuilder->reset();
    $quest = $this->questBuilder->build();

    $this->assertInstanceOf(IQuest::class, $quest);
    $this->assertSame(0, $quest->getQuestID());
    $this->assertSame('', $quest->getTitle());
    $this->assertSame('', $quest->getDescription());
    $this->assertSame(0, $quest->getRequiredMinutes());
    $this->assertSame('', $quest->getBlockchain());
    $this->assertSame(0, $quest->getParticipantsLimit());
    $this->assertSame(0.0, $quest->getPoolAmount());
    $this->assertSame('', $quest->getToken());
    $this->assertSame('', $quest->getPayoutDate());
    $this->assertFalse($quest->getIsApproved());
    $this->assertEmpty($quest->getQuestions());
  }

  public function testSetQuestId()
  {
    $this->questBuilder->setQuestId(1);
    $quest = $this->questBuilder->build();

    $this->assertSame(1, $quest->getQuestID());
  }

  public function testSetTitle()
  {
    $this->questBuilder->setTitle('Test Quest');
    $quest = $this->questBuilder->build();

    $this->assertSame('Test Quest', $quest->getTitle());
  }

  public function testSetDescription()
  {
    $this->questBuilder->setDescription('This is a test description.');
    $quest = $this->questBuilder->build();

    $this->assertSame('This is a test description.', $quest->getDescription());
  }

  public function testSetBlockchain()
  {
    $this->questBuilder->setBlockchain('Ethereum');
    $quest = $this->questBuilder->build();

    $this->assertSame('Ethereum', $quest->getBlockchain());
  }

  public function testSetRequiredMinutes()
  {
    $this->questBuilder->setRequiredMinutes(60);
    $quest = $this->questBuilder->build();

    $this->assertSame(60, $quest->getRequiredMinutes());
  }

  public function testSetExpiryDateString()
  {
    $this->questBuilder->setExpiryDateString('2024-12-31');
    $quest = $this->questBuilder->build();

    $this->assertSame('2024-12-31', $quest->getExpiryDateString());
  }

  public function testSetParticipantsLimit()
  {
    $this->questBuilder->setParticipantsLimit(100);
    $quest = $this->questBuilder->build();

    $this->assertSame(100, $quest->getParticipantsLimit());
  }

  public function testSetPoolAmount()
  {
    $this->questBuilder->setPoolAmount(1000.50);
    $quest = $this->questBuilder->build();

    $this->assertSame(1000.50, $quest->getPoolAmount());
  }

  public function testSetToken()
  {
    $this->questBuilder->setToken('ETH');
    $quest = $this->questBuilder->build();

    $this->assertSame('ETH', $quest->getToken());
  }

  public function testSetCreatorId()
  {
    $this->questBuilder->setCreatorId(2);
    $quest = $this->questBuilder->build();

    $this->assertSame(2, $quest->getCreatorId());
  }

  public function testSetPayoutDate()
  {
    $this->questBuilder->setPayoutDate('2024-12-31');
    $quest = $this->questBuilder->build();

    $this->assertSame('2024-12-31', $quest->getPayoutDate());
  }

  public function testSetIsApproved()
  {
    $this->questBuilder->setIsApproved(true);
    $quest = $this->questBuilder->build();

    $this->assertTrue($quest->getIsApproved());
  }

  public function testAddQuestion()
  {
    $question = $this->createMock(IQuestion::class);
    $this->questBuilder->addQuestion($question);
    $quest = $this->questBuilder->build();

    $this->assertCount(1, $quest->getQuestions());
    $this->assertSame($question, $quest->getQuestions()[0]);
  }

  public function testSetFlag()
  {
    $this->questBuilder->setFlag('test-flag');
    $quest = $this->questBuilder->build();

    $this->assertSame('test-flag', $quest->getFlag());
  }

  public function testSetPictureUrl()
  {
    $this->questBuilder->setPictureUrl('test-url.png');
    $quest = $this->questBuilder->build();

    $this->assertSame('test-url.png', $quest->getPictureUrl());
  }

  public function testBuild()
  {
    $this->questBuilder->setQuestId(1)
      ->setTitle('Test Quest')
      ->setDescription('This is a test description.')
      ->setBlockchain('Ethereum')
      ->setRequiredMinutes(60)
      ->setExpiryDateString('2024-12-31')
      ->setParticipantsLimit(100)
      ->setPoolAmount(1000.50)
      ->setToken('ETH')
      ->setCreatorId(2)
      ->setPayoutDate('2024-12-31')
      ->setIsApproved(true)
      ->setFlag('test-flag')
      ->setPictureUrl('test-url.png');

    $question = $this->createMock(IQuestion::class);
    $this->questBuilder->addQuestion($question);

    $quest = $this->questBuilder->build();

    $this->assertSame(1, $quest->getQuestID());
    $this->assertSame('Test Quest', $quest->getTitle());
    $this->assertSame('This is a test description.', $quest->getDescription());
    $this->assertSame('Ethereum', $quest->getBlockchain());
    $this->assertSame(60, $quest->getRequiredMinutes());
    $this->assertSame('2024-12-31', $quest->getExpiryDateString());
    $this->assertSame(100, $quest->getParticipantsLimit());
    $this->assertSame(1000.50, $quest->getPoolAmount());
    $this->assertSame('ETH', $quest->getToken());
    $this->assertSame(2, $quest->getCreatorId());
    $this->assertSame('2024-12-31', $quest->getPayoutDate());
    $this->assertTrue($quest->getIsApproved());
    $this->assertSame('test-flag', $quest->getFlag());
    $this->assertSame('test-url.png', $quest->getPictureUrl());
    $this->assertCount(1, $quest->getQuestions());
    $this->assertSame($question, $quest->getQuestions()[0]);
  }
}
