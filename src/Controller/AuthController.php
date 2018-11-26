<?php
namespace App\Controller;

use App\Form\UserType;
use App\Serializer\FormErrorSerializer;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Security\Core\Encoder\UserPasswordEncoderInterface;
use Symfony\Component\Translation\TranslatorInterface;


class AuthController extends AbstractController
{
    public function register(Request $request, TranslatorInterface $translator, EntityManagerInterface $em, UserPasswordEncoderInterface $encoder, FormErrorSerializer $formErrorSerializer)
    {
        $form = $this->createForm(UserType::class);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $user = $form->getData();
            $encoded = $encoder->encodePassword($user, $user->getPlainPassword());
            $user->setPassword($encoded);
            $em->persist( $user );
            $em->flush();

            return new Response($translator->trans('User has been created successfully.'));
        }

        return new JsonResponse($formErrorSerializer->convertFormToArray($form), Response::HTTP_BAD_REQUEST);
    }
}