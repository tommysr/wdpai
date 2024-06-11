<?php
namespace App\Request;

interface IRequest
{
  public function getPath(): string;
  public function getMethod(): string;
  public function getBody(): string;
  public function getHeaders(): array;
  public function getHeader(string $name): string;
}