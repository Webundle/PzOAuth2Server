<?php

namespace Puzzle\OAuthServerBundle\Entity;

use FOS\OAuthServerBundle\Entity\AuthCode as BaseAuthCode;
use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Table(name="oauth_server_auth_code")
 * @ORM\Entity
 */
class AuthCode extends BaseAuthCode
{
    /**
     * @var string
     *
     * @ORM\Column(name="id", type="string")
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="CUSTOM")
     * @ORM\CustomIdGenerator(class="Puzzle\OAuthServerBundle\Service\IdGenerator")
     */
    protected $id;
    
	/**
	 * @ORM\ManyToOne(targetEntity="Client")
	 * @ORM\JoinColumn(nullable=false)
	 */
	protected $client;
	
	/**
	 * @ORM\ManyToOne(targetEntity="Puzzle\OAuthServerBundle\Entity\User")
	 */
	protected $user;

}
