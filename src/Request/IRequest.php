<?php
namespace App\Request;
use App\Request\IMessage;

interface IRequest extends IMessage
{
  public function getPath(): string;
  public function getMethod(): string;
  public function getBody(): string;
}