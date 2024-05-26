<?php

namespace App\Models;

use App\Models\IQuest;

class Quest implements IQuest
{

    private ?string $flag;
    private int $questId;
    private string $title;
    private string $description;
    private int $worthKnowledge;
    private string $blockchain;
    private int $requiredMinutes;
    private string $expiryDate;
    private int $participantsCount;
    private int $participantsLimit;
    private int $maxPoints;
    private float $poolAmount;
    private string $token;
    private string $payoutDate;
    private string $creatorName;
    private int $creatorId;
    private bool $approved;
    private string $pictureUrl;

    private $questions = array();

    public function __construct(
        int $questId,
        string $title,
        string $description,
        int $worthKnowledge,
        string $blockchain,
        int $requiredMinutes,
        string $expiryDate,
        int $participantsCount,
        int $participantsLimit,
        float $poolAmount,
        string $token,
        int $creatorId,
        bool $approved,
        string $pictureUrl,
        int $maxPoints,
        string $payoutDate,
        string $creatorName,
        string $flag = null
    ) {
        $this->questId = $questId;
        $this->title = $title;
        $this->description = $description;
        $this->worthKnowledge = $worthKnowledge;
        $this->blockchain = $blockchain;
        $this->requiredMinutes = $requiredMinutes;
        $this->expiryDate = $expiryDate;
        $this->participantsCount = $participantsCount;
        $this->participantsLimit = $participantsLimit;
        $this->poolAmount = $poolAmount;
        $this->token = $token;
        $this->creatorId = $creatorId;
        $this->approved = $approved;
        $this->pictureUrl = $pictureUrl;
        $this->flag = $flag;
        $this->maxPoints = $maxPoints;
        $this->payoutDate = $payoutDate;
        $this->creatorName = $creatorName;
    }


    public function getCreatorName(): string
    {
        return $this->creatorName;
    }

    public function setCreatorName(string $creatorName): void
    {
        $this->creatorName = $creatorName;
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
        return $this->questId;
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

    public function getBlockchain(): string
    {
        return $this->blockchain;
    }

    public function getRequiredMinutes(): int
    {
        return $this->requiredMinutes;
    }

    public function getExpiryDateString(): string
    {
        return $this->expiryDate;
    }

    public function getParticipantsCount(): int
    {
        return $this->participantsCount;
    }

    public function getParticipantsLimit(): int
    {
        return $this->participantsLimit;
    }

    public function getPoolAmount(): float
    {
        return $this->poolAmount;
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

    public function setParticipantsLimit(int $participantLimit): void
    {
        $this->participantLimit = $participantLimit;
    }

    public function setPoolAmount(float $poolAmount): void
    {
        $this->poolAmount = $poolAmount;
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

    public function getPictureUrl(): string
    {
        return $this->pictureUrl;
    }

    public function setPictureUrl(string $pictureUrl): void
    {
        $this->pictureUrl = $pictureUrl;
    }

    public function getMaxPoints(): int
    {
        return $this->maxPoints;
    }

    public function setMaxPoints(int $maxPoints): void
    {
        $this->maxPoints = $maxPoints;
    }

    public function getPayoutDate(): string
    {
        return $this->payoutDate;
    }

    public function setPayoutDate(string $payoutDate): void
    {
        $this->payoutDate = $payoutDate;
    }

    public function setRequiredMinutes(int $timeRequiredMinutes): void
    {
        $this->timeRequiredMinutes = $timeRequiredMinutes;
    }
}


