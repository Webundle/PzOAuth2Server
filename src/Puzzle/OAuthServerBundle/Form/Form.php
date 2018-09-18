<?php

namespace Puzzle\OAuthServerBundle\Form;

use Symfony\Component\Form\Form as BaseForm;
use Symfony\Component\Form\FormView;

class Form extends BaseForm {
    
    public function __construct(FormConfig $config) {
        parent::__construct($config);
    }
    
    public function createView(FormView $parent = null){
        return $this->getConfig()->getForm()->createView();
    }
}