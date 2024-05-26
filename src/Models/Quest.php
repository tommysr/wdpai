<?php

namespace App\Models;

use App\Models\IQuest;

class Quest implements IQuest
{

    private ?string $flag;
    private int $questID;
    private string $title;
    private string $description;
    private int $worthKnowledge;
    private string $requiredWallet;
    private int $timeRequiredMinutes;
    private string $expiryDate;
    private int $participantsCount;
    private int $participantLimit;
    private float $poolAmount;
    private string $token;
    private int $points;
    private int $creatorId;
    private bool $approved;

    private $questions = array();

    public function __construct(
        int $questID,
        string $title,
        string $description,
        int $worthKnowledge,
        string $requiredWallet,
        int $timeRequiredMinutes,
        string $expiryDate,
        int $participantsCount,
        int $participantLimit,
        float $poolAmount,
        string $token,
        int $points,
        int $creatorId,
        bool $approved,
        string $flag = null
    ) {
        $this->questID = $questID;
        $this->title = $title;
        $this->description = $description;
        $this->worthKnowledge = $worthKnowledge;
        $this->requiredWallet = $requiredWallet;
        $this->timeRequiredMinutes = $timeRequiredMinutes;
        $this->expiryDate = $expiryDate;
        $this->participantsCount = $participantsCount;
        $this->participantLimit = $participantLimit;
        $this->poolAmount = $poolAmount;
        $this->token = $token;
        $this->points = $points;
        $this->creatorId = $creatorId;
        $this->approved = $approved;
        $this->flag = $flag;
    }

    public function getFlag(): string|null
    {
        return $this->flag;
    }

    public function setFlag(string $flag): void
    {
        $this->flag = $flag;
    }

    public function getQuestID(): int
    {
        return $this->questID;
    }

    public function getTitle(): string
    {
        return $this->title;
    }

    public function getDescription(): string
    {
        return $this->description;
    }

    public function getWorthKnowledge(): int
    {
        return $this->worthKnowledge;
    }

    public function getRequiredWallet(): string
    {
        return $this->requiredWallet;
    }

    public function getTimeRequiredMinutes(): int
    {
        return $this->timeRequiredMinutes;
    }

    public function getExpiryDateString(): string
    {
        return $this->expiryDate;
    }

    public function getParticipantsCount(): int
    {
        return $this->participantsCount;
    }

    public function getParticipantLimit(): int
    {
        return $this->participantLimit;
    }

    public function getPoolAmount(): float
    {
        return $this->poolAmount;
    }

    public function getPoints(): int
    {
        return $this->points;
    }

    public function getToken(): string
    {
        return $this->token;
    }

    public function getCreatorId(): int
    {
        return $this->creatorId;
    }

    public function getIsApproved(): bool
    {
        return $this->approved;
    }

    public function getQuestions(): array
    {
        return $this->questions;
    }

    public function setQuestions(array $questions)
    {
        $this->questions = $questions;
    }

    public function __equals(IQuest $other): bool
    {
        return $this->questID === $other->getQuestID()
            && $this->title === $other->getTitle()
            && $this->description === $other->getDescription()
            && $this->worthKnowledge === $other->getWorthKnowledge()
            && $this->requiredWallet === $other->getRequiredWallet()
            && $this->timeRequiredMinutes === $other->getTimeRequiredMinutes()
            && $this->expiryDate === $other->getExpiryDateString()
            && $this->participantsCount === $other->getParticipantsCount()
            && $this->participantLimit === $other->getParticipantLimit()
            && $this->poolAmount === $other->getPoolAmount()
            && $this->points === $other->getPoints()
            && $this->token === $other->getToken()
            && $this->creatorId === $other->getCreatorId()
            && $this->approved === $other->getIsApproved();
    }

    // Setters
    public function setQuestID(int $questID): void
    {
        $this->questID = $questID;
    }

    public function setTitle(string $title): void
    {
        $this->title = $title;
    }

    public function setDescription(string $description): void
    {
        $this->description = $description;
    }

    public function setWorthKnowledge(int $worthKnowledge): void
    {
        $this->worthKnowledge = $worthKnowledge;
    }

    public function setRequiredWallet(string $requiredWallet): void
    {
        $this->requiredWallet = $requiredWallet;
    }

    public function setTimeRequiredMinutes(int $timeRequiredMinutes): void
    {
        $this->timeRequiredMinutes = $timeRequiredMinutes;
    }

    public function setExpiryDateString(string $expiryDateString): void
    {
        $this->expiryDate = $expiryDateString;
    }

    public function setParticipantsCount(int $participantsCount): void
    {
        $this->participantsCount = $participantsCount;
    }

    public function setParticipantLimit(int $participantLimit): void
    {
        $this->participantLimit = $participantLimit;
    }

    public function setPoolAmount(float $poolAmount): void
    {
        $this->poolAmount = $poolAmount;
    }

    public function setPoints(int $points): void
    {
        $this->points = $points;
    }

    public function setToken(string $token): void
    {
        $this->token = $token;
    }

    public function setCreatorId(int $creatorId): void
    {
        $this->creatorId = $creatorId;
    }

    public function setIsApproved(bool $isApproved): void
    {
        $this->approved = $isApproved;
    }
}


