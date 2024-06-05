<?php

namespace App\Models;


interface IQuest
{
    public function setFlag(string $flag): void;
    public function getFlag(): ?string;
    public function getQuestID(): int;
    public function getTitle(): string;
    public function getDescription(): string;
    public function getAvgRating(): float;
    public function getBlockchain(): string;
    public function getRequiredMinutes(): int;
    public function getExpiryDateString(): string;
    public function getParticipantsCount(): int;
    public function getParticipantsLimit(): int;
    public function getPoolAmount(): float;
    public function getToken(): string;
    public function getCreatorId(): int;
    public function getIsApproved(): bool;
    public function getQuestions(): array;
    public function getPayoutDate(): string;
    public function setQuestions(array $questions);
    public function getPictureUrl(): string;
    public function getMaxPoints(): int;
    public function getCreatorName(): string;

    // Setters

    public function setCreatorName(string $creatorName): void;
    public function setPictureUrl(string $pictureUrl): void;
    public function setMaxPoints(int $maxPoints): void;
    public function setPayoutDate(string $payoutDate): void;
    public function setQuestID(int $questID): void;
    public function setTitle(string $title): void;
    public function setDescription(string $description): void;
    public function setAvgRating(float $avgRating): void;
    public function setBlockchain(string $blockchain): void;
    public function setRequiredMinutes(int $timeRequiredMinutes): void;
    public function setExpiryDateString(string $expiryDateString): void;
    public function setParticipantsCount(int $participantsCount): void;
    public function setParticipantsLimit(int $participantLimit): void;
    public function setPoolAmount(float $poolAmount): void;
    public function setToken(string $token): void;
    public function setCreatorId(int $creatorId): void;
    public function setIsApproved(bool $isApproved): void;
}