<?php
namespace Puzzle\OAuthServerBundle\Twig;

use Puzzle\OAuthServerBundle\Entity\Client;
use Doctrine\ORM\EntityManagerInterface;

/**
 *
 * @author AGNES Gnagne Cedric <cecenho55@gmail.com>
 *
 */
class PuzzleOAuthServerExtension extends \Twig_Extension
{
    /**
     * @var EntityManagerInterface $em
     */
    protected $em;
    
    /**
     * @var \Twig_Environment $twig
     */
    protected $twig;
    
    /**
     * @var array $roles
     */
    protected $roles;
    
    public function __construct(EntityManagerInterface $em, \Twig_Environment $twig, array $roles) {
        $this->em = $em;
        $this->twig = $twig;
        $this->roles = $roles;
    }
    
    public function getFunctions() {
        return [
            new \Twig_SimpleFunction('authorization_builder_content', [$this, 'renderAuthorizationContent'], ['needs_environment' => false, 'is_safe' => ['html']]),
        ];
    }
    
    public function renderAuthorizationContent(Client $client, string $scope) {
        $role = $this->roles[$scope] ?? null ;
        
        return $this->twig->render('PuzzleOAuthServerBundle:Authorize:authorize_scope.html.twig', [
            'client'    => $client,
            'scope'     => $scope,
            'role'      => $role,
        ]);
    }
}
