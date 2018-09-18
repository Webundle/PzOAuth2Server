<?php

namespace Puzzle\OAuthServerBundle\Service;

use OAuth2\OAuth2 as BaseOAuth2;
use OAuth2\OAuth2ServerException;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use OAuth2\IOAuth2Storage;

class OAuth2 extends BaseOAuth2
{
    public function __construct(IOAuth2Storage $storage, array $config) {
        parent::__construct($storage, $config);
    }
    
    /**
     * Custom grant access token
     * 
     * {@inheritDoc}
     * @see \OAuth2\OAuth2::grantAccessToken()
     */
    public function grantAccessToken(Request $request = null)
    {
        $filters = array(
            "grant_type" => array(
                "filter" => FILTER_VALIDATE_REGEXP,
                "options" => array("regexp" => self::GRANT_TYPE_REGEXP),
                "flags" => FILTER_REQUIRE_SCALAR
            ),
            "scope" => array("flags" => FILTER_REQUIRE_SCALAR),
            "code" => array("flags" => FILTER_REQUIRE_SCALAR),
            "redirect_uri" => array("filter" => FILTER_SANITIZE_URL),
            "username" => array("flags" => FILTER_REQUIRE_SCALAR),
            "password" => array("flags" => FILTER_REQUIRE_SCALAR),
            "refresh_token" => array("flags" => FILTER_REQUIRE_SCALAR),
        );
        
        if ($request === null) {
            $request = Request::createFromGlobals();
        }
        
        // Input data by default can be either POST or GET
        if ($request->getMethod() === 'POST') {
            $inputData = $request->request->all();
        } else {
            $inputData = $request->query->all();
        }
        
        // Basic authorization header
        $authHeaders = $this->getAuthorizationHeader($request);
        
        // Filter input data
        $input = filter_var_array($inputData, $filters);
        
        // Grant Type must be specified.
        if (!$input["grant_type"]) {
            throw new OAuth2ServerException(Response::HTTP_BAD_REQUEST, self::ERROR_INVALID_REQUEST, 'Invalid grant_type parameter or parameter missing');
        }
        
        // Authorize the client
        $clientCredentials = $this->getClientCredentials($inputData, $authHeaders);
        
        $client = $this->storage->getClient($clientCredentials[0]);
        
        if (!$client) {
            throw new OAuth2ServerException(Response::HTTP_BAD_REQUEST, self::ERROR_INVALID_CLIENT, 'The client credentials are invalid');
        }
        
        if ($this->storage->checkClientCredentials($client, $clientCredentials[1]) === false) {
            throw new OAuth2ServerException(Response::HTTP_BAD_REQUEST, self::ERROR_INVALID_CLIENT, 'The client credentials are invalid');
        }
        
        if (!$this->storage->checkRestrictedGrantType($client, $input["grant_type"])) {
            throw new OAuth2ServerException(Response::HTTP_BAD_REQUEST, self::ERROR_UNAUTHORIZED_CLIENT, 'The grant type is unauthorized for this client_id');
        }
        
        // Do the granting
        switch ($input["grant_type"]) {
            case self::GRANT_TYPE_AUTH_CODE:
                // returns array('data' => data, 'scope' => scope)
                $stored = $this->grantAccessTokenAuthCode($client, $input);
                break;
            case self::GRANT_TYPE_USER_CREDENTIALS:
                // returns: true || array('scope' => scope)
                $stored = $this->grantAccessTokenUserCredentials($client, $input);
                break;
            case self::GRANT_TYPE_CLIENT_CREDENTIALS:
                // returns: true || array('scope' => scope)
                $stored = $this->grantAccessTokenClientCredentials($client, $input, $clientCredentials);
                break;
            case self::GRANT_TYPE_REFRESH_TOKEN:
                // returns array('data' => data, 'scope' => scope)
                $stored = $this->grantAccessTokenRefreshToken($client, $input);
                break;
            default:
                if (substr($input["grant_type"], 0, 4) !== 'urn:'
                    && !filter_var($input["grant_type"], FILTER_VALIDATE_URL)
                    ) {
                        throw new OAuth2ServerException(
                            Response::HTTP_BAD_REQUEST,
                            self::ERROR_INVALID_REQUEST,
                            'Invalid grant_type parameter or parameter missing'
                            );
                    }
                    
                    // returns: true || array('scope' => scope)
                    $stored = $this->grantAccessTokenExtension($client, $inputData, $authHeaders);
        }
        
        if (!is_array($stored)) {
            $stored = array();
        }
        
        // if no scope provided to check against $input['scope'] then application defaults are set
        // if no data is provided than null is set
        $stored += array('scope' => $this->getVariable(self::CONFIG_SUPPORTED_SCOPES, null), 'data' => null,
            'access_token_lifetime' => $this->getVariable(self::CONFIG_ACCESS_LIFETIME),
            'issue_refresh_token' => true, 'refresh_token_lifetime' => $this->getVariable(self::CONFIG_REFRESH_LIFETIME));
        
        $scope = $stored['scope'];
        if ($input["scope"]) {
            // Check scope, if provided
            if (!isset($stored["scope"]) || !$this->checkScope($input["scope"], $stored["scope"])) {
                throw new OAuth2ServerException(Response::HTTP_BAD_REQUEST, self::ERROR_INVALID_SCOPE, 'An unsupported scope was requested.');
            }
            $scope = $input["scope"];
        }
        
        $token = $this->createAccessToken($client, $stored['data'], $scope, $stored['access_token_lifetime'], $stored['issue_refresh_token'], $stored['refresh_token_lifetime']);
        
        // Store Access Token in appropriate app user
//         $formeClient = $this->container->get('forme_oauth_server.oauth.curl');
//         $userUri = $request->query->get('user_uri');
//         $serviceUri = $request->query->get('service_uri');
        
//         if($userUri){
//             // Update APIs client user's access and refresh token
//             $data = [
//                 'access_token_up' => $token['access_token'],
//                 'refresh_token_up' => $token['refresh_token'],
//                 'services_to_add' => $serviceUri
//             ];
//             $formeClient->putResources($userUri, 'Authorization: Bearer '. $token['access_token'], $data);
//             $user = $formeClient->getResources($userUri, 'Authorization: Bearer '. $token['access_token']);
//             $formeClient->putResources($serviceUri, 'Authorization: Bearer '. $token['access_token'], ['api_id' => $user['id']]);
//         }
        
        return new Response(json_encode($token), 200, $this->getJsonHeaders());
    }
    
    /**
     * Returns HTTP headers for JSON.
     *
     * @see     http://tools.ietf.org/html/draft-ietf-oauth-v2-20#section-5.1
     * @see     http://tools.ietf.org/html/draft-ietf-oauth-v2-20#section-5.2
     *
     * @return array
     *
     * @ingroup oauth2_section_5
     */
    private function getJsonHeaders()
    {
        $headers = $this->getVariable(self::CONFIG_RESPONSE_EXTRA_HEADERS, array());
        $headers += array(
            'Content-Type' => 'application/json',
            'Cache-Control' => 'no-store',
            'Pragma' => 'no-cache',
        );
        return $headers;
    }
}
