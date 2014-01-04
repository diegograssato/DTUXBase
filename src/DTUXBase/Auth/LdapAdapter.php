<?php

namespace DTUXBase\Auth;

use Zend\Authentication\Adapter\AdapterInterface,
    Zend\Authentication\Result;

/**
 * Adaptador padrão de autenticação
 * @category DTUXBase
 * @package DTUXBase
 * @subpackage Auth
 * @author Diego Pereira Grassato <diego.grassato@gmail.com>
 * @data 31/10/13 11:39
 */
class LdapAdapter extends  \DTUXBase\Service\ServiceLocatorAware implements AdapterInterface
{
    protected $entity;
    protected $username;
    protected $password;


    public function getUsername()
    {
        return $this->username;
    }

    public function setUsername($username)
    {
        $this->username = $username;
    }

    public function getPassword()
    {
        return $this->password;
    }

    public function setPassword($password)
    {
        $this->password = $password;
    }

    /**
     * @param mixed $entity
     */
    public function setEntity($entity)
    {
        $this->entity = $entity;
    }

    /**
     * @return mixed
     */
    public function getEntity()
    {
        return $this->entity;
    }

    /**
     * @return Result
     */
    public function authenticate()
    {
        /*$repository = $this->getManager()->getRepository($this->getEntity());
        $user = $repository->findByEmailAndPassword($this->getUsername(), $this->getPassword());
        if ($user)
            return new Result(Result::SUCCESS, array('user' => $user), array('OK'));
        else
            return new Result(Result::FAILURE_CREDENTIAL_INVALID, null, array());
        */

    }



}
