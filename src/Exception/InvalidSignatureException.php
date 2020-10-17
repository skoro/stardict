<?php declare(strict_types=1);

namespace StarDict\Exception;

class InvalidSignatureException extends Exception
{
    public $message = 'Invalid dictionary signature.';   
}