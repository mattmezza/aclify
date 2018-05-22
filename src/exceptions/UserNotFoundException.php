<?php

namespace Aclify\Exceptions;

class UserNotFoundException extends \Exception
{
    public function __construct($message = "", $code = 0, \Exception $previous = null)
    {
        parent::__construct("User not found", $code, $previous);
    }

    public function __toString()
    {
        return __class__ . ": [{$this->code}]: {$this->message}\n";
    }
}
