<?php
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
