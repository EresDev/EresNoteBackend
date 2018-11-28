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

/**
 * Class AuthController
 * @package App\Controller
 */
class AuthController extends AbstractController
{
    /**
     * @param Request $request
     * @param TranslatorInterface $translator
     * @param EntityManagerInterface $em
     * @param UserPasswordEncoderInterface $encoder
     * @param FormErrorSerializer $formErrorSerializer
     * @return JsonResponse
     */
    public function register(Request $request, TranslatorInterface $translator, EntityManagerInterface $em, UserPasswordEncoderInterface $encoder, FormErrorSerializer $formErrorSerializer) : JsonResponse
    {
        $form = $this->createForm(UserType::class);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $user = $form->getData();
            $encoded = $encoder->encodePassword($user, $user->getPlainPassword());
            $user->setPassword($encoded);
            $em->persist( $user );
            $em->flush();

            return new JsonResponse(['status' => 'success' , 'details' => $translator->trans('User has been created successfully.')]);
        }

        return new JsonResponse(['status' => 'error' , 'details' => $formErrorSerializer->convertFormToArray($form)], Response::HTTP_BAD_REQUEST);
    }
}