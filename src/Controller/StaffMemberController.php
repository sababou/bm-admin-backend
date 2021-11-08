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


    /**
     * @Route("/staff_member/add", name="staff_member_add", methods={"POST"})
     */
    public function addMember(Request $request)
    {
        // Check if there are errors in fields
        $_err = $this->checkFields($request);

        if($_err){
          $responseData = array(
            "status" => "ERR",
            "err" => $_err
          );
        }else{

          // If there is no error, we build & save a new User
          $user = $this->buildUserFromRequest($request);

          $em = $this->getDoctrine()->getManager();
          $em->persist($user);
          $em->flush();

          $responseData = array(
            "status" => "OK"
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


    private function checkFields(Request $request) : ?string{

        $_err = null;

        // PARAMS recieved from POST request
        $email = $request->request->has('_email')?$request->request->get('_email'):null;
        $password = $request->request->has('_password')?$request->request->get('_password'):null;
        $name = $request->request->has('_name')?$request->request->get('_name'):null;
        $_token = $request->request->has('_token')?$request->request->get('_token'):null;

        // fields verification
        if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
          $_err = "L'adresse email n'est pas valide.";
        }else{
          $em = $this->getDoctrine()->getManager();
          $user = $em->getRepository(User::class)->findByEmail($email);
          if($user){
            $_err = "Cette adresse email est déjà attribuée à un utilisateur.";
          }
        }

        if (strlen($password) < 8) {
          $_err = "Le mot de passe doit contenir au moins 8 caractères.";
        }

        if (strlen($name) < 3) {
          $_err = "Le nom doit contenir au moins 3 caractères.";
        }

        if(!($this->isCsrfTokenValid('member-subscribe', $submittedToken))){
          $_err = "La validation a échoué.";
        }

        return $_err;
    }


    private function buildUserFromRequest(Request $request) : User {

        $email = $request->request->get('_email');
        $password = $request->request->get('_password');
        $name = $request->request->get('_name');

        $user = new User();

        $user->setEmail($email);
        $user->setName($name);
        // Encode the new users password
        $user->setPassword($this->passwordEncoder->encodePassword($user, $password));

        // Set their role
        $user->setRoles(['ROLE_USER']);

        return $user;
    }



}
