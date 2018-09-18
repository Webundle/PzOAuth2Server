<?php

namespace Puzzle\OAuthServerBundle\Controller;

use FOS\RestBundle\Controller\FOSRestController;
use Puzzle\OAuthServerBundle\Service\Repository;
use Symfony\Bridge\Doctrine\RegistryInterface;
use Symfony\Component\HttpFoundation\ParameterBag;
use JMS\Serializer\SerializerInterface;
use Puzzle\OAuthServerBundle\Service\ErrorFactory;
use Symfony\Component\EventDispatcher\EventDispatcherInterface;

class BaseFOSRestController extends FOSRestController
{
    /**
     * @var RegistryInterface $doctrine
     */
    protected $doctrine;
    
    /**
     * @var Repository $repositry
     */
    protected $repository;
    
    /**
     * @var SerializerInterface $serializer
     */
    protected $serializer;
    
    /**
     * @var EventDispatcherInterface $dispatcher
     */
    protected $dispatcher;
    
    /**
     * @var ErrorFactory $errorFactory
     */
    protected $errorFactory;
    
    /**
     * Filters simple
     *
     * @var array
     */
    protected $filters;
    
    /**
     * Filters used in LIKE Clause
     *
     * @var array
     */
    protected $filtersLIKE;
    
    /**
     * Filters used in IN Clause
     *
     * @var array
     */
    protected $filtersIN;
    
    /**
     * Filters used in MEMEBER OF Clause (special Doctrine Clause)
     *
     * @var array
     */
    protected $filtersMEMBEROF;
    
    /**
     * @var string
     */
    protected $entityName;
    
    /**
     * @var string
     */
    protected $connection;
    
    /**
     * @var ParameterBag
     */
    protected $query;
    
    /**
     * @var array
     */
    protected $fields;
    
    /**
     * @param RegistryInterface         $doctrine
     * @param Repository                $repository
     * @param SerializerInterface       $serializer
     * @param EventDispatcherInterface  $dispatcher
     * @param ErrorFactory              $errorFactory
     */
    public function __construct(
        RegistryInterface $doctrine, 
        Repository $repository, 
        SerializerInterface $serializer,
        EventDispatcherInterface $dispatcher,
        ErrorFactory $errorFactory
    ){
        $this->doctrine = $doctrine;
        $this->repository = $repository;
        $this->serializer = $serializer;
        $this->dispatcher = $dispatcher;
        $this->errorFactory = $errorFactory;
        $this->connection = 'default';
    }
}
