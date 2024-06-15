<?php

use PHPUnit\Framework\TestCase;
use App\Services\Question\QuestionService;
use App\Models\QuestionType;
use App\Models\IQuest;
use App\Models\IQuestion;
use App\Models\IOption;
use App\Repository\IQuestionsRepository;
use App\Repository\IOptionsRepository;

class QuestionServiceTest extends TestCase
{
  protected $questionRepositoryMock;
  protected $optionRepositoryMock;
  protected $questionService;

  protected function setUp(): void
  {
    $this->questionRepositoryMock = $this->createMock(IQuestionsRepository::class);
    $this->optionRepositoryMock = $this->createMock(IOptionsRepository::class);
    $this->questionService = new QuestionService($this->questionRepositoryMock, $this->optionRepositoryMock);
  }

  public function testUpdateQuestions()
  {
    $questMock = $this->createMock(IQuest::class);
    $questId = 1;
    $questMock->method('getQuestID')->willReturn($questId);

    $question1 = $this->createMock(IQuestion::class);
    $question1->method('getFlag')->willReturn('added');
    $question1->method('getOptions')->willReturn([]);

    $question2 = $this->createMock(IQuestion::class);
    $question2->method('getFlag')->willReturn('removed');
    $question2->method('getQuestionId')->willReturn(2);

    $questMock->method('getQuestions')->willReturn([$question1, $question2]);

    // Set expectations on mock repositories
    $this->questionRepositoryMock->expects($this->once())
      ->method('saveQuestion')
      ->with($question1)
      ->willReturn(100); // Mock question ID

    $this->optionRepositoryMock->expects($this->once()) // No options processing for removed question
      ->method('deleteAllOptions');

    $this->questionRepositoryMock->expects($this->once())
      ->method('deleteQuestionById')
      ->with(2);

    $this->questionService->updateQuestions($questMock);
  }

  // Test case for fetching questions
  public function testFetchQuestions()
  {
    $questMock = $this->createMock(IQuest::class);
    $questId = 1;
    $questMock->method('getQuestID')->willReturn($questId);

    $question1 = $this->createMock(IQuestion::class);
    $question1->method('getQuestionId')->willReturn(1);

    $option1 = $this->createMock(IOption::class);
    $option1->method('getOptionId')->willReturn(101);

    $question1->method('getOptions')->willReturn([$option1]);

    $this->questionRepositoryMock->method('getQuestionsByQuestId')->willReturn([$question1]);
    $this->optionRepositoryMock->method('getOptionsByQuestionId')->willReturn([$option1]);

    // Call the method to be tested
    $questions = $this->questionService->fetchQuestions($questMock);

    // Assertions
    $this->assertCount(1, $questions);
    $this->assertCount(1, $questions[0]->getOptions());
  }

  // Test case for evaluating options
  public function testEvaluateOptions()
  {
    $questionId = 1;

    $selectedOptions = [101, 102]; // Example selected 

    $questionMock = $this->createMock(IQuestion::class);
    $questionMock->method('getType')->willReturn(QuestionType::MULTIPLE_CHOICE->value);
    $questionMock->method('getPoints')->willReturn(10);

    $this->questionRepositoryMock->method('getById')->willReturn($questionMock);

    $option1 = $this->createMock(IOption::class);
    $option1->method('getOptionId')->willReturn(101);

    $option2 = $this->createMock(IOption::class);
    $option2->method('getOptionId')->willReturn(102);

    $this->optionRepositoryMock->method('getOptionsByQuestionId')->willReturn([$option1, $option2]);

    $this->optionRepositoryMock->method('getCorrectOptionsIdsForQuestionId')->willReturn([101, 102, 103]);

    // Call the method to be tested
    $result = $this->questionService->evaluateOptions($questionId, $selectedOptions);

    // Assertions
    $this->assertEquals(7, $result['points']); // Adjust based on your evaluation logic
    $this->assertEquals(10, $result['maxPoints']);
    $this->assertContains(101, $result['options']);
    $this->assertContains(102, $result['options']);
  }
}
