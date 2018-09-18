<?php

namespace Puzzle\OAuthServerBundle\Entity;

use FOS\OAuthServerBundle\Entity\RefreshToken as BaseRefreshToken;
use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Table(name="oauth_server_refresh_token")
 * @ORM\Entity
 */
class RefreshToken extends BaseRefreshToken
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
