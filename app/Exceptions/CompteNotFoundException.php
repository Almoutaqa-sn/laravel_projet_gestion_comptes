<?php

namespace App\Exceptions;

use Exception;

class CompteNotFoundException extends Exception
{
    protected $message = 'Le compte demandé est introuvable.';
    protected $code = 404;

    public function render($request)
    {
        return response()->json([
            'success' => false,
            'message' => $this->message,
            'data' => null
        ], $this->code);
    }
}
