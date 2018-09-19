<?php
namespace Puzzle\OAuthServerBundle\Traits;

use Doctrine\ORM\Mapping as ORM;
use JMS\Serializer\Annotation as JMS;

/**
 * Taggable
 * 
 * @author AGNES Gnagne Cedric <cecenho55@gmail.com>
 */
trait Taggable
{
    /**
     * @ORM\Column(name="tags", type="array", nullable=true)
     * @var array
     * @JMS\Expose
     * @JMS\Type("array")
     */
    private $tags;
    
	public function setTags($tags) : self {
	    $this->tags = $tags;
	    return $this;
	}
	
	public function addTag($tag) : self {
	    $this->tags = array_unique(array_merge($this->tags, [$tag]));
	    return $this;
	}
	
	public function removeTag($tag) : self {
	    $this->tags = array_diff($this->tags, [$tag]);
	    return $this;
	}
	
	public function getTags() {
	    return $this->tags;
	}
}
