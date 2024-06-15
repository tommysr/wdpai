<?php

namespace App\Controllers;

use App\Controllers\Interfaces\IUploadController;
use App\Middleware\IResponse;
use App\Middleware\JsonResponse;
use App\Middleware\RedirectResponse;
use App\Request\IFullRequest;
use App\Services\Session\ISessionService;
use App\Validator\IValidationChain;
use App\View\IViewRenderer;

class UploadController extends AppController implements IUploadController
{
  const MAX_FILE_SIZE = 1024 * 1024;
  const SUPPORTED_TYPES = ['image/png', 'image/jpeg'];
  const UPLOAD_DIRECTORY = '/../public/uploads/';

  public function __construct(IFullRequest $request, ISessionService $sessionService, IViewRenderer $viewRenderer)
  {
    parent::__construct($request, $sessionService, $viewRenderer);
  }

  public function getIndex(IFullRequest $request): IResponse
  {
    return new RedirectResponse('/error/404', ['what are you looking for?']);
  }

  // leave it here for now, but i can be middleware 
  private function validateQuestFile($fileData): array
  {
    $errors = [];

    if ($fileData['size'] > self::MAX_FILE_SIZE) {
      $errors[] = 'file is too big';
    }

    if (!in_array($fileData['type'], self::SUPPORTED_TYPES)) {
      $errors[] = 'file type is not supported';
    }

    return $errors;
  }

  private function generateFileName($filePath): string
  {
    $imageFileType = strtolower(pathinfo($filePath, PATHINFO_EXTENSION));
    $uniqueName = uniqid() . '.' . $imageFileType;
    $targetFile = $uniqueName;

    $counter = 1;
    while (file_exists($targetFile)) {
      $targetFile = $uniqueName . "_$counter" . $imageFileType;
      $counter++;
    }

    return $targetFile;
  }

  public function postUploadPicture(IFullRequest $request): IResponse
  {
    $fileData = $this->request->getUploadedFiles()['file'];
    $errors = $this->validateQuestFile($fileData);
    $lastUploadedFile = $request->getParsedBodyParam('lastUploadedFile');

    if ($lastUploadedFile) {
      $dir = dirname(__DIR__) . self::UPLOAD_DIRECTORY;
      unlink($dir . $lastUploadedFile);
    }

    if ($errors) {
      return new JsonResponse(['errors' => $errors]);
    }

    if ($fileData && is_uploaded_file($fileData['tmp_name'])) {
      $newFileName = $this->generateFileName($fileData['name']);
      $dir = dirname(__DIR__) . self::UPLOAD_DIRECTORY;

      copy(
        $fileData['tmp_name'],
        $dir . $newFileName
      );

      return new JsonResponse(['name' => $newFileName]);
    }

    return new JsonResponse(['errors' => ['file not uploaded']]);
  }
}
