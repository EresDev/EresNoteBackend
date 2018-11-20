<?php
namespace App\Security\Encoder;

use Symfony\Component\Security\Core\Encoder\BCryptPasswordEncoder;
use Symfony\Component\Security\Core\Exception\BadCredentialsException;
/**
 * This class added a feature of minimum password length check in BCryptPasswordEncoder
 */
class BCryptPasswordEncoderPlus extends BCryptPasswordEncoder
{
    const MIN_PASSWORD_LENGTH = 6;

    private $cost;
    /**
     * @param int $cost The algorithmic cost that should be used
     *
     * @throws \RuntimeException         When no BCrypt encoder is available
     * @throws \InvalidArgumentException if cost is out of range
     */
    public function __construct(int $cost)
    {
       parent::__construct($cost);
       $this->cost = $cost;
    }
    /**
     * Encodes the raw password.
     *
     * It doesn't work with PHP versions lower than 5.3.7, since
     * the password compat library uses CRYPT_BLOWFISH hash type with
     * the "$2y$" salt prefix (which is not available in the early PHP versions).
     *
     * @see https://github.com/ircmaxell/password_compat/issues/10#issuecomment-11203833
     *
     * It is almost best to **not** pass a salt and let PHP generate one for you.
     *
     * @param string $raw  The password to encode
     * @param string $salt The salt
     *
     * @return string The encoded password
     *
     * @throws BadCredentialsException when the given password is too long
     *
     * @see http://lxr.php.net/xref/PHP_5_5/ext/standard/password.c#111
     */
    public function encodePassword($raw, $salt) : string
    {
        if (parent::isPasswordTooLong($raw)) {
            throw new BadCredentialsException('Invalid password. Too long. Max length acceptable: ' . 72);
        }

        if ($this->isPasswordTooShort($raw)) {
            throw new BadCredentialsException('Invalid password. Too short. Min length required: '. self::MIN_PASSWORD_LENGTH);
        }

        $options = array('cost' => $this->cost);
        if ($salt) {
            // Ignore $salt, the auto-generated one is always the best
        }
        return password_hash($raw, PASSWORD_BCRYPT, $options);
    }
    /**
     * {@inheritdoc}
     */
    public function isPasswordValid($encoded, $raw, $salt) : bool
    {
        return !parent::isPasswordTooLong($raw) && !$this->isPasswordTooShort($raw) && password_verify($raw, $encoded);
    }

    /**
     * @param string $plainPassword
     * @return bool
     */
    public function isPasswordTooShort($plainPassword) : bool
    {
        if (strlen($plainPassword) < self::MIN_PASSWORD_LENGTH){
            return true;
        }
        return false;
    }
}