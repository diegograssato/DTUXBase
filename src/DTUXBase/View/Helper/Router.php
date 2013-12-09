<?php
namespace DTUXBase\View\Helper;
use Zend\View\Helper\AbstractHelper;

/**
 * Classe que enviar a entidade do usuário logado para view
 * @category DTUXBase
 * @package DTUXBase
 * @subpackage View\Helper
 * @author Diego Pereira Grassato <diego.grassato@gmail.com>
 * @data 31/10/13 19:30
 */
class Router extends AbstractHelper
{
    /**
     * @var Rota
     */
    protected $router;

    /**
     * @param $router Contrutor padrão
     */
    public function __construct($router)
    {
        $this->router = $router;
    }

    /**
     * @todo É invocado toda vez que ouver um acesso a roteamento
     * @return array Quando invocado aramezana na variavel 'router' a rota que foi acessada
     */
    public function __invoke()
    {
        return array(
            'router' => $this->router,
        );
    }
}