<?php declare(strict_types=1);

namespace StarDict\Tests;

use StarDict\Exception\InvalidSignatureException;
use StarDict\Info\SignatureChecker;

class SignatureCheckerTest extends TestCase
{
    public function testValidSignatureBool()
    {
        $check = new SignatureChecker('test');
        $this->assertTrue($check->check('test'));
    }

    public function testSignatureException()
    {
        $this->expectException(InvalidSignatureException::class);
        $this->expectExceptionMessage('Invalid dictionary signature.');

        $check = new SignatureChecker('test');
        $check->checkAndThrow('abc');
    }
}