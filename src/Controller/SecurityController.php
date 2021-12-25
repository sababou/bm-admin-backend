<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Http\Authentication\AuthenticationUtils;

class SecurityController extends AbstractController
{

    #[Route('/login', name: 'app_login')]
    public function login(AuthenticationUtils $authenticationUtils): JsonResponse
    {
        // get the login error if there is one
        $error = $authenticationUtils->getLastAuthenticationError();
        // last username entered by the user
        $lastUsername = $authenticationUtils->getLastUsername();

        $_status = "NOT_LOGGED";

        if($this->getUser()){
          $_status = "ALREADY_LOGGED";
        }

        $responseData = array(
          "status" => $_status,
          "error" => $error
        );

        $response = new JsonResponse($responseData);
        return $response;
    }



    #[Route('/first_step_login', name: 'first_step_login')]
    public function firstStepLogin(Request $request): JsonResponse
    {
        $userPhone = $request->request->has('_phone')?$request->request->get('_phone'):null;

        $_err = null;

        $em = $this->getDoctrine()->getManager();

        $user = $em->getRepository(User::class)->findOneByPhone($userPhone);

        if($user){
          $_status = "OK";
        }else{
          $_status = "ERR";
          $_err = "NO_MATCHING_USER";
        }

        $responseData = array(
          "status" => $_status,
          "error" => $_err
        );

        $response = new JsonResponse($responseData);
        return $response;
    }


    #[Route('/second_step_login', name: 'second_step_login')]
    public function secondStepLogin(Request $request): JsonResponse
    {
        $userPhone = $request->request->has('_phone')?$request->request->get('_phone'):null;

        $_err = null;

        $em = $this->getDoctrine()->getManager();

        $user = $em->getRepository(User::class)->findOneByPhone($userPhone);

        if($user){

          $loginToken = $em->getRepository(LoginToken::class)->getActual();

          if($loginToken){
            $guardHandler->authenticateUserAndHandleSuccess(
                $user,
                $request,
                $formAuthenticator,
                'main'
            );

            $loginTrace = new LoginTrace();
            $loginTrace->setUser($user);
            $loginTrace->setDate(new \DateTime());
            $loginTrace->setLoginToken($loginToken);

            $em->persist($loginTrace);
            $em->flush();

            $_status = "OK";
          }else{
            $_status = "ERR";
            $_err = "NO_MATCHING_TOKEN";
          }

        }else{
          $_status = "ERR";
          $_err = "NO_MATCHING_USER";
        }

        $responseData = array(
          "status" => $_status,
          "error" => $_err
        );

        $response = new JsonResponse($responseData);
        return $response;
    }


    #[Route('/logout', name: 'app_logout')]
    public function logout()
    {
        throw new \Exception('This method can be blank - it will be intercepted by the logout key on your firewall');
    }
}
