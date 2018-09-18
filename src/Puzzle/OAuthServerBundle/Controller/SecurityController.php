<?php
namespace Puzzle\OAuthServerBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Security\Core\Authentication\Token\UsernamePasswordToken;
use Symfony\Component\HttpFoundation\Response;
use Puzzle\OAuthServerBundle\Entity\Client;
use Puzzle\OAuthServerBundle\Entity\User;
use Puzzle\OAuthServerBundle\UserEvents;
use Puzzle\OAuthServerBundle\Form\Type\UserType;
use Puzzle\OAuthServerBundle\Event\UserEvent;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Puzzle\OAuthServerBundle\Service\Utils;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;
use Puzzle\OAuthServerBundle\Service\OAuth2GrantUrlGenerator;
use GuzzleHttp\Client as ClientHttp;
use Symfony\Component\EventDispatcher\EventDispatcher;
use Symfony\Component\Security\Guard\GuardAuthenticatorHandler;
use Symfony\Component\Security\Core\Exception\AuthenticationException;
use Symfony\Component\Security\Core\Security;
use Symfony\Component\Security\Http\Authentication\AuthenticationUtils;
use Doctrine\DBAL\Exception\UniqueConstraintViolationException;

/**
 * Security 
 * 
 * @author AGNES Gnagne Cedric <cecenho55@gmail.com>
 */
class SecurityController extends Controller
{
	/**
	 * Main Login
	 * 
	 * @param Request $request
	 * @return \Symfony\Component\HttpFoundation\Response
	 */
	public function loginAction(Request $request){
	    
	    /** @var AuthenticationUtils $authenticationUtils */
	    $authenticationUtils = $this->get('security.authentication_utils');
	    
	    $error = $authenticationUtils->getLastAuthenticationError();
	    $lastUsername = $authenticationUtils->getLastUsername();
	    
	    $csrfToken = $this->has('form.csrf_provider')
	    ? $this->get('form.csrf_provider')->generateCsrfToken('authenticate')
	    : null;
	    
	    return $this->render("PuzzleOAuthServerBundle:Security:login.html.twig", array(
	        'last_username'  => $lastUsername,
	        'error'          => $error,
	        'csrf_token'     => $csrfToken
	    ));
	}
	
	
	/**
	 * OAuth Register
	 * 
	 * @param Request $request
	 * @return Response | RedirectResponse
	 */
	public function registerAction(Request $request) {
	    $user = new User();
	    $form = $this->createForm(UserType::class, $user, [
	        'method' => 'POST',
	        'action' => $this->generateUrl('register')
	    ]);
	    $form->handleRequest($request);
	    if ($form->isSubmitted() === true && $form->isValid() === true) {
	        try {
	            $user->setRoles([User::ROLE_DEFAULT]);
	            $em = $this->getDoctrine()->getManager();
	            $em->persist($user);
	            $em->flush();
	            /** @var EventDispatcher */
	            $this->get('event_dispatcher')->dispatch(UserEvents::USER_PASSWORD, new UserEvent($user, [
	                'plainPassword' => $user->getPlainPassword()
	            ]));
	            
	            // Send Confirmation Email
	            // Connect on Main Security Context
	            $token = new UsernamePasswordToken($user, $user->getPassword(), 'main', [User::ROLE_DEFAULT]);
	            /** @var GuardAuthenticatorHandler $authenticationGuardHandler */
	            $authenticationGuardHandler = $this->get('security.authentication.guard_handler');
	            $authenticationGuardHandler->authenticateWithToken($token, $request);
	            
	            return $this->redirect($_SESSION["_sf2_attributes"]["_security.main.target_path"]);
	            
	        } catch (UniqueConstraintViolationException $e) {
	            $this->addFlash('error', 'message.dupliacted_account');
	            return $this->redirectToRoute('register');
	        }
	    }
	    
	    return $this->render("PuzzleOAuthServerBundle:Security:register.html.twig", ['form' => $form->createView()]);
	}
	
	
	/**
	 * OAuth Login URL Generator
	 * Redirect to pos_oauth_login
	 *
	 * @param Request $request
	 * @return Response | RedirectResponse
	 */
	public function oauthLoginUrlGeneratorAction(Request $request){
	    if(session_status() != PHP_SESSION_ACTIVE){
	        session_start();
	    }
	    
	    $_SESSION['state'] = md5(uniqid(mt_rand(), true));
	    
	    $redirectUri = $request->get('redirect_uri');
	    $clientHost = Utils::extractHostFromUrl($redirectUri);
	    $client = $this->getDoctrine()->getRepository(Client::class)->findOneBy(['host' => $clientHost]);
	    
	    if ($client === null){
	        return $this->redirect($this->generateUrl('pos_client_not_found', ['service' => $clientHost]));
	    }
	    
	    $baseUri = $this->generateUrl('oauth_authorize', [], UrlGeneratorInterface::ABSOLUTE_URL);
	    $config = [
	        'scope' => $this->getParameter('pos.default_scope'),
	        'state' => $_SESSION['state'],
	        'client_id' => $client->getId().'_'.$client->getRandomId(),
	        'redirect_uri' => $redirectUri
	    ];
	    
	    return $this->redirect(OAuth2GrantUrlGenerator::generateLoginUrl($baseUri, $config));
	}
	
