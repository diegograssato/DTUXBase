<?php
namespace DTUXBase\Service;
use Zend\ServiceManager\ServiceLocatorAwareInterface,
    Zend\ServiceManager\ServiceLocatorInterface;

/**
 * Classe que requisita informações sobre a conectividade via serviceLocator
 * @category DTUXBase
 * @package DTUXBase
 * @subpackage Service
 * @author Diego Pereira Grassato <diego.grassato@gmail.com>
 * @data 31/10/13 11:39
 */
class ServiceLocatorAware implements ServiceLocatorAwareInterface
{
    /**
     * @var \Zend\ServiceManager\ServiceLocatorInterface $sm
     */
    protected $sm;

    /**
     * Set the service locator
     *
     * @param ServiceLocatorInterface $sm
     *
     * @return void
     */
    public function setServiceLocator(ServiceLocatorInterface $sm)
    {
        if ($sm instanceof \Zend\ServiceManager\ServiceLocator) {
            throw new \InvalidArgumentException('É necessário sem uma instancia valida de ServiceLocator');
        }
        $this->sm = $sm;
    }

    /**
     * Get the service locator
     *
     * @return ServiceLocator ServiceLocator instance
     */
    public function getServiceLocator()
    {
        return $this->sm;
    }

}