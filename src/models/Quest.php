<?php

class Quest {

    private $questID;
    private $title;
    private $description;
    private $worthKnowledge;
    private $requiredWallet;
    private $timeRequired;
    private $expiryDate;
    private $participantsCount;
    private $participantLimit;
    private $poolAmount;
    // private $pictures;

    public function __construct($questID, $title, $description, $worthKnowledge, $requiredWallet, $timeRequired, $expiryDate, $participantsCount, $participantLimit, $poolAmount) {
        $this->questID = $questID;
        $this->title = $title;
        $this->description = $description;
        $this->worthKnowledge = $worthKnowledge;
        $this->requiredWallet = $requiredWallet;
        $this->timeRequired = $timeRequired;
        $this->expiryDate = $expiryDate;
        $this->participantsCount = $participantsCount;
        $this->participantLimit = $participantLimit;
        $this->poolAmount = $poolAmount;
        // $this->pictures = $pictures;
    }

    public function getQuestID() {
        return $this->questID;
    }

    public function getTitle() {
        return $this->title;
    }

    public function getDescription() {
        return $this->description;
    }

    public function getWorthKnowledge() {
        return $this->worthKnowledge;
    }

    public function getRequiredWallet() {
        return $this->requiredWallet;
    }

    public function getTimeRequired() {
        return $this->timeRequired;
    }

    public function getExpiryDate() {
        return $this->expiryDate;
    }

    public function getParticipantsCount() {
        return $this->participantsCount;
    }

    public function getParticipantLimit() {
        return $this->participantLimit;
    }

    public function getPoolAmount() {
        return $this->poolAmount;
    }

    // public function getPictures() {
    //     return $this->pictures;
    // }
}
