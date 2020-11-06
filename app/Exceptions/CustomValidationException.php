<?php

namespace App\Exceptions;

use Exception;

class CustomValidationException extends Exception
{
    public function render($request, Exception $exception)
    {
        if ($exception instanceof CustomValidationException)  {
            return $exception->render($request);
        }
        return parent::render($request, $exception);
    }
}
