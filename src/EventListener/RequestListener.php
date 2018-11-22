<?php
namespace App\EventListener;

use App\Entity\User;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpKernel\Debug\TraceableEventDispatcher;
use Symfony\Component\HttpKernel\Event\GetResponseEvent;
use Symfony\Component\Validator\Validator\ValidatorInterface;

class RequestListener
{
    private $validator;

    public function __construct( ValidatorInterface $validator)
    {
        $this->validator = $validator;
    }

    public function onLoginCheckValidateCredentials(GetResponseEvent $event,  $kernal_event_name, TraceableEventDispatcher $dispatcher)
    {
        if ( $event->isMasterRequest() ) {

            $request = $event->getRequest();

            $uri = $request->getRequestUri();

            if ('/api/login_check' == $uri) {
                $content = json_decode($request->getContent());
                if( isset($content->email) && isset($content->passw) ){
                    $user = new User();
                    $user->setEmail($content->email);
                    $user->setPassword($content->passw);
                    $errors = $this->validator->validate($user);

                    if($errors->count()){
                        $response = new JsonResponse(['code' => 422, 'message' => $errors[0]->getMessage()]);
                        $response->setStatusCode(422 );
                        $event->setResponse($response);
                        $event->stopPropagation();
                    }
                }
                //JWT bundle is already handling missing email or passw
            }
        }
    }
}