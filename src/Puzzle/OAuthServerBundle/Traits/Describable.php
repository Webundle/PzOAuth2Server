<?php
namespace Puzzle\OAuthServerBundle\Traits;

use Doctrine\ORM\Mapping as ORM;
use JMS\Serializer\Annotation as JMS;

/**
 * Describable
 * 
 * @author AGNES Gnagne Cedric <cecenho55@gmail.com>
 */
trait Describable
{
    /**
     * @var string
     * @ORM\Column(name="description", type="text", nullable=true)
     * @JMS\Expose
	 * @JMS\Type("string")
     */
    private $description;
    
    public function setDescription($description) {
        $this->description = $description;
        return $this;
    }
    
    public function getDescription() {
        return $this->description;
    }
}
