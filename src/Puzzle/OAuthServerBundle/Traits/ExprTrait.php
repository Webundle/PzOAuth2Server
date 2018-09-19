<?php

namespace Puzzle\OAuthServerBundle\Traits;

/**
 * ExprTrait
 * 
 * @author AGNES Gnagne Cedric <cecenho55@gmail.com>
 */
trait ExprTrait
{
    public function stringify($array){
        return is_array($array) ? implode(',', $array) : null;
    }
    
    public function count($childs = null) {
        return count($childs);
    }
}
