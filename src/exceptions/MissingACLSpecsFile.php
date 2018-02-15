<?php
declare(strict_types=1);

namespace Aclify\Exceptions;

class MissingACLSpecsFile extends \Exception
{
    public function MissingACLSpecsFile($filename) : void
    {
        parent("The specified file $filename is not readable. Please check it twice.");
    }
}
