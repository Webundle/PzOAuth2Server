<?php

namespace Puzzle\OAuthServerBundle\Service;


/**
 * OAuth2 Grant Type Url Generator
 * 
 * @author AGNES Gnagne Cedric <cecenho55@gmail.com>
 */
class OAuth2GrantUrlGenerator
{
	/**
	 * Generate Login URL
	 * 
	 * @param string $baseUri
	 * @param array $config
	 * @return string
	 */
	public static function generateLoginUrl(string $baseUri, array $config) {
		$loginUrl = $baseUri.'?client_id='.$config['client_id'].'&response_type=code&redirect_uri='.$config['redirect_uri'].'&scope='.$config['scope'].'&state='.$config['state'];
		
		if(array_key_exists('client_redirect_uri', $config)){
			$loginUrl .='&client_redirect_uri='.$config['client_redirect_uri'];
		}
		
		if(array_key_exists('interne', $config)){
		    $loginUrl .='&interne='.$config['interne'];
		}
		
		return $loginUrl;
	}
	
	
	/**
	 * Authorization Code Grant
	 *
	 * @param array $config
	 * @return string
	 */
	public static function generateAuthorizationCodeUrl(string $baseUri, array $config) {
	    $url = $baseUri.'?client_id='.$config['client_id'].'&client_secret='.$config['client_secret'].'&grant_type=authorization_code&redirect_uri='.$config['redirect_uri'].'&code='.$config['code'];
	    
	    if(array_key_exists('interne', $config)){
	        $url .='&interne='.$config['interne'];
	    }
	    
	    if(array_key_exists('client_redirect_uri', $config)){
	        $url .='&client_redirect_uri='.$config['client_redirect_uri'];
	    }
	    
	    if(array_key_exists('service_uri', $config)){
	        $url .='&service_uri='.$config['service_uri'];
	    }
	    
	    if(array_key_exists('user_uri', $config)){
	        $url .='&user_uri='.$config['user_uri'];
	    }
	    
	    return $url;
	}
	
	/**
	 * Implicit Grant
	 *
	 * @param array $config
	 * @return string
	 */
	public function generateImplicitUrl(string $baseUri, array $config) {
	    $url = $this->baseAuthUri.'?client_id='.$config['client_id'].'&response_type=token&redirect_uri='.$config['redirect_uri'].'&state='.$config['state'];
	   
	   if(array_key_exists('interne', $config)){
	       $url .='&interne='.$config['interne'];
	   }
	   
	   return $url;
	}
	
	
	/**
	 * Password Grant
	 *
	 * @param array $config
	 * @return string
	 */
	public static function generatePasswordUrl(string $baseUri, array $config) {
	    $url = $this->baseTokenUri.'?client_id='.$config['client_id'].'&client_secret='.$config['client_secret'].'&grant_type=password&username='.$config['username'].'&password='.$config['password'].'&redirect_uri='.$config['redirect_uri'];
	   
	   if(array_key_exists('interne', $config)){
	       $url .='&interne='.$config['interne'];
	   }
	   
	   return $url;
	}
	
	
	/**
	 * Credentials Grant
	 *
	 * @param array $config
	 * @return string
	 */
	public static function getClientCrendentialsUrl(string $baseUri, array $config) {
	    $url = $baseUri.'?client_id='.$config['client_id'].'&client_secret='.$config['client_secret'].'&grant_type=client_credentials&scope='.$config['scope'];
	   
	   if(array_key_exists('interne', $config)){
	       $url .='&interne='.$config['interne'];
	   }
	   
	   return $url;
	}
	
	
	/**
	 * Refresk Token
	 *
	 * @return string
	 */
	public static function generateRefreshTokenUrl(string $baseUri, array $config) {
	    $url = $baseUri.'?client_id='.$config['client_id'].'&client_secret='.$config['client_secret'].'&grant_type=refresh_token&refresh_token='.$config['refresh_token'];
	    
	    if(array_key_exists('interne', $config)){
	        $url .='&interne='.$config['interne'];
	    }
	    
	    return $url;
	}
	
}