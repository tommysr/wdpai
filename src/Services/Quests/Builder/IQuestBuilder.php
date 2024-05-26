<?php

namespace App\Services\Quests\Builder;

use App\Models\IQuest;
use App\Models\IQuestion;

interface IQuestBuilder
{
  public function setQuestId(int $id): self;
  public function setTitle(string $title): self;
  public function setDescription(string $description): self;
  public function setWorthKnowledge(int $worthKnowledge): self;
  public function setRequiredWallet(string $requiredWallet): self;
  public function setTimeRequiredMinutes(int $timeRequiredMinutes): self;
  public function setExpiryDateString(string $expiryDateString): self;
  public function setParticipantsCount(int $participantsCount): self;
  public function setParticipantLimit(int $participantLimit): self;
  public function setPoolAmount(float $poolAmount): self;
  public function setPoints(int $points): self;
  public function setToken(string $token): self;
  public function setCreatorId(int $creatorId): self;
  public function setIsApproved(bool $isApproved): self;
  public function addQuestion(IQuestion $question): self;
  public function setFlag(?string $flag): self;
  public function build(): IQuest;
  public function reset(): void;
}