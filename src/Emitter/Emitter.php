<?php

namespace App\Emitter;

use App\Middleware\IResponse;
use App\Emitter\IEmitter;
use App\Middleware\IRedirectResponse;
use App\Middleware\IJsonResponse;

class Emitter implements IEmitter
{
  public function emit(IResponse $response): void
  {
    // Check the type of response and handle it accordingly
    if ($response instanceof IRedirectResponse) {
      $this->emitRedirectResponse($response);
    } elseif ($response instanceof IJsonResponse) {
      $this->emitJsonResponse($response);
    } else {
      $this->emitNormalResponse($response);
    }
  }
  private function buildHeaders(array $headers): string
  {
    $headerString = '';
    foreach ($headers as $name => $values) {
      $headerString .= sprintf('%s: %s', $name, implode(', ', $values)) . "\r\n";
    }
    return $headerString;
  }

  private function emitNormalResponse(IResponse $response)
  {
    $headerString = $this->buildHeaders($response->getHeaders());
    header($headerString, true);

    http_response_code($response->getStatusCode());
    echo $response->getBody();
  }

  private function emitJsonResponse(IJsonResponse $response)
  {
    $headerString = $this->buildHeaders($response->getHeaders());
    header($headerString, true);

    http_response_code($response->getStatusCode());
    echo json_encode($response->getData(), 1024);
  }

  private function emitRedirectResponse(IRedirectResponse $response)
  {
    $messages = $response->getMessages();
    $redirectUri = $response->getRedirectUri();

    if (!empty($messages)) {
      $queryParams = http_build_query(['messages' => $messages]);
      $redirectUri .= (parse_url($response->getRedirectUri(), PHP_URL_QUERY) ? '&' : '?') . $queryParams;
    }
    header('Location: ' . "http://$_SERVER[HTTP_HOST]" . $redirectUri, true, $response->getStatusCode());
    exit();
  }
}