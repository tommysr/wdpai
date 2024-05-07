<?php

enum QuestionType
{
  case SINGLE_CHOICE;
  case MULTIPLE_CHOICE;
  case READ_TEXT;
  case UNKNOWN;
}


function getQuestionTypeFromName(string $name): QuestionType {
  $lookup = [
      'single_choice' => QuestionType::SINGLE_CHOICE,
      'multiple_choice' => QuestionType::MULTIPLE_CHOICE,
      'read_text' => QuestionType::READ_TEXT,
  ];

  return $lookup[$name] ?? QuestionType::UNKNOWN;
}


class Question
{
  private int $questionId;
  private int $questId;
  private string $text;
  private QuestionType $type;
  private int $score;

  private $options = array();

  public function __equals(Question $other): bool {
    return $this->questionId === $other->getQuestionId()
    && $this->text === $other->getText()
    && $this->type === $other->getType();
  }


  public function __construct(int $questionId, int $questId, string $text, QuestionType $type)
  {
    $this->questionId = $questionId;
    $this->questId = $questId;
    $this->text = $text;
    $this->type = $type;
    $this->score = 2;
  }


  public function getQuestionId()
  {
    return $this->questionId;
  }

  public function getQuestId()
  {
    return $this->questId;
  }

  public function getText()
  {
    return $this->text;
  }

  public function getType()
  {
    return $this->type;
  }

  public function getOptions()
  {
    return $this->options;
  }

  public function setOptions(array $options)
  {
    $this->options = $options;
  }
}