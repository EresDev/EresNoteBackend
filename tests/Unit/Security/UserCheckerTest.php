<?php
namespace App\Tests\Unit\Security;

use App\Entity\User;
use App\Security\UserChecker;
use PHPUnit\Framework\TestCase;

/**
 * Class UserCheckerTest
 * @package App\Tests\Unit\Security
 */
class UserCheckerTest extends TestCase
{
    /**
     * @expectedException     Symfony\Component\Security\Core\Exception\CustomUserMessageAuthenticationException
     * @expectedExceptionCode 3
     */
    public function testCheckPreAuth_inactiveUser(){
        $validator = $this->createMock(\Symfony\Component\Validator\Validator\ValidatorInterface::class);
        $userChecker = new UserChecker($validator);
        $user = new User();
        $user->setEmail("a_test_user@example.com");
        $user->setPassword("sdfhdskj9834hjo");
        $user->setDeleted(false);
        $user->setActive(false);
        $userChecker->checkPreAuth($user);
    }

    /**
     * @expectedException     Symfony\Component\Security\Core\Exception\CustomUserMessageAuthenticationException
     * @expectedExceptionCode 1
     */
    public function testCheckPreAuth_invalidEmail(){
        $validator = $this->createMock(\Symfony\Component\Validator\Validator\ValidatorInterface::class);

        $validator
            ->expects($this->once())
            ->method('validate')
            ->will($this->returnValue('invalid email'));

        $userChecker = new UserChecker($validator);
        $user = new User();
        $user->setEmail("a_test_user");
        $user->setPassword("sdfhdskj9834hjo");
        $user->setDeleted(false);
        $user->setActive(true);
        $userChecker->checkPreAuth($user);
    }

    /**
     * @expectedException     Symfony\Component\Security\Core\Exception\CustomUserMessageAuthenticationException
     * @expectedExceptionCode 2
     */
    public function testCheckPreAuth_deletedUser(){
        $validator = $this->createMock(\Symfony\Component\Validator\Validator\ValidatorInterface::class);
        $userChecker = new UserChecker($validator);
        $user = new User();
        $user->setEmail("a_test_user@example.com");
        $user->setPassword("sdfhdskj9834hjo");
        $user->setDeleted(true);
        $user->setActive(false);
        $userChecker->checkPreAuth($user);
    }


    public function testCheckPreAuth_validUser(){
        $validator = $this->createMock(\Symfony\Component\Validator\Validator\ValidatorInterface::class);
        $userChecker = new UserChecker($validator);
        $user = new User();
        $user->setEmail("a_test_user@example.com");
        $user->setPassword("sdfhdskj9834hjo");
        $user->setDeleted(false);
        $user->setActive(true);
        $this->assertNull($userChecker->checkPreAuth($user));
    }

    public function testCheckPostAuth(){
        $validator = $this->createMock(\Symfony\Component\Validator\Validator\ValidatorInterface::class);
        $userChecker = new UserChecker($validator);
        $user = new User();
        $user->setEmail("a_test_user@example.com");
        $user->setPassword("sdfhdskj9834hjo");
        $user->setDeleted(false);
        $user->setActive(true);
        $this->assertNull($userChecker->checkPostAuth($user));
    }
}