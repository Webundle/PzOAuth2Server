<?php

namespace Puzzle\OAuthServerBundle\Form\Handler;

use FOS\OAuthServerBundle\Form\Handler\AuthorizeFormHandler as BaseAuthorizeFormHandler;
use Puzzle\OAuthServerBundle\Form\Form;
use Symfony\Component\HttpFoundation\RequestStack;

class AuthorizeFormHandler extends BaseAuthorizeFormHandler {
    /**
     * @var RequestStack
     */
    private $requestStack;
    
    public function __construct(Form $form, RequestStack $requestStack) {
        $this->requestStack = $requestStack;
        parent::__construct($form, $requestStack);
    }
    
    private function getCurrentRequest() {
        return $this->requestStack->getCurrentRequest();
    }
    
    /**
     * {@inheritDoc}
     * @see \FOS\OAuthServerBundle\Form\Handler\AuthorizeFormHandler::isAccepted()
     */
    public function isAccepted(){
        return  $this->getCurrentRequest()->get('accepted') !== null;
    }
    
    /**
     * {@inheritDoc}
     * @see \FOS\OAuthServerBundle\Form\Handler\AuthorizeFormHandler::isRejected()
     */
    public function isRejected(){
        return !self::isAccepted();
    }
    
    /**
     * {@inheritDoc}
     * @see \FOS\OAuthServerBundle\Form\Handler\AuthorizeFormHandler::getScope()
     */
    public function getScope(){
        return is_array($this->form->getData()) ? !$this->form->getData()['scope'] : parent::getScope();
    }
    
    /**
     * {@inheritDoc}
     * @see \FOS\OAuthServerBundle\Form\Handler\AuthorizeFormHandler::onSuccess()
     */
    protected function onSuccess()
    {
        $data = $this->form->getData();
        if (is_array($data)){
            $_GET = [
                'client_id' => $data['client_id'],
                'response_type' => $data['response_type'],
                'redirect_uri' => $data['redirect_uri'],
                'state' => $data['state'],
                'scope' => $data['scope'],
            ];
        }else {
            parent::onSuccess();
        }
    }
}