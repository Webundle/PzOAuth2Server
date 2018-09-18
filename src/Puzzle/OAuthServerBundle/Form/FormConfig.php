<?php

namespace Puzzle\OAuthServerBundle\Form;

use Symfony\Component\EventDispatcher\EventDispatcherInterface;
use Symfony\Component\Form\FormBuilder;
use Symfony\Component\Form\FormFactoryInterface;
use Symfony\Component\Form\Extension\HttpFoundation\HttpFoundationRequestHandler;

class FormConfig extends FormBuilder {
    protected $builder;
    public function __construct(string $name, $type, $dataClass, EventDispatcherInterface $dispatcher, FormFactoryInterface $factory, array $options = array()){
        parent::__construct($name, $dataClass, $dispatcher, $factory, $options);
        $this->builder = self::create($name, $type);
        $this->setRequestHandler(new HttpFoundationRequestHandler());
    }
    
    public function getForm()
    {
        return $this->builder->getForm();
    }
}