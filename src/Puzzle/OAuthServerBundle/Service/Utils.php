<?php

namespace Puzzle\OAuthServerBundle\Service;

use Symfony\Component\HttpFoundation\ParameterBag;
use Puzzle\OAuthServerBundle\Entity\User;

/**
 * 
 * @author AGNES Gnagne Cedric <cecenho55@gmail.com>
 *
 */
class Utils
{
	/**
	 * sha512 encoder
	 * 
	 * @param string $pass
	 * @param string $salt
	 * @return string
	 */
	public static function sha512(string $pass, string $salt)
	{
		$iterations = 5000; // Par défaut
		$salted = $pass.'{'.$salt.'}';
		
		$digest = hash('sha512', $salted, true);
		for ($i = 1; $i < $iterations; $i++) {
			$digest = hash('sha512', $digest.$salted, true);
		}
		
		return base64_encode($digest);
	}
	
	public static function blameRequestQuery(ParameterBag $query, User $user) {
	    $filter = $query->get('filter');
	    $filter = $filter ? $filter.',createdBy=='.$user->getId() : 'createdBy=='.$user->getId();
	    $query->set('filter', $filter);
	    
	    return $query;
	}
	
	public static function setter($object, $fields, $data) {
	    foreach ($fields as $field) {
	        if (isset($data[$field]) && $data[$field]) {
	            $object->{'set'.ucwords($field)}($data[$field]);
	        }
	    }
	    
	    return $object;
	}
}