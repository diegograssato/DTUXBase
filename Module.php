<?php
/**
 * This file is placed here for compatibility with Zendframework 2's ModuleManager.
 * It allows usage of this module even without composer.
 * The original Module.php is in 'src/DTUXBase' in order to respect PSR-0
 */
//require_once __DIR__ . '/src/DTUXBase/Module.php';
namespace DTUXBase;

use DoctrineModule\Persistence\ObjectManagerAwareInterface;

class Module
{

    public function getAutoloaderConfig()
    {
        return array(
            'Zend\Loader\StandardAutoloader' => array(
                'namespaces' => array(
                    __NAMESPACE__ => __DIR__ . '/src/' . __NAMESPACE__,
                ),
            ),
        );
    }

    public function getFormElementConfig()
    {
        return array(
            'initializers' => array(
                'ObjectManagerInitializer' => function ($element, $formElements) {
                        if ($element instanceof ObjectManagerAwareInterface) {
                            // var_dump(get_class($element));
                            $services = $formElements->getServiceLocator();
                            $entityManager = $services->get('manager');
                            $element->setObjectManager($entityManager);
                        }
                    },
            ),
        );
    }

    public function getViewHelperConfig()
    {
        return array(
            'invokables' => array(
                'UserIdentity' => new \DTUXBase\View\Helper\UserIdentity(), //Registra a entidade do usuário que está em sessao
                'menuBar' => 'DTUXBase\View\Helper\MenuBar',
                'elementToRow' => 'DTUXBase\View\Helper\ElementToRow',
                'firewall' => 'DTUXBase\View\Helper\FirewallLink'

            ),
            'factories' => array(
                'Router' => function ($sm) {
                        return new \DTUXBase\View\Helper\Router($sm->getServiceLocator()->get('application')->getMvcEvent()->getRouteMatch()); // Registra a rota traçada para ser passado para view
                    },

                /*
                 * Implementar ACLs
                 *

                 'firewall' => function ($serviceManager) {
                        $helper = \DTUXBase\View\Helper\FirewallLink();
                        //$helper->setAclService($serviceManager>getServiceLocator()->get('ZfcAcl\Service\Acl'));
                        return $helper;
                    },
 */
                'flashMessage' => function ($serviceManager) {
                        $flashmessenger = $serviceManager->getServiceLocator()->get('ControllerPluginManager')->get('flashmessenger');
                        $message = new \DTUXBase\View\Helper\FlashMessages();
                        $message->setFlashMessenger($flashmessenger);

                        return $message;
                    },
                'absoluteUrl' => function ($sm) {
                        $locator = $sm->getServiceLocator();
                        return new \DTUXBase\View\Helper\AbsoluteUrl($locator->get('Request'));
                    },

            ),

        );
    }
}
