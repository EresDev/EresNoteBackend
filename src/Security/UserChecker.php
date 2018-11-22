<?php
namespace App\Security;

use Symfony\Component\Security\Core\Exception\CustomUserMessageAuthenticationException;
use Symfony\Component\Security\Core\User\UserCheckerInterface;
use Symfony\Component\Security\Core\User\UserInterface;

use App\Entity\User;
use Symfony\Component\Validator\Validator\ValidatorInterface;

/**
 * Class UserChecker
 * @package App\Security
 *
 * Validate username/email before authentication
 * Also make sure that the user is not deleted or inactive
 */
class UserChecker implements UserCheckerInterface
{
    /**
     * @var ValidatorInterface
     */
    private $validator;

    /**
     * UserChecker constructor.
     * @param ValidatorInterface $validator
     */
    public function __construct(ValidatorInterface $validator)
    {
        $this->validator = $validator;
    }

    /**
     * @param UserInterface $user
     */
    public function checkPreAuth(UserInterface $user) : void
    {

        if (!$user instanceof User) {
            return;
        }
        $errors = $this->validator->validate($user);

        if ($errors->count()) {
            /*
             * Uses a __toString method on the $errors variable which is a
             * ConstraintViolationList object. This gives us a nice string
             * for debugging.
             */
            //$errorsString = (string) $errors;

            throw new CustomUserMessageAuthenticationException(
                $errors, ["test" => "test"], 1
            );

        }

        // user is deleted, show a generic Account Not Found message.
        if ($user->isDeleted()) {
            //throw new AccountDeletedException('Account Deleted');

            // or to customize the message shown
            throw new CustomUserMessageAuthenticationException(
                '', [],2
            );
        }

        if (!$user->isActive()) {
            //throw new AccountDeletedException('Account Not Active');

            // or to customize the message shown
            throw new CustomUserMessageAuthenticationException(
                'Your account is not active. Sorry about that!' , ['test' => 'test'], 1313
            );
        }

    }
    /**
     * @param UserInterface $user
     */
    public function checkPostAuth(UserInterface $user) : void
    {
        if (!$user instanceof User) {
            return;
        }

    }
}