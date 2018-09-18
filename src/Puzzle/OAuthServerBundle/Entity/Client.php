<?php

namespace Puzzle\OAuthServerBundle\Entity;

use FOS\OAuthServerBundle\Entity\Client as BaseClient;
use Doctrine\ORM\Mapping as ORM;
use Puzzle\OAuthServerBundle\Entity\User;

/**
 * @ORM\Table(name="oauth_server_client")
 * @ORM\Entity
 */
class Client extends BaseClient
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
	 * @ORM\Column(type="string", length=255)
	 */
	private $name;
	
	/**
	 * @ORM\Column(type="string", length=255, unique=true)
	 */
	private $host;
	
	/**
	 * @ORM\Column(name="is_interne", type="boolean")
	 */
	private $isInterne;
	
	/**
      * @ORM\ManyToOne(targetEntity="Puzzle\OAuthServerBundle\Entity\User")
      * @ORM\JoinColumn(name="user_id", referencedColumnName="id")
      */
	protected $user;
	
	public function __construct(){
		$this->users = new \Doctrine\Common\Collections\ArrayCollection();
		parent::__construct();
	}
	
	/**
	 * {@inheritdoc}
	 */
	public function setName($name){
		$this->name = $name;
	}
	
	/**
	 * {@inheritdoc}
	 */
	public function getName(){
		return $this->name;
	}
	
    public function setUser(User $user){
    	$this->user = $user;
        return $this;
    }

    public function getUser(){
        return $this->user;
    }
    
    public function setHost($host){
        $this->host = $host;
        return $this;
    }
    
    public function getHost(){
        return $this->host;
    }
    
    public function setInterne(bool $interne){
        $this->isInterne = $interne;
        return $this;
    }
    
    public function isInterne(){
        return $this->isInterne;
    }
}
