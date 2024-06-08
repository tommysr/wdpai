<?php

namespace App\Controllers;

use App\Controllers\Interfaces\IUPloadController;
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

  public function __construct(IFullRequest $request, ISessionService $sessionService, IViewRenderer $viewRenderer, IValidationChain $validation)
  {
    parent::__construct($request, $sessionService, $viewRenderer);
  }

  public function getIndex(IFullRequest $request): IResponse
  {
    return new RedirectResponse('/error/404', ['unknown route']);
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
    $targetFile = $filePath . $uniqueName;

    $counter = 1;
    while (file_exists($targetFile)) {
      $baseName = pathinfo($filePath, PATHINFO_FILENAME);
      $extension = pathinfo($filePath, PATHINFO_EXTENSION);
      $targetFile = $filePath . $baseName . "_$counter" . $extension;
      $counter++;
    }

    return $targetFile;
  }

  public function postUploadPicture(IFullRequest $request): IResponse
  {
    $fileData = $this->request->getUploadedFiles()['file'];
    $errors = $this->validateQuestFile($fileData);

    if ($errors) {
      return new JsonResponse(['errors' => $errors]);
    }

    if ($fileData && is_uploaded_file($fileData['tmp_name'])) {
      $newFileName = $this->generateFileName($fileData['name']);
      move_uploaded_file(
        $fileData['tmp_name'],
        dirname(__DIR__) . self::UPLOAD_DIRECTORY . $newFileName
      );
      return new JsonResponse(['name' => $newFileName]);
    }

    return new JsonResponse(['errors' => ['file not uploaded']]);
  }
}
