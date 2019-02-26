<?php

namespace App\Exceptions;

use Exception;

class ApiException extends Exception {

    public function __construct($message)
    {
        parent::__construct($message);
    }
}