	/**
	 * OAuth Login
	 * @return \Symfony\Component\HttpFoundation\Response
	 */
	public function oauthLoginAction(){
	    /** @var AuthenticationUtils $authenticationUtils */
	    $authenticationUtils = $this->get('security.authentication_utils');
	    
	    $error = $authenticationUtils->getLastAuthenticationError();
	    $lastUsername = $authenticationUtils->getLastUsername();
	    
	    $csrfToken = $this->has('form.csrf_provider')
	    ? $this->get('form.csrf_provider')->generateCsrfToken('authenticate')
	    : null;
	    
	    return $this->render("PuzzleOAuthServerBundle:Security:oauth_login.html.twig", array(
	        'last_username'  => $lastUsername,
	        'error'          => $error,
	        'csrf_token'     => $csrfToken
	    ));
	}
	
	/**
	 * OAuth Logout
	 */
	public function oauthLogoutAction(Request $request){
	    $this->get('security.token_storage')->setToken(null);
	    $this->container->get('session')->invalidate();
	    
	    return $this->redirect($request->get('redirect_uri'));
	}
	
	/**
	 * OAuth Connect Client
	 * 
	 * @param Request $request
	 * @return \Symfony\Component\HttpFoundation\RedirectResponse
	 */
	public function oauthConnectAction(Request $request)
	{
	    if ($code = $request->query->get('code')){
	        $service = $request->query->get('service');
	        
	        $redirectUri = $request->get('redirect_uri');
	        $clientHost = Utils::extractHostFromUrl($redirectUri);
	        $client = $this->getDoctrine()->getRepository(Client::class)->findOneBy(['host' => $clientHost]);
	        
	        if ($client){
	            $baseUri = $this->generateUrl('oauth_authorize', [], UrlGeneratorInterface::ABSOLUTE_URL);
	            $config = [
	                'code' => $code,
	                'client_id' => $client->getId().'_'.$client->getRandomId(),
	                'client_secret' => $client->getSecret(),
	                'redirect_uri' => $redirectUri,
	                'scope' => $this->getParameter('pos.default_scope'),
	                'interne' => $client->isInterne(),
	                'service_uri' => $this->getParameter('host_apis').'/v1/'.$service.'/users/me',
	                'user_uri' => $this->getParameter('host_apis').'/v1/account/me'
	            ];
	            
	            $url = OAuth2GrantUrlGenerator::generateAuthorizationCodeUrl($baseUri, $config);
	            $clientHttp = new ClientHttp();
	            $response = $clientHttp->get($url, [
	                'Accept' => 'application/json',
	                'Content-type’ => ‘application/json'
	            ]);
	            $array = json_decode($response->getBody()->getContents(), true);
	            
	            if (array_key_exists('access_token', $array)){
	                $response = $clientHttp->get('/v1/account/me', [
	                    'Accept' => 'application/json',
	                    'Content-type’ => ‘application/json',
	                    'headers' => ['Authorization: Bearer '.$array['access_token']]
	                ]);
	                $apiUser = json_decode($response->getBody()->getContents(), true);
	                
	                $em = $this->getDoctrine()->getManager();
	                $user = $em->getRepository(User::class)->findOneBy(array('email' => $apiUser['email']));
	                
	                return $this->redirect($redirectUri);
	            }
	        }
	        
	        // Client Not Found Error
	        return $this->redirect($this->generateUrl('pos_client_not_found', ['service' => $service]));
	    }
	    
	    return $this->redirectToRoute('login', array('service' => $service));
	}
}