<?php 

namespace Puzzle\OAuthServerBundle\EventListener;

use Doctrine\ORM\EntityManagerInterface;
use Puzzle\OAuthServerBundle\Event\UserEvent;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;

/**
 * @author AGNES Gnagne Cedric <cecenho5@gmail.com>
 */
class UserListener
{
	/**
	 * @var EntityManagerInterface $em
	 */
	private $em;
	
	/**
	 * @var \Swift_Mailer $mailer
	 */
	private $mailer;
	
	/**
	 * @var \Twig_Environment $twig
	 */
	private $twig;
	
	/**
	 * @var UrlGeneratorInterface
	 */
	private $router;
	
	/**
	 * @var string $fromEmail
	 */
	private $fromEmail;
	
	/**
	 * @param EntityManagerInterface $em
	 * @param UrlGeneratorInterface $router
	 * @param \Swift_Mailer $mailer
	 * @param string $fromEmail
	 */
	public function __construct(EntityManagerInterface $em, UrlGeneratorInterface $router, \Swift_Mailer $mailer, \Twig_Environment $twig, string $fromEmail){
		$this->em = $em;
		$this->mailer = $mailer;
		$this->router = $router;
		$this->twig = $twig;
		$this->fromEmail = $fromEmail;
	}
	
	/**
	 * Create user
	 * @param UserEvent $event
	 */
	public function onCreate(UserEvent $event) {
		$user = $event->getUser();
		
		// For test
		$template = null;
		if ($template !== null) {
		    // Notification by email
		    $this->mailer->send(array(
		        'subject' => $template->getName(),
		        'to' => $user->getEmail(),
		        'from' => $this->fromEmail,
		        'body' => $this->twig->render($template->getDocument(), [
		            'user' => $user,
		            'confirmationUrl' => $this->router->generate('app_user_confirm', ['token' => $user->getConfirmationToken()])
		        ])
		    ));
		}
		
		return;
	}
	
	/**
	 * Update password
	 * @param UserEvent $event
	 */
	public function onUpdatePassword(UserEvent $event) {
	    $user = $event->getUser();
	    $user->setPassword(hash('sha512', $user->getPlainPassword()));
	    
	    $this->em->flush($user);
	    return;
	}
	
}

?>
