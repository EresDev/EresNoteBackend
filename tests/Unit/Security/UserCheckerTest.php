<?php
namespace App\Tests\Unit\Security;

use App\Entity\User;
use App\Security\UserChecker;
use PHPUnit\Framework\TestCase;
use Symfony\Component\Translation\TranslatorInterface;

/**
 * Class UserCheckerTest
 * @package App\Tests\Unit\Security
 */
class UserCheckerTest extends TestCase
{
    /**
     * @expectedException     Symfony\Component\Security\Core\Exception\CustomUserMessageAuthenticationException
     * @expectedExceptionCode 2
     */
    public function testCheckPreAuth_inactiveUser(){
        $translator = $this->createMock(TranslatorInterface::class);

        $translator
            ->method('trans')
            ->will($this->returnArgument(0));

        $userChecker = new UserChecker($translator);
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
    public function testCheckPreAuth_deletedUser(){
        $translator = $this->createMock(TranslatorInterface::class);

        $translator
            ->method('trans')
            ->will($this->returnArgument(0));

        $userChecker = new UserChecker($translator);
        $user = new User();
        $user->setEmail("a_test_user@example.com");
        $user->setPassword("sdfhdskj9834hjo");
        $user->setDeleted(true);
        $user->setActive(false);
        $userChecker->checkPreAuth($user);
    }


    public function testCheckPreAuth_validUser(){
        $translator = $this->createMock(TranslatorInterface::class);

        $translator
            ->method('trans')
            ->will($this->returnArgument(0));

        $userChecker = new UserChecker($translator);
        $user = new User();
        $user->setEmail("a_test_user@example.com");
        $user->setPassword("sdfhdskj9834hjo");
        $user->setDeleted(false);
        $user->setActive(true);
        $this->assertNull($userChecker->checkPreAuth($user));
    }

    public function testCheckPostAuth(){
        $translator = $this->createMock(TranslatorInterface::class);

        $translator
            ->method('trans')
            ->will($this->returnArgument(0));

        $userChecker = new UserChecker($translator);
        $user = new User();
        $user->setEmail("a_test_user@example.com");
        $user->setPassword("sdfhdskj9834hjo");
        $user->setDeleted(false);
        $user->setActive(true);
        $this->assertNull($userChecker->checkPostAuth($user));
    }
}