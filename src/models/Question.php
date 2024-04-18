<?php

enum QuestionType
{
  case SingleChoice;
  case MultipleChoice;
  case NoChoice;
}

class Question
{
  private int $questionId;
  private int $questId;
  private string $text;
  private QuestionType $type;


  public function __construct(int $questionId, int $questId, string $text, QuestionType $type)
  {
    $this->questionId = $questionId;
    $this->questId = $questId;
    $this->text = $text;
    $this->type = $type;
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