<?php
class InvalidQuestIdException extends Exception {
    public function errorMessage() {
        return "Invalid quest ID provided.";
    }
}

class QuestNotFoundException extends Exception {
    public function errorMessage() {
        return "Quest not found in the database.";
    }
}

class NoQuestionsException extends Exception {
    public function errorMessage() {
        return "No questions found for the given quest.";
    }
}