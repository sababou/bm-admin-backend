<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\Routing\Annotation\Route;

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

        $orderCriterias = $this->getOrderCriteriasFromQuery();

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

          $resultOrders = $repository->findBy(
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





    ///////////////////////////////////////////////////////////////////////////////////////////////////////////
    ///////////////////////////////////////////////////////////////////////////////////////////////////////////

    //            POST     METHODS

    ///////////////////////////////////////////////////////////////////////////////////////////////////////////
    ///////////////////////////////////////////////////////////////////////////////////////////////////////////


    #[Route('/api/order/set_validated', name: 'order_update_status', methods:"POST")]
    public function setStatusValidated(Request $request): JsonResponse
    {

        try{

          $this->checkTokenValidity($request);
          $this->checkValidationFields($request);

          // Updating order
          $orderId = $request->request->has("_order_id") ? $request->request->get("_order_id") : null;
          $em = $this->getDoctrine()->getManager();
          $order = $em->getRepository(Order::class)->find($orderId);

          // set validation date
          $order->setValidationDate(new \DateTime());
          // set validated by
          $order->setValidatedBy($this->getUser());
          // set status
          $order->setStatus("VALIDATED");

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


    #[Route('/api/order/set_shipped', name: 'order_update_status', methods:"POST")]
    public function setStatusShipped(Request $request): JsonResponse
    {
        try{

          $this->checkTokenValidity($request);
          $this->checkShipmentFields($request);

          // Updating order
          $orderId = $request->request->has("_order_id") ? $request->request->get("_order_id") : null;
          $em = $this->getDoctrine()->getManager();
          $order = $em->getRepository(Order::class)->find($orderId);


          // set status : shipped
          $order->setStatus("SHIPPED");

          // set shipped by
          $order->setShippeddBy($this->getUser());

          // set shipment date
          $order->setShipmentDate(new \DateTime());

          // set entrustedTo
          $entrustedTo = $request->request->has("_entrusted_to") ? $request->request->get("_entrusted_to") : null;
          $userEntrustedTo = $em->getRepository(User::class)->find($entrustedTo);
          $order->setEntrustedTo($userEntrustedTo);


          if($order->getCommune()->getWilaya()->getId() != 16){
            $barcodeContent = $request->request->has("_barcode_content") ? $request->request->get("_barcode_content") : null;
            $order->setBarcodeContent($barcodeContent);
          }
          // set barcode


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

    #[Route('/api/order/set_delivered', name: 'order_update_status', methods:"POST")]
    public function setStatusDelivered(Request $request): JsonResponse
    {

        try{

          $this->checkTokenValidity($request);
          $this->checkDeliveryFields($request);

          // Updating order
          $orderId = $request->request->has("_order_id") ? $request->request->get("_order_id") : null;
          $em = $this->getDoctrine()->getManager();
          $order = $em->getRepository(Order::class)->find($orderId);

          // set delivery date


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


    #[Route('/api/order/set_returned', name: 'order_update_status', methods:"POST")]
    public function setStatusReturned(Request $request): JsonResponse
    {
        try{

          $this->checkTokenValidity($request);
          $this->checkReturnFields($request);

          // Updating order
          $orderId = $request->request->has("_order_id") ? $request->request->get("_order_id") : null;
          $em = $this->getDoctrine()->getManager();
          $order = $em->getRepository(Order::class)->find($orderId);

          // set return date
          // set returned by


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

        if(!($this->isCsrfTokenValid('update-order-status', $_token))){
          throw new \Exception('ERR_VALIDATION');
        }

    }


    private function checkValidationFields(Request $request) : void
    {
        $orderId = $request->request->has("_order_id") ? $request->request->get("_order_id") : null;
        if($orderId == null){
          throw new \Exception("ERR_INPUT_ORDER");
        }

        $em = $this->getDoctrine()->getManager();

        $order = $em->getRepository(Order::class)->find($orderId);
        if($order == null){
          throw new \Exception("ERR_NO_MATCHING_ORDER");
        }

        if($order->getValidationDate()){
          throw new \Exception("ERR_ALREADY_VALIDATED");
        }
    }

    private function checkShipmentFields(Request $request) : void
    {
        $orderId = $request->request->has("_order_id") ? $request->request->get("_order_id") : null;
        if($orderId == null){
          throw new \Exception("ERR_INPUT_ORDER");
        }

        $em = $this->getDoctrine()->getManager();
        $order = $em->getRepository(Order::class)->find($orderId);
        if($order == null){
          throw new \Exception("ERR_NO_MATCHING_ORDER");
        }

        if($order->getShipmentDate()){
          throw new \Exception("ERR_ALREADY_SHIPPED");
        }
    }

    private function checkDeliveryFields(Request $request) : void
    {
        $orderId = $request->request->has("_order_id") ? $request->request->get("_order_id") : null;
        if($orderId == null){
          throw new \Exception("ERR_INPUT_ORDER");
        }

        $em = $this->getDoctrine()->getManager();
        $order = $em->getRepository(Order::class)->find($orderId);
        if($order == null){
          throw new \Exception("ERR_NO_MATCHING_ORDER");
        }

        if($order->getDeliveryDate()){
          throw new \Exception("ERR_ALREADY_DELIVERED");
        }
    }

    private function checkReturnFields(Request $request) : void
    {
        $orderId = $request->request->has("_order_id") ? $request->request->get("_order_id") : null;
        if($orderId == null){
          throw new \Exception("ERR_INPUT_ORDER");
        }

        $em = $this->getDoctrine()->getManager();
        $order = $em->getRepository(Order::class)->find($orderId);
        if($order == null){
          throw new \Exception("ERR_NO_MATCHING_ORDER");
        }

        if($order->getReturnDate()){
          throw new \Exception("ERR_ALREADY_RETURNED");
        }
    }






    private function getOrderCriteriasFromQuery(Request $request) : ?array{
      $_arr = array();

      $queryStatus = $request->query->has('status')?$request->query->has('status'):null;
      $queryCustomerPhone = $request->query->has('customer_phone')?$request->query->has('customer_phone'):null;

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



}
