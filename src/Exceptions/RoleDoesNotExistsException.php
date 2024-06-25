<?php

namespace App\Exceptions;

use Exception;

class RoleDoesNotExistsException extends Exception
{
    protected $code = 400;
}
