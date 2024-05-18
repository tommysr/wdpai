<?php
class InvalidQuestIdException extends Exception
{
    public function errorMessage()
    {
        return "Invalid quest ID provided.";
    }
}

class QuestNotFoundException extends Exception
{
    public function errorMessage()
    {
        return "Quest not found in the database.";
    }
}

class NoQuestionsException extends Exception
{
    public function errorMessage()
    {
        return "No questions found for the given quest.";
    }
}

class AuthorizationException extends Exception
{
    public function errorMessage()
    {
        return "authorization failed";
    }
}

class NotFoundException extends Exception
{
    public function errorMessage()
    {
        return "not found";
    }
}

class NotLoggedInException extends Exception
{
    public function errorMessage()
    {
        return "you're not logged in";
    }
}

class ValidationException extends Exception
{
    public function errorMessage()
    {
        return "validation failed";
    }
}

class AlreadyApproved extends Exception
{
    public function errorMessage()
    {
        return 'already approved';
    }
}

class GameplayInProgressException extends Exception
{
    public function errorMessage()
    {
        return 'gameplay in progress';
    }
}

