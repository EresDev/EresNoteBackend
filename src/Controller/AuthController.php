<?php
namespace App\Controller;

use App\Entity\User;
use App\Security\UserProvider;
use Doctrine\ORM\EntityManager;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Security\Core\Encoder\UserPasswordEncoderInterface;

class AuthController extends AbstractController
{
    public function register(Request $request, EntityManagerInterface $em, UserPasswordEncoderInterface $encoder)
    {
        $username = $request->get('username');
        $password = $request->get('password');
        //print_r($_POST); echo "1111"; exit;
        $user = new UserProvider();
        $user->setEmail($username);
        $user->setPassw($encoder->encodePassword($user, $password));

        $user2 = new User();
        $user2->setEmail($username);
        $user2->setPassw($user->getPassw());

        $em->persist( $user2);
        $em->flush();
        return new Response(sprintf('User %s successfully created', $user->getEmail()));
    }

    public function login(Request $request)
    {
        $json = $request->getContent();
        $vars = json_decode($json, true);
        $jwtManager = $this->container
            ->get('lexik_jwt_authentication.jwt_manager');
        $user = new User();
        return $this->json([
            'vars' => $vars,
            'message' => 'me',
            'lasdmessage' => 'me',
            'path' => 'pa',
            'jwtManager' => $jwtManager->create($user),
        ]);
    }
}