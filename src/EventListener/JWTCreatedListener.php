<?php

namespace App\EventListener;

use Lexik\Bundle\JWTAuthenticationBundle\Event\JWTCreatedEvent;

use App\Entity\LoginToken;


class JWTCreatedListener{

  private $em;

  public function __construct($entityManager)
    {
        $this->em = $entityManager;
    }



  /**
   * @param JWTCreatedEvent $event
   *
   * @return void
   */

  public function onJWTCreated(JWTCreatedEvent $event)
  {
      $loginToken = $this->em->getRepository(LoginToken::class)->findOneBy([], ["id"=>"DESC"]);
      $expiration = $loginToken->getLimitDate();
      //$expiration->setTime(2, 0, 0);

      $payload        = $event->getData();
      $payload['exp'] = $expiration->getTimestamp();

      $event->setData($payload);
  }

}
