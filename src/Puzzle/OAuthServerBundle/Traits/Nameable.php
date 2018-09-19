<?php
namespace Puzzle\OAuthServerBundle\Traits;

use Doctrine\ORM\Mapping as ORM;
use JMS\Serializer\Annotation as JMS;

/**
 * Nameable
 * 
 * @author AGNES Gnagne Cedric <cecenho55@gmail.com>
 */
trait Nameable
{
    /**
	 * @var string
	 * @ORM\Column(name="name", type="string", length=255)
	 * @JMS\Expose
	 * @JMS\Type("string")
	 */
	private $name;
    
    public function setName($name) : self {
        $this->name = $name;
        return $this;
    }
    
    public function getName() : string {
        return $this->name;
    }
}
