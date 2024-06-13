<?php

use PHPUnit\Framework\TestCase;
use App\Services\Quests\Builder\QuestBuilderService;
use App\Services\Quests\Builder\IQuestBuilder;
use App\Models\IQuest;
use App\Models\QuestionType;
use App\Models\Question;
use App\Models\Option;

class QuestBuilderServiceTest extends TestCase
{
  private $questBuilder;
  private $questBuilderService;

  protected function setUp(): void
  {
    $this->questBuilder = $this->createMock(IQuestBuilder::class);
    $this->questBuilderService = new QuestBuilderService($this->questBuilder);
  }

  public function testBuildQuest()
  {
    $data = [
      'questId' => '1',
      'title' => 'Sample Quest',
      'description' => 'This is a sample quest.',
      'blockchain' => 'Ethereum',
      'minutesRequired' => '10',
      'expiryDate' => '2024-12-31',
      'payoutDate' => '2024-12-31',
      'participantsLimit' => '100',
      'poolAmount' => '1000.50',
      'token' => 'ETH',
      'questThumbnail' => 'thumbnail.png',
      'creatorId' => '2',
      'questions' => [
        [
          'id' => '1',
          'text' => 'What is 2+2?',
          'score' => '10',
          'options' => [
            ['id' => '1', 'text' => '3', 'isCorrect' => false],
            ['id' => '2', 'text' => '4', 'isCorrect' => true],
          ],
        ],
      ],
    ];

    $this->questBuilder->expects($this->once())->method('reset');
    $this->questBuilder->expects($this->once())->method('setQuestId')->with(1);
    $this->questBuilder->expects($this->once())->method('setTitle')->with('Sample Quest');
    $this->questBuilder->expects($this->once())->method('setDescription')->with('This is a sample quest.');
    $this->questBuilder->expects($this->once())->method('setBlockchain')->with('Ethereum');
    $this->questBuilder->expects($this->once())->method('setRequiredMinutes')->with(10);
    $this->questBuilder->expects($this->once())->method('setExpiryDateString')->with('2024-12-31');
    $this->questBuilder->expects($this->once())->method('setPayoutDate')->with('2024-12-31');
    $this->questBuilder->expects($this->once())->method('setParticipantsLimit')->with(100);
    $this->questBuilder->expects($this->once())->method('setPoolAmount')->with(1000.50);
    $this->questBuilder->expects($this->once())->method('setToken')->with('ETH');
    $this->questBuilder->expects($this->once())->method('setPictureUrl')->with('thumbnail.png');
    $this->questBuilder->expects($this->once())->method('setCreatorId')->with(2);
    $this->questBuilder->expects($this->once())->method('setIsApproved')->with(false);

    $question = $this->createMock(Question::class);
    $option1 = $this->createMock(Option::class);
    $option2 = $this->createMock(Option::class);

    $this->questBuilder->expects($this->once())
      ->method('addQuestion')
      ->with($this->callback(function ($question) {
        return $question->getText() === 'What is 2+2?' &&
          $question->getScore() === 10 &&
          $question->getType() === QuestionType::SINGLE_CHOICE->value;
      }));

    $quest = $this->createMock(IQuest::class);
    $this->questBuilder->expects($this->once())->method('build')->willReturn($quest);

    $result = $this->questBuilderService->buildQuest($data);
    $this->assertSame($quest, $result);
  }
}
