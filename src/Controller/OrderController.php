<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\Routing\Annotation\Route;

use App\Entity\Order;
use App\Entity\User;
use App\Entity\OperationTrace;

class OrderController extends AbstractController
{

    ///////////////////////////////////////////////////////////////////////////////////////////////////////////
    ///////////////////////////////////////////////////////////////////////////////////////////////////////////

    //            GET     METHODS

    ///////////////////////////////////////////////////////////////////////////////////////////////////////////
    ///////////////////////////////////////////////////////////////////////////////////////////////////////////




    #[Route('/api/order/list', name: 'order_list', methods:"GET")]
    public function getOrderList(Request $request): JsonResponse
    {
        $em = $this->getDoctrine()->getManager();

        $orderCriterias = $this->getOrderCriteriasFromQuery($request);

        $queryLimit = 21 ; // in Front-End, we print 20 results, we put the button "SEE MORE" if size or array is 21
        $queryOffset = $request->query->has('offset') ? $request->query->get('offset') : 0 ;

        if($orderCriterias == null){
          $responseData = array(
            "status" => "INVALID_CRITERIAS"
          );
        }else{
          $_orderBy = "DESC";

          if($orderCriterias["status"] == "SAVED"){
            $_orderBy = "ASC";
          }

          $orders = array();



          $resultOrders = $em->getRepository(Order::class)->findBy(
            $orderCriterias,
            array('id' => $_orderBy),
            $queryLimit,
            $queryOffset
          );

          foreach($resultOrders as $order){
            $orders[] = $order->buildJSONArray();
          }

          $responseData = array(
            "status" => "OK",
            "orders" => $orders
          );

        }

        return new JsonResponse($responseData);
    }
    
    
    /**
     * @Route("/api/order/entrusted_to_list", methods={"GET"})
     */
    public function getEntrustedToList(Request $request) : JsonResponse
    {

        $users = array();

        $em = $this->getDoctrine()->getManager();

        $resultUsers = $em->getRepository(User::class)->findAllByRole("ROLE_ENTRUSTED_TO");

        foreach($resultUsers as $user){
        	$userArr = $user->buildJSONArray();


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


    #[Route('/api/order/set_validated', name: 'order_status_set_validated', methods:"POST")]
    public function setStatusValidated(Request $request): JsonResponse
    {

        try{

          $this->checkValidationFields($request);

          // Updating order
          $data = json_decode($request->getContent(), true);
    			$orderId = $data['_order_id'];

          $em = $this->getDoctrine()->getManager();
          $order = $em->getRepository(Order::class)->find($orderId);

//        $user = $this->getUser();
					$user = $em->getRepository(User::class)->find(1);

          // set validation date
          $order->setValidationDate(new \DateTime());
          // set validated by
          $order->setValidatedBy($user);
          // set status
          $order->setStatus("VALIDATED");

          $this->createOperationTrace($user, "VALIDATE - #".$orderId);

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


    #[Route('/api/order/set_shipped', name: 'order_status_set_shipped', methods:"POST")]
    public function setStatusShipped(Request $request): JsonResponse
    {
        try{

          $this->checkShippingFields($request);

          // Updating order
					$data = json_decode($request->getContent(), true);
    			$orderId = $data['_order_id'];
          $em = $this->getDoctrine()->getManager();
          $order = $em->getRepository(Order::class)->find($orderId);

//        $user = $this->getUser();
					$user = $em->getRepository(User::class)->find(1);


          // set status : shipped
          $order->setStatus("SHIPPED");

          // set shipped by
          $order->setShippedBy($user);

          // set shipping date
          $order->setShippingDate(new \DateTime());

          // set entrustedTo
    			$entrustedTo = $data['_entrusted_to'];
          $userEntrustedTo = $em->getRepository(User::class)->find($entrustedTo);
          $order->setEntrustedTo($userEntrustedTo);

          // set barcode
          if($order->getCommune()->getWilaya()->getId() != 16){
						$barcodeContent = $data['_barcode_content'];
            $order->setDeliveryBarcode($barcodeContent);
          }

          $this->createOperationTrace($user, "SHIPPED - #".$orderId);

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

    #[Route('/api/order/set_delivered', name: 'order_status_set_delivered', methods:"POST")]
    public function setStatusDelivered(Request $request): JsonResponse
    {

        try{

          $this->checkDeliveryFields($request);

          // Updating order
					$data = json_decode($request->getContent(), true);
    			$orderId = $data['_order_id'];
          $em = $this->getDoctrine()->getManager();
          $order = $em->getRepository(Order::class)->find($orderId);

          //$user = $this->getUser();
					$user = $em->getRepository(User::class)->find(1);

					$order->setStatus("DELIVERED");
          // set delivery date
          $order->setDeliveryDate(new \DateTime());

          $order->setDeliveredBy($user);

          $this->createOperationTrace($user, "DELIVERED - #".$orderId);

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


    #[Route('/api/order/set_returned', name: 'order_status_set_returned', methods:"POST")]
    public function setStatusReturned(Request $request): JsonResponse
    {
        try{

          $this->checkReturnFields($request);

          // Updating order
          $data = json_decode($request->getContent(), true);
    			$orderId = $data['_order_id'];
          $em = $this->getDoctrine()->getManager();
          $order = $em->getRepository(Order::class)->find($orderId);

          $order->setStatus("RETURNED");

          //$user = $this->getUser();
					$user = $em->getRepository(User::class)->find(1);


          // set delivery date
          $order->setReturnDate(new \DateTime());

          $order->setReturnedBy($user);

          $this->createOperationTrace($user, "RETURNED - #".$orderId);

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



    #[Route('/api/order/set_canceled', name: 'order_status_set_canceled', methods:"POST")]
    public function setStatusCanceled(Request $request): JsonResponse
    {

        try{

          $this->checkCancelFields($request);

          // Updating order
          $data = json_decode($request->getContent(), true);
    			$orderId = $data['_order_id'];
          $em = $this->getDoctrine()->getManager();
          $order = $em->getRepository(Order::class)->find($orderId);

          $order->setStatus("CANCELED");

          //$user = $this->getUser();
					$user = $em->getRepository(User::class)->find(1);

          // set validation date
          $order->setCancelDate(new \DateTime());
          // set validated by
          $order->setCanceledBy($user);
          // set status
          $order->setStatus("CANCELED");

          $this->createOperationTrace($user, "CANCEL - #".$orderId);

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


    private function checkValidationFields(Request $request) : void
    {
    		$data = json_decode($request->getContent(), true);
    		$orderId = null;

    		if(array_key_exists('_order_id', $data)){
					$orderId = $data['_order_id'];
				}

        if($orderId == null){
          throw new \Exception("ERR_INPUT_ORDER");
        }

        $em = $this->getDoctrine()->getManager();

        $order = $em->getRepository(Order::class)->find($orderId);
        if($order == null){
          throw new \Exception("ERR_NO_MATCHING_ORDER");
        }else{
        	if($order->getStatus() != "SAVED"){
        		throw new \Exception("ERR_ORDER_STATUS_MISUSE");
        	}
        }

        if($order->getValidationDate()){
          throw new \Exception("ERR_ALREADY_VALIDATED");
        }
    }

    private function checkShippingFields(Request $request) : void
    {
        $data = json_decode($request->getContent(), true);
    		$orderId = null;

    		if(array_key_exists('_order_id', $data)){
					$orderId = $data['_order_id'];
				}
        if($orderId == null){
          throw new \Exception("ERR_INPUT_ORDER");
        }

        $em = $this->getDoctrine()->getManager();
        $order = $em->getRepository(Order::class)->find($orderId);
        if($order == null){
          throw new \Exception("ERR_NO_MATCHING_ORDER");
        }else{
					if($order->getCommune()->getWilaya()->getId() != 16){
						$barcodeContent = null;
					  if(array_key_exists('_barcode_content', $data)){
							$barcodeContent = $data['_barcode_content'];
							if(!(strlen($barcodeContent) > 1)){
								$barcodeContent = null;
							}
						}
					  if($barcodeContent == null){
					    throw new \Exception("ERR_INPUT_BARCODE");
					  }
          }


        	if($order->getStatus() != "VALIDATED"){
        		throw new \Exception("ERR_ORDER_STATUS_MISUSE");
        	}

        }

        $entrustedTo = null;
        if(array_key_exists('_entrusted_to', $data)){
					$entrustedTo = $data['_entrusted_to'];
				}
        if($entrustedTo == null){
          throw new \Exception("ERR_INPUT_ENTRUST_TO");
        }

        $em = $this->getDoctrine()->getManager();
        $user = $em->getRepository(User::class)->find($entrustedTo);
        if($user == null){
          throw new \Exception("ERR_NO_MATCHING_USER");
        }

        if($order->getShippingDate()){
          throw new \Exception("ERR_ALREADY_SHIPPED");
        }
    }

    private function checkDeliveryFields(Request $request) : void
    {
        $data = json_decode($request->getContent(), true);
    		$orderId = null;

    		if(array_key_exists('_order_id', $data)){
					$orderId = $data['_order_id'];
				}
        if($orderId == null){
          throw new \Exception("ERR_INPUT_ORDER");
        }

        $em = $this->getDoctrine()->getManager();
        $order = $em->getRepository(Order::class)->find($orderId);
        if($order == null){
          throw new \Exception("ERR_NO_MATCHING_ORDER");
        }else{
        	if($order->getStatus() != "SHIPPED"){
        		throw new \Exception("ERR_ORDER_STATUS_MISUSE");
        	}
        }

        if($order->getDeliveryDate()){
          throw new \Exception("ERR_ALREADY_DELIVERED");
        }
    }

    private function checkReturnFields(Request $request) : void
    {
        $data = json_decode($request->getContent(), true);
    		$orderId = null;

    		if(array_key_exists('_order_id', $data)){
					$orderId = $data['_order_id'];
				}
        if($orderId == null){
          throw new \Exception("ERR_INPUT_ORDER");
        }

        $em = $this->getDoctrine()->getManager();
        $order = $em->getRepository(Order::class)->find($orderId);
        if($order == null){
          throw new \Exception("ERR_NO_MATCHING_ORDER");
        }else{
        	if($order->getStatus() != "SHIPPED"){
        		throw new \Exception("ERR_ORDER_STATUS_MISUSE");
        	}
        }

        if($order->getReturnDate()){
          throw new \Exception("ERR_ALREADY_RETURNED");
        }
    }


    private function checkCancelFields(Request $request) : void
    {
    		$data = json_decode($request->getContent(), true);
    		$orderId = null;

    		if(array_key_exists('_order_id', $data)){
					$orderId = $data['_order_id'];
				}

        if($orderId == null){
          throw new \Exception("ERR_INPUT_ORDER");
        }

        $em = $this->getDoctrine()->getManager();

        $order = $em->getRepository(Order::class)->find($orderId);
        if($order == null){
          throw new \Exception("ERR_NO_MATCHING_ORDER");
        }else{
        	if($order->getStatus() == "RETURNED" || $order->getStatus() == "CANCELED" || $order->getStatus() == "DELIVERED"){
        		throw new \Exception("ERR_ORDER_STATUS_MISUSE");
        	}
        }

        if($order->getValidationDate()){
          throw new \Exception("ERR_ALREADY_VALIDATED");
        }
    }






    private function getOrderCriteriasFromQuery(Request $request) : ?array{
      $_arr = array();

      $queryStatus = $request->query->has('status')?$request->query->get('status'):null;
      $queryCustomerPhone = $request->query->has('customer_phone')?$request->query->get('customer_phone'):null;

      // CRITERIA : STATUS
      if(($queryStatus == "SAVED")
          || ($queryStatus == "VALIDATED")
          || ($queryStatus == "SHIPPED")
          || ($queryStatus == "DELIVERED")
          || ($queryStatus == "RETURNED")){

        $_arr["status"] = $queryStatus;
      }

      // CRITERIA : CUSTOMER PHONE
      // first : remove all unncessary caracters : ".,/-" etc...
      preg_replace("/[^0-9]/", "", $queryCustomerPhone);

      // than check if the phone matches a real phone number
      $mobileregex = "/^[0][0-9]{9}$/" ;
      if(preg_match($mobileregex, $queryCustomerPhone)){
        $_arr["customerPhone"] = $queryCustomerPhone;
      }

      if(count($_arr) > 0){
        return $_arr;
      }else{
        return null;
      }

    }


    private function createOperationTrace(User $user, string $operation){
    	$em = $this->getDoctrine()->getManager();

      $operationTrace = new OperationTrace();
      $operationTrace->setUser($user);
      $operationTrace->setDate(new \DateTime());
      $operationTrace->setOperation($operation);

      $em->persist($operationTrace);
      $em->flush();
    }



}
