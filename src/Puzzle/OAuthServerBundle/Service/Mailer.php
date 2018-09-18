<?php

namespace Puzzle\OAuthServerBundle\Service;

/**
 * Mailer
 * 
 * @author AGNES Gnagne Cedric <cecenho55@gmail.com>
 */
class Mailer
{
	/**
	 * @var \Swift_Mailer
	 */
	protected $mailer;
	
	/**
	 * @var string
	 */
	protected $defaultFromMail;
	
	public function __construct(\Swift_Mailer $mailer){
	    $this->mailer = $mailer;
	}
	
    public function send($params, $fromMail = null) {
        $fromMail = $fromMail ?? $this->defaultFromMail;
    	$message = \Swift_Message::newInstance()
    			->setSubject($params['subject'])
    			->setFrom($fromMail)
    			->setTo($params['to'])
    			->setBody($params['body'])
    			->setContentType("text/html");
   
    	if(array_key_exists('attachments', $params && $params['attachments'])){
    		foreach ($params['attachments'] as $attachment){
    			$message->attach(\Swift_Attachment::fromPath($attachment), "application/octet-stream");
    		}
    	}
    	
    	if ($this->mailer->send($message, $failures)) {
    		return true;
    	}
    	
    	return false;
    }
}
