<?php
namespace DTUXBase\View\Helper;

use Zend\View\Helper\AbstractHelper,
    Zend\Authentication\AuthenticationService;

/**
 * Classe que ainda será completadada
 * Enviar para view a aprovação ou reprovação de um recurso
 * @category DTUXBase
 * @package DTUXBase
 * @subpackage View\Helper
 * @author Diego Pereira Grassato <diego.grassato@gmail.com>
 * @data 30/10/13 19:30
 */
class FirewallLink  extends AbstractHelper
{
    /**
     * @var ACL
     */
    protected $aclService;

    /**
     * @param $resource Recurso que o usuário esta requisitando
     * @param null $privilege O Privilegio que o usuário esta requisitando
     * @return mixed
     */
    public function isAllowed($resource, $privilege = null)
    {
        return $this->getAclService()->isAllowed($resource, $privilege);
    }

    /**
     * @return ACL
     */
    public function getAclService()
    {
        return $this->aclService;
    }

    /**
     * @param $aclService
     */
    public function setAclService($aclService)
    {
        $this->aclService = $aclService;
    }
}