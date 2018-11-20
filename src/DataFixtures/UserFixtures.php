<?php
namespace App\DataFixtures;

use App\Entity\User;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Common\Persistence\ObjectManager;
use Symfony\Component\Security\Core\Encoder\UserPasswordEncoderInterface;

class UserFixtures extends Fixture
{
    public const TEST_USER_REFERENCE = 'test-user';

    private $encoder;

    public function __construct(UserPasswordEncoderInterface $encoder)
    {
        $this->encoder = $encoder;
    }

    /**
     * @param ObjectManager $manager
     * @return mixed
     */
    public function load(ObjectManager $manager)
    {
        $user_email = "arslanafzal321@gmail.com";
        $user_password = "3489hteur43xw21@1";
        $user = new User();
        $user->setEmail($user_email);

        $encoded = $this->encoder->encodePassword($user, $user_password);

        $user->setPassword($encoded);
        $user->setActive(true);
        $user->setDeleted(false);

        $manager->persist($user);

        $manager->flush();

        // other fixtures can get this object using the UserFixtures::ADMIN_USER_REFERENCE constant
        $this->addReference(self::TEST_USER_REFERENCE, $user);

    }
}