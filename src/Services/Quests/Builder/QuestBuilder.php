<?php

namespace App\Services\Quests\Builder;

use App\Services\Quests\Builder\IQuestBuilder;
use App\Models\Quest;
use App\Models\IQuest;
use App\Models\IQuestion;


// maybe consider extracting quest validation middleware here and use it 
// as part of the actual builder.
class QuestBuilder implements IQuestBuilder
{
  private IQuest $quest;
  private array $questions = [];

  public function __construct()
  {
    $this->reset();
  }

  public function reset(): void
  {
    $this->quest = new Quest(0, '', '', 0, '', 0, '', 0, 0, 0, '', 0, false, '', 0, '', '');
    $this->questions = [];
  }

  public function setQuestId(int $id): self
  {
    $this->quest->setQuestID($id);
    return $this;
  }

  public function setTitle(string $title): self
  {
    $this->quest->setTitle($title);
    return $this;
  }

  public function setDescription(string $description): self
  {
    $this->quest->setDescription($description);
    return $this;
  }

  public function setWorthKnowledge(int $worthKnowledge): self
  {
    $this->quest->setWorthKnowledge($worthKnowledge);
    return $this;
  }

  public function setRequiredWallet(string $requiredWallet): self
  {
    $this->quest->setRequiredWallet($requiredWallet);
    return $this;
  }

  public function setRequiredMinutes(int $timeRequiredMinutes): self
  {
    $this->quest->setRequiredMinutes($timeRequiredMinutes);
    return $this;
  }

  public function setExpiryDateString(string $expiryDateString): self
  {
    $this->quest->setExpiryDateString($expiryDateString);
    return $this;
  }

  public function setParticipantsCount(int $participantsCount): self
  {
    $this->quest->setParticipantsCount($participantsCount);
    return $this;
  }

  public function setParticipantsLimit(int $participantLimit): self
  {
    $this->quest->setParticipantsLimit($participantLimit);
    return $this;
  }

  public function setPoolAmount(float $poolAmount): self
  {
    $this->quest->setPoolAmount($poolAmount);
    return $this;
  }

  public function setMaxPoints(int $points): self
  {
    $this->quest->setMaxPoints($points);
    return $this;
  }

  public function setToken(string $token): self
  {
    $this->quest->setToken($token);
    return $this;
  }

  public function setCreatorId(int $creatorId): self
  {
    $this->quest->setCreatorId($creatorId);
    return $this;
  }

  public function setIsApproved(bool $isApproved): self
  {
    $this->quest->setIsApproved($isApproved);
    return $this;
  }

  public function addQuestion(IQuestion $question): self
  {
    $this->questions[] = $question;
    return $this;
  }

  public function setFlag(?string $flag): self
  {
    $this->quest->setFlag($flag);
    return $this;
  }

  public function build(): IQuest
  {
    $this->quest->setQuestions($this->questions);
    $result = $this->quest;
    $this->reset();

    return $result;
  }
}
