<?php

namespace App\Services\Quests\Builder;

use App\Models\IQuest;
use App\Models\IQuestion;

interface IQuestBuilder
{
  public function setQuestId(int $id): self;
  public function setTitle(string $title): self;
  public function setDescription(string $description): self;
  public function setBlockchain(string $blockchain): self;
  public function setRequiredMinutes(int $timeRequiredMinutes): self;
  public function setExpiryDateString(string $expiryDateString): self;
  public function setParticipantsLimit(int $participantLimit): self;
  public function setPayoutDate(string $payoutDate): self;
  public function setPoolAmount(float $poolAmount): self;
  public function setToken(string $token): self;
  public function setCreatorId(int $creatorId): self;
  public function setIsApproved(bool $isApproved): self;
  public function addQuestion(IQuestion $question): self;
  public function setFlag(?string $flag): self;
  public function build(): IQuest;
  public function reset(): void;
}