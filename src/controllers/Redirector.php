<?php

class Redirector
{
  public static function redirectTo(string $url, int $code = 0): void
  {
    $url = "http://$_SERVER[HTTP_HOST]/$url";
    header('Location:' . $url, true, $code);
    exit();
  }

  public static function redirectToWithParams(string $url, array $params = [], int $code = 0): void
  {
    $queryString = http_build_query($params);
    $url = "http://$_SERVER[HTTP_HOST]/$url" . '?' . $queryString;
    header('Location:' . $url, true, $code);
    exit();
  }
}
