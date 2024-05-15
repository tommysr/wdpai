<?php

interface IGlobalVariablesManager
{
  public static function getGlobalVariables(ISessionService $sessionService): array;
}

class GlobalVariablesManager
{
  public static function getGlobalVariables(ISessionService $sessionService): array
  {
    $sessionService::start();

    $globalVariables = $sessionService::get('global_variables');

    if (!$globalVariables) {
      $globalVariables = [
        'user' => null,
        'role' => 'guest',
      ];

      $sessionService::set('global_variables', $globalVariables);
    }

    return $globalVariables;
  }
}
