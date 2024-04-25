<?php

class QuestionType
{
  const SINGLE_CHOICE = 'single_choice';
  const MULTIPLE_CHOICE = 'multiple_choice';
  const READ_TEXT = 'read_text';
  const UNKNOWN = 'unknown';

  protected $value;

  public function __construct($value)
  {
    $this->value = $value;
  }

  // Method to parse a string into a QuestionType instance
  public static function fromName(string $name): ?self
  {
    switch ($name) {
      case self::SINGLE_CHOICE:
      case self::MULTIPLE_CHOICE:
      case self::READ_TEXT:
      case self::UNKNOWN:
        return new static($name);
      default:
        return null;
    }
  }

  // Getter method for the value
  public function getValue(): string
  {
    return $this->value;
  }
}

class Question
{
  private int $questionId;
  private int $questId;
  private string $text;
  private QuestionType $type;
  private int $score;


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

}