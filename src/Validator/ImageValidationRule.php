<?php

namespace App\Validator;

use App\Validator\IValid;

class ImageValidationRule implements IValid
{
  public function validate($value): bool|string
  {
    $allowed_extensions = ['jpg', 'jpeg', 'png'];

    $file_extension = strtolower(pathinfo($value, PATHINFO_EXTENSION));

    if (!in_array($file_extension, $allowed_extensions)) {
      return 'Invalid file extension. Only jpg, jpeg, and png are allowed.';
    }

    if (strpos($value, '/') !== false || strpos($value, '\\') !== false) {
      return 'Filename contains invalid characters (/ or \\).';
    }

    if (trim($value) === '') {
      return 'Filename is empty or only contains whitespace.';
    }

    return true;
  }
}