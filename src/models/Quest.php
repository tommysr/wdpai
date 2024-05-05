<?php

class Quest
{

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
        bool $approved
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
    }

    public function getQuestID()
    {
        return $this->questID;
    }

    public function getTitle()
    {
        return $this->title;
    }

    public function getDescription()
    {
        return $this->description;
    }

    public function getWorthKnowledge()
    {
        return $this->worthKnowledge;
    }

    public function getRequiredWallet()
    {
        return $this->requiredWallet;
    }

    public function getTimeRequiredMinutes()
    {
        return $this->timeRequiredMinutes;
    }

    public function getExpiryDateString()
    {
        return $this->expiryDate;
    }

    public function getParticipantsCount()
    {
        return $this->participantsCount;
    }

    public function getParticipantLimit()
    {
        return $this->participantLimit;
    }

    public function getPoolAmount()
    {
        return $this->poolAmount;
    }

    public function getPoints()
    {
        return $this->points;
    }

    public function getToken()
    {
        return $this->token;
    }

    public function getCreatorId() 
    {
        return $this->creatorId;
    }

    public function isApproved() {
        return $this->approved;
    }
}
