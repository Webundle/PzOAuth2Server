<?php
namespace Puzzle\OAuthServerBundle\Traits;

use Doctrine\ORM\Mapping as ORM;
use JMS\Serializer\Annotation as JMS;

/**
 * Pictureable
 *
 * @author AGNES Gnagne Cedric <cecenho55@gmail@com>
 */
trait Pictureable
{
    /**
    * @ORM\Column(type="string", length=255, nullable=true)
    * @JMS\Expose
	* @JMS\Type("string")
    */
    private $picture;
    
    public function setPicture($picture) {
        $this->picture = $picture;
        return $this;
    }
    
    public function getPicture() {
        return $this->picture;
    }
}
