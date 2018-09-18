<?php

namespace Puzzle\OAuthServerBundle\Controller;

use Symfony\Component\HttpFoundation\Request;
use OAuth2\OAuth2ServerException;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Puzzle\OAuthServerBundle\Service\OAuth2;

/**
 * OAuth Server Token
 * 
 * @author AGNES Gnagne Cedric <cecenho55@gmail.com>
 */
class TokenController extends Controller
{
    /**
     * @var OAuth2
     */
    protected $server;

    /**
     * @param OAuth2 $server
     */
    public function __construct(OAuth2 $server){
        $this->server = $server;
    }

    /**
     * @param Request $request
     *
     * @return Response
     */
    public function tokenAction(Request $request)
    {
        try {
            return $this->server->grantAccessToken($request);
        } catch (OAuth2ServerException $e) {
            return $e->getHttpResponse();
        }
    }
}
