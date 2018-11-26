<?php
namespace App\Security;

use Symfony\Component\Security\Core\Exception\CustomUserMessageAuthenticationException;
use Symfony\Component\Security\Core\User\UserCheckerInterface;
use Symfony\Component\Security\Core\User\UserInterface;

use App\Entity\User;
use Symfony\Component\Translation\TranslatorInterface;
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
     * @var TranslatorInterface
     */
    private $translator;

    /**
     * UserChecker constructor.
     * @param TranslatorInterface $translator
     */
    public function __construct(TranslatorInterface $translator)
    {
        $this->translator = $translator;
    }

    /**
     * @param UserInterface $user
     */
    public function checkPreAuth(UserInterface $user) : void
    {
        if (!$user instanceof User) {
            return;
        }

        // user is deleted, show a generic Account Not Found message.
        if ($user->isDeleted()) {
            //throw new AccountDeletedException('Account Deleted');

            // or to customize the message shown
            throw new CustomUserMessageAuthenticationException(
                $this->translator->trans('Your account has been deleted.'), [],1
            );
        }

        if (!$user->isActive()) {
            //throw new AccountDeletedException('Account Not Active');

            // or to customize the message shown
            throw new CustomUserMessageAuthenticationException(
                $this->translator->trans('Your account is not active.') , [], 2
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