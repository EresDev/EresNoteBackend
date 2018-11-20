<?php
namespace App\Tests\Unit\Security\Encoder;

use App\Security\Encoder\BCryptPasswordEncoderPlus;
use PHPUnit\Framework\TestCase;
use Symfony\Component\Security\Core\Exception\BadCredentialsException;

/**
 * Class BCryptPasswordEncoderPlusTest
 * @package App\Tests\Unit\Security\Encoder
 */

class BCryptPasswordEncoderPlusTest extends TestCase
{
    const VALID_COST = '12';
    /**
     * @expectedException \Symfony\Component\Security\Core\Exception\BadCredentialsException
     */
    public function testEncodePassword_tooShortPassword() : void
    {
        $tooShortPassword = "abc14";
        $encoder = new BCryptPasswordEncoderPlus(self::VALID_COST);
        $encoder->encodePassword($tooShortPassword, null);
    }

    /**
     * @expectedException \Symfony\Component\Security\Core\Exception\BadCredentialsException
     */
    public function testEncodePassword_tooLongPassword() : void
    {
        $tooLongPassword = str_repeat('a', 73);
        $encoder = new BCryptPasswordEncoderPlus(self::VALID_COST);
        $encoder->encodePassword($tooLongPassword, null);
    }

    public function testEncodePassword_validPassword() : void
    {
        $validPassword = "dk435j0934j5";
        $encoder = new BCryptPasswordEncoderPlus(self::VALID_COST);
        $encodedPassword = $encoder->encodePassword($validPassword, null);
        $this->assertEquals(60, strlen($encodedPassword));
    }
}