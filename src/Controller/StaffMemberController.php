<?php

namespace App\Controller;

use App\Entity\User;
use App\Form\UserType;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Core\Encoder\UserPasswordEncoderInterface;


class StaffMemberController extends AbstractController
{
    private $passwordEncoder;

    public function __construct(UserPasswordEncoderInterface $passwordEncoder)
    {
        $this->passwordEncoder = $passwordEncoder;
    }


    ///////////////////////////////////////////////////////////////////////////////////////////////////////////
    ///////////////////////////////////////////////////////////////////////////////////////////////////////////

    //            GET     METHODS

    ///////////////////////////////////////////////////////////////////////////////////////////////////////////
    ///////////////////////////////////////////////////////////////////////////////////////////////////////////


    /**
     * @Route("/staff_member/get_csrf_token", methods={"GET"})
     */
    public function getToken(Request $request) : JsonResponse
    {
        $csrf = $this->get('security.csrf.token_manager');
        $token = $csrf->getToken('staff-member')->getValue();

        $responseData = array(
          "status" => "OK",
          "token" => $token
        );

        return new JsonResponse($responseData);
    }


    /**
     * @Route("/staff_member/list", methods={"GET"})
     */
    public function getStaffMemberList(Request $request) : JsonResponse
    {

        $role = $this->getRoleFromQuery($request);

        $users = array();

        $em = $this->getDoctrine()->getManager();

        if($role){
          $resultUsers = $em->getRepository(User::class)->findAllByRole($role);
        }else{
          $resultUsers = $em->getRepository(User::class)->findAll();
        }

        foreach($resultUsers as $user){
          $users[] = $user->buildJSONArray();
        }


        $responseData = array(
          "status" => "OK",
          "users" => $users
        );

        return new JsonResponse($responseData);
    }


    ///////////////////////////////////////////////////////////////////////////////////////////////////////////
    ///////////////////////////////////////////////////////////////////////////////////////////////////////////

    //            POST     METHODS

    ///////////////////////////////////////////////////////////////////////////////////////////////////////////
    ///////////////////////////////////////////////////////////////////////////////////////////////////////////


    /**
     * @Route("/staff_member/add", name="staff_member_add", methods={"POST"})
     */
    public function addMember(Request $request) : JsonResponse
    {

        try{

          $this->checkTokenValidity($request);
          $this->checkAddUserFields($request);


          $em = $this->getDoctrine()->getManager();

          // If there is no error, we build & save a new User
          $user = $this->buildUserFromRequest($request);

          $em = $this->getDoctrine()->getManager();
          $em->persist($user);
          $em->flush();

          $responseData = array(
            "status" => "OK"
          );


        }catch(\Exception $e){
          $responseData = array(
            "status" => "ERR",
            "err" => $e->getMessage()
          );
        }


        return new JsonResponse($responseData);
    }

    /////////////////////////////////////////////////////////////////////////////////////////////////////////////
    /////////////////////////////////////////////////////////////////////////////////////////////////////////////
    /////////////////////////////////////////////////////////////////////////////////////////////////////////////

    //                PRIVATE   METHODS

    /////////////////////////////////////////////////////////////////////////////////////////////////////////////
    /////////////////////////////////////////////////////////////////////////////////////////////////////////////
    /////////////////////////////////////////////////////////////////////////////////////////////////////////////

    private function checkTokenValidity(Request $request) : void{

        $_token = $request->request->has('_token')?$request->request->get('_token'):null;

        if(!($this->isCsrfTokenValid('staff-member', $_token))){
          throw new \Exception('ERR_VALIDATION');
        }

    }

    private function checkAddUserFields(Request $request) : void{

        $_err = null;

        // PARAMS recieved from POST request
        $email = $request->request->has('_email')?$request->request->get('_email'):null;
        $password = $request->request->has('_password')?$request->request->get('_password'):null;
        $name = $request->request->has('_name')?$request->request->get('_name'):null;
        $role = $request->request->has('_role')?$request->request->get('_role'):null;

        // fields verification
        if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
          throw new \Exception('ERR_INPUT_EMAIL');
        }else{
          $em = $this->getDoctrine()->getManager();
          $user = $em->getRepository(User::class)->findByEmail($email);
          if($user){
            throw new \Exception('ERR_EXISTING_USER');
          }
        }

        if (strlen($password) < 8) {
          throw new \Exception('ERR_INPUT_PASSWORD');
        }

        if (strlen($name) < 3) {
          throw new \Exception('ERR_INPUT_NAME');
        }

        if(($role != "ROLE_VALIDATION")
              && ($role != "ROLE_SHIPMENT")
              && ($role != "ROLE_DELIVERY")
              && ($role != "ROLE_RETURN")
              && ($role != "ROLE_CUSTOMER_SERVICE")){

          throw new \Exception('ERR_INPUT_ROLE');

        }
    }


    private function buildUserFromRequest(Request $request) : User {

        $email = $request->request->get('_email');
        $password = $request->request->get('_password');
        $name = $request->request->get('_name');
        $phone = $request->request->get('_phone');
        $role = $request->request->get('_role');

        $user = new User();

        $user->setEmail($email);
        $user->setName($name);
        // Encode the new users password
        $user->setPassword($this->passwordEncoder->encodePassword($user, $password));

        // Set their role
        $user->setRoles(['ROLE_USER', $role]);

        return $user;
    }



    private function getRoleFromQuery(Request $request) : ?string{
      $role = null;

      $queryRole = $request->query->has('role')?$request->query->has('role'):null;

      if(($queryRole == "ROLE_VALIDATION")
          || ($queryRole == "ROLE_SHIPMENT")
          || ($queryRole == "ROLE_DELIVERY")
          || ($queryRole == "ROLE_RETURN")){

        $role = $queryRole;
      }

      return $role;
    }


}
