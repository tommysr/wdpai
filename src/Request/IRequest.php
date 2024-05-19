<?php
namespace App\Request;


interface IRequest
{
  public function getPath(): string;
  public function getMethod(): string;
  public function get(string $key);
  public function post(string $key);
  public function server(string $key);
}