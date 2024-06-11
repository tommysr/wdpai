<?php

namespace App\View;

interface IViewRenderer
{
  public function render(string $template, array $variables = [], string $content = null): string;
}