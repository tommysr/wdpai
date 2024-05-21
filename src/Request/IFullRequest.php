<?php

namespace App\Request;

use App\Request\IRequest;

interface IFullRequest extends IRequest
{
  public function getQuery(string $key, $default = null);
  public function getParsedBodyParam(string $key, $default = null);
  public function getCookie(string $key, $default = null);
  public function getServerParam(string $key, $default = null);

  public function getServerParams(): array;
  public function getCookieParams(): array;
  public function getQueryParams(): array;
  public function getUploadedFiles(): array;
  public function getParsedBody(): array;

  public function withAttribute(string $key, $value): IFullRequest;
  public function getAttributes(): array;
  public function getAttribute(string $key, $default = null);
}