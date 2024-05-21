<?php

namespace App\View;


use App\View\IViewRenderer;

class ViewRenderer implements IViewRenderer
{
  protected string $viewPath;

  public function __construct(string $viewPath)
  {
    $this->viewPath = rtrim($viewPath, '/');
  }

  public function render(string $template, array $variables = [], string $content = null): string
  {
    $templatePath = "{$this->viewPath}/{$template}.php";

    if (!file_exists($templatePath)) {
      throw new \Exception('Template file not found');
    }

    extract($variables);
    ob_start();
    include $templatePath;
    return ob_get_clean();
  }
}