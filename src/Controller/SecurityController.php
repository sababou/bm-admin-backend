<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Http\Authentication\AuthenticationUtils;
use Lexik\Bundle\JWTAuthenticationBundle\Services\JWTTokenManagerInterface;


use Symfony\Component\Security\Guard\GuardAuthenticatorHandler;
use App\Security\LoginFormAuthenticator;

use App\Entity\User;
use App\Entity\LoginToken;
use App\Entity\LoginTrace;


class SecurityController extends AbstractController
{

    #[Route('/api/login', name: 'app_login')]
    public function login(Request $request, JWTTokenManagerInterface $JWTManager): JsonResponse
    {
        $data = json_decode($request->getContent(), true);
    		
        //$userPhone = array_key_exists("_phone", $data) ? $data['_phone'] : 0;
        //$token = array_key_exists("_token", $data) ? $data['_token'] : 0;
        
        $userPhone = $request->query->get('_phone');
        $token = $request->query->get('_token');

        $_err = null;
        $_token = null;

        $em = $this->getDoctrine()->getManager();

        $user = $em->getRepository(User::class)->findOneByPhone($userPhone);

        if($user){

          $loginToken = $em->getRepository(LoginToken::class)->findOneByToken($token);

          if($loginToken){
          
          	if (new \DateTime() > $loginToken->getLimitDate()) {
							$_status = "ERR";
            	$_err = "TOKEN_EXPIRED";
						} else {
						
						/*
							$guardHandler->authenticateUserAndHandleSuccess(
                $user,
                $request,
                $loginAuthenticator,
                'main'
            	);
            	*/
            	//$JWTManager->create($user)
							$_token = $JWTManager->create($user,10);
            	
			        
			        $loginTrace = new LoginTrace();
		          $loginTrace->setUser($user);
		          $loginTrace->setDate(new \DateTime());
		          $loginTrace->setLoginToken($loginToken);

		          $em->persist($loginTrace);
		          $em->flush();

		          $_status = "OK";
						}  
            
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
          "error" => $_err,
          "token" => $_token
        );

        $response = new JsonResponse($responseData);
        return $response;
    }



    #[Route('/api/first_step_login', name: 'first_step_login')]
    public function firstStepLogin(Request $request): JsonResponse
    {
        $data = json_decode($request->getContent(), true);
        $userPhone = array_key_exists("_phone", $data) ? $data['_phone']:0;
        $_username = "";

        $_err = null;

        $em = $this->getDoctrine()->getManager();

        $user = $em->getRepository(User::class)->findOneByPhone($userPhone);


        if($user){
          $_status = "OK";
          $_username = $user->getName();
        }else{
          $_status = "ERR";
          $_err = "NO_MATCHING_USER";
        }

        $responseData = array(
          "status" => $_status,
          "error" => $_err,
          "user_name" => $_username
        );

        $response = new JsonResponse($responseData);
        return $response;
    }


    #[Route('/api/second_step_login', name: 'second_step_login')]
    public function secondStepLogin(Request $request, GuardAuthenticatorHandler $guardHandler, LoginFormAuthenticator $loginAuthenticator): JsonResponse
    {
    		$data = json_decode($request->getContent(), true);
    		
        //$userPhone = array_key_exists("_phone", $data) ? $data['_phone'] : 0;
        //$token = array_key_exists("_token", $data) ? $data['_token'] : 0;
        
        $userPhone = $request->query->get('_phone');
        $token = $request->query->get('_token');

        $_err = null;
        $_token = null;

        $em = $this->getDoctrine()->getManager();

        $user = $em->getRepository(User::class)->findOneByPhone($userPhone);

        if($user){

          $loginToken = $em->getRepository(LoginToken::class)->findOneByToken($token);

          if($loginToken){
          
          	if (new \DateTime() > $loginToken->getLimitDate()) {
							$_status = "ERR";
            	$_err = "TOKEN_EXPIRED";
						} else {
						
						/*
							$guardHandler->authenticateUserAndHandleSuccess(
                $user,
                $request,
                $loginAuthenticator,
                'main'
            	);
            	*/
            	
							$_token = $this->get("lexik_jwt_authentication.encoder")->encode(array("username" => $username, "exp" => $expirationTime));
            	
			        
			        $loginTrace = new LoginTrace();
		          $loginTrace->setUser($user);
		          $loginTrace->setDate(new \DateTime());
		          $loginTrace->setLoginToken($loginToken);

		          $em->persist($loginTrace);
		          $em->flush();

		          $_status = "OK";
						}  
            
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
          "error" => $_err,
          "token" => $_token
        );

        $response = new JsonResponse($responseData);
        return $response;
    }


    #[Route('/api/logout', name: 'app_logout')]
    public function logout()
    {
        throw new \Exception('This method can be blank - it will be intercepted by the logout key on your firewall');
    }
}
