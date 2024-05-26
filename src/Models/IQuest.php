<?php

namespace App\Models;

use App\Models\QuestionType;

interface IQuest
{
    public function setFlag(string $flag): void;
    public function getFlag(): ?string;
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

    // Setters
    public function setQuestID(int $questID): void;
    public function setTitle(string $title): void;
    public function setDescription(string $description): void;
    public function setWorthKnowledge(int $worthKnowledge): void;
    public function setRequiredWallet(string $requiredWallet): void;
    public function setTimeRequiredMinutes(int $timeRequiredMinutes): void;
    public function setExpiryDateString(string $expiryDateString): void;
    public function setParticipantsCount(int $participantsCount): void;
    public function setParticipantLimit(int $participantLimit): void;
    public function setPoolAmount(float $poolAmount): void;
    public function setPoints(int $points): void;
    public function setToken(string $token): void;
    public function setCreatorId(int $creatorId): void;
    public function setIsApproved(bool $isApproved): void;
}