<?php

namespace App\Request;

interface IMessage
{
  public function getHeaders(): array;
  public function getHeader(string $name): string;
}