<?php
namespace Puzzle\OAuthServerBundle\Traits;

use Doctrine\ORM\Mapping as ORM;
use JMS\Serializer\Annotation as JMS;

/**
 * PrimaryKeyable
 *
 * @author AGNES Gnagne Cedric <cecenho55@gmail@com>
 */
trait PrimaryKeyable
{
      /**
      * @var string
      *
      * @ORM\Column(name="id", type="string")
      * @ORM\Id
      * @ORM\GeneratedValue(strategy="CUSTOM")
      * @ORM\CustomIdGenerator(class="Puzzle\OAuthServerBundle\Service\IdGenerator")
      * @JMS\Expose
  	  * @JMS\Type("string")
  	  * @JMS\XmlAttribute
      */
     protected $id;
    
    public function getId() {
       return $this->id;
    }
    
    public function clearId() {
        $this->id = null;
        return $this;
    }
}
