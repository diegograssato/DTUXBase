<?php

namespace DTUXBase\Form\Handler\Factory;

use DTUXBase\Form\Handler\AbstracFormHandler;
use Zend\ServiceManager\FactoryInterface;
use Zend\ServiceManager\ServiceLocatorInterface;


class ServerFormHandler implements FactoryInterface
{

    /**
     * Create service
     *
     * @param ServiceLocatorInterface $serviceLocator
     *
     * @return mixed
     */
    public function createService(ServiceLocatorInterface $serviceLocator)
    {
        return new AbstracFormHandler( $serviceLocator->get('SimpleAdmin\Service\Server') );
    }
}