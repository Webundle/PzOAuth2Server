<?php

namespace Puzzle\OAuthServerBundle\Entity;

use FOS\OAuthServerBundle\Entity\AccessToken as BaseAccessToken;
use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Table(name="oauth_server_access_token")
 * @ORM\Entity
 * @ORM\Entity(repositoryClass="Puzzle\OAuthServerBundle\Repository\AccessTokenRepository")
 */
class AccessToken extends BaseAccessToken
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
