<?php

namespace Puzzle\OAuthServerBundle\Entity;

use FOS\OAuthServerBundle\Entity\Client as BaseClient;
use Doctrine\ORM\Mapping as ORM;
use Puzzle\OAuthServerBundle\Entity\User;
use Knp\DoctrineBehaviors\Model\Timestampable\Timestampable;
use Knp\DoctrineBehaviors\Model\Blameable\Blameable;

/**
 * @ORM\Table(name="oauth_server_client")
 * @ORM\Entity
 */
class Client extends BaseClient
{
    use Timestampable,
        Blameable;
    
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
	
	public function __construct() {
		parent::__construct();
	}
	
	/**
	 * {@inheritdoc}
	 */
	public function setName($name) : self {
		$this->name = $name;
		return $this;
	}
	
	/**
	 * {@inheritdoc}
	 */
	public function getName() :? string {
		return $this->name;
	}
	
    public function setHost($host) : self {
        $this->host = $host;
        return $this;
    }
    
    public function getHost() :? string {
        return $this->host;
    }
    
    public function setInterne(bool $interne) : self {
        $this->isInterne = $interne;
        return $this;
    }
    
    public function isInterne() :? bool {
        return $this->isInterne;
    }
}
