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
     * @Route("/api/staff_member/get_csrf_token", methods={"GET"})
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
     * @Route("/api/staff_member/list", methods={"GET"})
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
        	$userArr = $user->buildJSONArray();

        	$lastLogin = $em->getRepository(LoginTrace::class)->findOneByUser($user, ["id"=>"DESC"]);
        	$lastOperation = $em->getRepository(LoginTrace::class)->findOneByUser($user, ["id"=>"DESC"]);

        	$userArr['last_login'] = $lastLogin->getDate()->format('Y-m-d H:i:s');
        	$userArr['last_operation'] = array(
        																"operation_type" => $lastOperation->getOperation(), 
        																"operation_date" => $lastOperation->getDate()->format('Y-m-d H:i:s')
      																);

          $users[] = $userArr;
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
     * @Route("/api/staff_member/add", name="staff_member_add", methods={"POST"})
     */
    public function addMember(Request $request) : JsonResponse
    {
        $data = json_decode($request->getContent(), true);

        try{

          $this->checkTokenValidity($data);
          $this->checkAddUserFields($data);

          $em = $this->getDoctrine()->getManager();

          // If there is no error, we build & save a new User
          $user = $this->buildUserFromRequest($data);

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

    private function checkTokenValidity(array $data) : void{



        $_token = $data['_token'];

        if(!($this->isCsrfTokenValid('staff-member', $_token))){
          throw new \Exception('ERR_VALIDATION');
        }

    }

    private function checkAddUserFields(array $data) : void{

        $_err = null;

        // PARAMS recieved from POST request
        $email = array_key_exists('_email', $data) ? $data['_email'] : null ;
        $password = array_key_exists('_password', $data) ? $data['_password'] : null ;
        $name = array_key_exists('_name', $data) ? $data['_name'] : null ;
        $phone = array_key_exists('_phone', $data) ? $data['_phone'] : null ;
        $role = array_key_exists('_role', $data) ? $data['_role'] : null ;

        // fields verification
        if (strlen($name) < 3) {
          throw new \Exception('ERR_INPUT_NAME');
        }

        if(!preg_match("/^[0]{1}[0-9]{9}$/", $phone)) {
          throw new \Exception('ERR_INPUT_PHONE');
        }

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


        if(($role != "ROLE_VALIDATION")
              && ($role != "ROLE_SHIPPING")
              && ($role != "ROLE_DELIVERY_ENTRUST")
              && ($role != "ROLE_DELIVERY_DONE")
              && ($role != "ROLE_CANCEL")
              && ($role != "ROLE_RETURN")){




          throw new \Exception('ERR_INPUT_ROLE');

        }
    }


    private function buildUserFromRequest(array $data) : User {

        $email = $data['_email'];
        $password = $data['_password'];
        $name = $data['_name'];
        $phone = $data['_phone'];
        $role = $data['_role'];

        $user = new User();

        $user->setEmail($email);
        $user->setName($name);
        $user->setPhone($phone);
        // Encode the new users password
        $user->setPassword($this->passwordEncoder->encodePassword($user, $password));

        // Set their role
        $user->setRoles(['ROLE_USER', $role]);

        return $user;
    }



    private function getRoleFromQuery(Request $request) : ?string{
      $role = null;

      $queryRole = $request->query->has('role')?$request->query->get('role'):null;

      if(($queryRole == "ROLE_VALIDATION")
          || ($queryRole == "ROLE_SHIPPING")
          || ($queryRole == "ROLE_DELIVERY_ENTRUST")
          || ($queryRole == "ROLE_DELIVERY_DONE")
          || ($queryRole == "ROLE_CANCEL")
          || ($queryRole == "ROLE_RETURN")){

        $role = $queryRole;
      }

      return $role;
    }


}
