<?php
/**
 * Classe de From abstrata
 * @category DTUXBase
 * @package DTUXBase
 * @subpackage From
 * @author Diego Pereira Grassato <diego.grassato@gmail.com>
 * @data 10/12/13 13:35
 */
namespace DTUXBase\Form;
use Zend\ServiceManager\ServiceLocatorAwareInterface,
    Zend\ServiceManager\ServiceLocatorInterface,
    Zend\Form\Form;

use Doctrine\Common\Persistence\ObjectManager,
    DoctrineModule\Persistence\ObjectManagerAwareInterface;
abstract class AbstractForm extends Form implements ServiceLocatorAwareInterface, ObjectManagerAwareInterface
{

    protected $objectManager;
    protected $serviceLocator;
    protected $serviceManager;

    public function setServiceLocator(ServiceLocatorInterface $serviceLocator)
    {
        $this->serviceLocator = $serviceLocator;
    }

    public function getServiceLocator()
    {
        return $this->serviceLocator;
    }

    public function setObjectManager(ObjectManager $objectManager)
    {
        $this->objectManager = $objectManager;
    }

    public function getObjectManager()
    {

        return $this->objectManager;
    }
}
?>