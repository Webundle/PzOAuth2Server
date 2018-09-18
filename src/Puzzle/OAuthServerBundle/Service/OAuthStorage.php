<?php

namespace Puzzle\OAuthServerBundle\Service;

use FOS\OAuthServerBundle\Model\AccessTokenManagerInterface;
use FOS\OAuthServerBundle\Model\AuthCodeManagerInterface;
use FOS\OAuthServerBundle\Model\ClientManagerInterface;
use FOS\OAuthServerBundle\Model\RefreshTokenManagerInterface;
use FOS\OAuthServerBundle\Storage\OAuthStorage as BaseOAuthStorage;
use Symfony\Component\Security\Core\Encoder\EncoderFactoryInterface;
use Symfony\Component\Security\Core\User\UserProviderInterface;

class OAuthStorage extends BaseOAuthStorage
{
    public function __construct(ClientManagerInterface $clientManager, AccessTokenManagerInterface $accessTokenManager,
        RefreshTokenManagerInterface $refreshTokenManager, AuthCodeManagerInterface $authCodeManager,
        UserProviderInterface $userProvider = null, EncoderFactoryInterface $encoderFactory = null)
    {
        parent::__construct($clientManager, $accessTokenManager, $refreshTokenManager, $authCodeManager, $userProvider, $encoderFactory);
    }

}
