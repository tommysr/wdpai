<?php

namespace App\Models;

use App\Models\QuestionType;

interface IQuest
{
    public function getQuestID(): int;
    public function getTitle(): string;
    public function getDescription(): string;
    public function getWorthKnowledge(): int;
    public function getRequiredWallet(): string;
    public function getTimeRequiredMinutes(): int;
    public function getExpiryDateString(): string;
    public function getParticipantsCount(): int;
    public function getParticipantLimit(): int;
    public function getPoolAmount(): float;
    public function getPoints(): int;
    public function getToken(): string;
    public function getCreatorId(): int;
    public function getIsApproved(): bool;
    public function getQuestions(): array;
    public function setQuestions(array $questions);
    public function __equals(IQuest $other): bool;
}