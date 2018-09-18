<?php

namespace Puzzle\OAuthServerBundle\EventListener;

use FOS\OAuthServerBundle\Event\OAuthEvent;
use Puzzle\OAuthServerBundle\Entity\User;
use Doctrine\ORM\EntityManager;
use Puzzle\OAuthServerBundle\Entity\AuthCode;
use Doctrine\ORM\EntityManagerInterface;

/**
 * Oauth Server Event Listener
 * 
 * @author AGNES Gnagne Cedric <cecenho55@gmail.com>
 */
class OAuthEventListener
{	
	/**
	 * @var EntityManager $em
	 */
	protected $em;
	
	public function __construct(EntityManagerInterface $em){
		$this->em = $em;
	}
	
	/**
	 * Pre-auhorization process
	 * 
	 * @param OAuthEvent $event
	 */
    public function onPreAuthorizationProcess(OAuthEvent $event)
    {
        if ($user = $this->getUser($event)) {
				$authCode = $this->em
				                 ->getRepository(AuthCode::class)
        			             ->findOneBy(array('user' => $event->getUser()->getId(), 'client' => $event->getClient()->getId()));
				
				if($authCode){
					$event->setAuthorizedClient(true);
				}else{
					$event->setAuthorizedClient(false);
				}
				
//             $event->setAuthorizedClient(
//                 $user->isAuthorizedClient($event->getClient())
//             );
        }
    }

    /**
     * Post-authorization process
     * 
     * @param OAuthEvent $event
     */
    public function onPostAuthorizationProcess(OAuthEvent $event)
    {
        if ($event->isAuthorizedClient()) {
            if (! $event->getClient()) {
                $user = $this->getUser($event);
                $user->addClient($event->getClient());
                
				$this->em->flush();
            }
        }
    }

    /**
     * Get User
     * 
     * @param OAuthEvent $event
     * @return object|NULL
     */
    protected function getUser(OAuthEvent $event){
        return $this->em->getRepository(User::class)->findOneBy(array('username' => $event->getUser()->getUsername()));
    }
}