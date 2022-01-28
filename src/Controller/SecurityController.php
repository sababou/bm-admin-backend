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

        $_phone = array_key_exists("_phone", $data) ? $data['_phone'] : 0;
        $_loginToken = array_key_exists("_token", $data) ? $data['_token'] : 0;

        $_err = null;
        $_token = null;
        $_userdata = null;

        $em = $this->getDoctrine()->getManager();

        $user = $em->getRepository(User::class)->findOneByPhone($_phone);

        if($user){

          $loginToken = $em->getRepository(LoginToken::class)->findOneByToken($_loginToken);

          if($loginToken){

          	if (new \DateTime() > $loginToken->getLimitDate()) {
							$_status = "ERR";
            	$_err = "TOKEN_EXPIRED";
						} else {

							$_token = $JWTManager->create($user);

			        $loginTrace = new LoginTrace();
		          $loginTrace->setUser($user);
		          $loginTrace->setDate(new \DateTime());
		          $loginTrace->setLoginToken($loginToken);

		          $em->persist($loginTrace);
		          $em->flush();
		          
		          $_userdata = array(
		          	"user_name" => $user->getName(),
		          	"user_roles" => $user->getRoles()
		          );

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
          "token" => $_token,
          "userdata" => $_userdata
          
        );

        $response = new JsonResponse($responseData);
        return $response;
    }
    
    
    
    #[Route('/api/check_login_status', name: 'app_check_login_status')]
    public function checkLoginStatus(Request $request): JsonResponse
    {
				$user = $this->getUser();
				
        $responseData = array(
          "status" => "ALREADY_LOGGED",
          "user_name" => $user->getName(),
	      	"user_roles" => $user->getRoles()
        );

        $response = new JsonResponse($responseData);
        return $response;
    }


}
