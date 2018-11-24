<?php
namespace App\Controller;

use App\Entity\User;

use Doctrine\ORM\EntityManagerInterface;
use Lexik\Bundle\JWTAuthenticationBundle\Services\JWTTokenManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Security\Core\Authentication\AuthenticationManagerInterface;
use Symfony\Component\Security\Core\Encoder\UserPasswordEncoderInterface;
use Symfony\Component\Security\Csrf\TokenStorage\TokenStorageInterface;


class AuthController extends AbstractController
{
    public function register(Request $request, EntityManagerInterface $em, UserPasswordEncoderInterface $encoder)
    {
        $email = $request->get('email');
        $password = $request->get('passw');

        $user = new User();
        $user->setEmail($email);

        $encoded = $encoder->encodePassword($user, $password);
        $user->setPassword($encoded);

        $em->persist( $user );
        $em->flush();
        return new Response(sprintf('User %s successfully created', $user->getEmail()));
    }

    public function login(Request $request, JWTTokenManagerInterface $jwtManager, UserPasswordEncoderInterface $encoder, TokenStorageInterface $tokenStorage, AuthenticationManagerInterface $provider)
    {
        $user = $this->getUser();

        return $this->json(array(
            'username' => $user->getEmail(),
            'roles' => $user->getRoles(),
        ));

//        $json = $request->getContent();
//        $vars = json_decode($json, true);
//
//        $user = $tokenStorage->getToken($vars['email'])->getUser();
//
////        $user = new User();
////        $user->setEmail($vars['email']);
////        $user->setPassword($encoder->encodePassword($user, $vars['passw']));
//        //$jwt = $jwtManager->create($user);
//        print_r($user);
//
//        exit;
    }

    public function testLogin(){
//        $u = new User();
//        $u->setEmail(1);
        return new Response("Willkommen");
    }
}