<?php
namespace DTUXBase\Service;

use Zend\Stdlib\Hydrator,
    Zend\Paginator\Paginator,
    Zend\EventManager\EventManagerAwareInterface,
    Zend\EventManager\EventManagerInterface,
    Zend\Authentication\AuthenticationService,
    Zend\Authentication\Storage\Session as SessionStorage;

use DoctrineMongoODMModule\Paginator\Adapter\DoctrinePaginator as ODMPaginator,
    Doctrine\ORM\Tools\Pagination\Paginator as ORMPaginator,
    DoctrineORMModule\Paginator\Adapter\DoctrinePaginator as PaginatorAdapter;

/**
 * Classe de controle serviços
 * @category DTUXBase
 * @package DTUXBase
 * @subpackage Service
 * @author Diego Pereira Grassato <diego.grassato@gmail.com>
 * @data 29/10/13 11:39
 */
abstract class AbstractService extends \DTUXBase\Service\ServiceLocatorAware
{
    /**
     * @var \Zend\ServiceManager\ServiceLocatorInterface $manager ObjectManager
     */
    protected $manager;
    /**
     * @var \DTUXBase\Entity $entity Entidade current
     */
    protected $entity;

    /**
     * @var String $sessionName Armazena o nome de uma sessão
     */
    protected $sessionName;


    /**
     * @var String $transformer Usado para transformar um id em objeto
     */
    protected $transformer;

       /**
     * @var EventManagerInterface
     */
    protected $eventManager;

    public function __construct($entity)
    {
        $this->entity = $entity;
    }

    /**
     * Insere registro no banco e altera
     * @todo InsertOrUpdate insere e atualiza um registro no banco ele identifica atravéz do array de dados e se tiver um ID ele atualiza caso contrario insere
     * @param array $data
     * @return mixed
     */
    public function salvar(array $data)
    {

        if (!is_array($data)) {
            throw new \InvalidArgumentException('É necessário passar um array de dados para completar esta operação');
        }

        $entity = new $this->entity($data);
        if (isset($data['id']) && $data['id'] > 0) {
            $entity = $this->getManager()->getReference($this->entity, $data['id']);
            (new Hydrator\ClassMethods())->hydrate($data, $entity);

        }

        $this->getManager()->persist($entity);
        $this->getManager()->flush();
        return $entity;
    }

    /**
     * Obtem a conectividade com o banco de dados
     * @todo Obtem a conectividade com o banco de dados
     * @return \Zend\ServiceManager\ServiceLocatorInterface Manager
     */
    public function getManager()
    {

        $this->manager = $this->getServiceLocator()->get('manager');
        return $this->manager;
    }

    /**
     * Obtem a conectividade com o banco de dados
     * @todo Obtem a conectividade com o banco de dados
     * @return \Zend\ServiceManager\ServiceLocatorInterface Manager
     */
    public function getDataTransformer()
    {

        $this->transformer = $this->getServiceLocator()->get('DataTransformer');
        return $this->transformer;
    }



    /**
     * Insere registro no banco
     * @todo Insert insere um registro no banco
     * @param array $data
     * @return mixed
     */
    public function inserir($entity)
    {
        if (is_null($entity)) {
            throw new \InvalidArgumentException('É necessário passar um array de dados para completar esta operação');
        }
        echo get_class($entity);
        $this->getManager()->persist($entity);
        $this->getManager()->flush();
        return $entity;
    }



    /**
     * Remove um registro no banco
     * @todo Delete remove registro no banco ele identifica atravéz de um ID
     * @param $id
     * @return mixed
     */
    public function delete($id)
    {

        if (!isset($id)) {
            throw new \InvalidArgumentException('É necessário ter um indentificador para completar esta operação');
        }
        $entity = $this->getManager()->getReference($this->entity, $id);
        if ($entity) {
            $this->getManager()->remove($entity);
            $this->getManager()->flush();
            return $id;
        }
    }

    /**
     * Realiza a busca e retorna um paginator
     * @todo Pode ser passa um campo ou um array de dados, no caso de array as chaves serão os campos de do banco e os values os valores
     * @param string|array $busca Parametro(s) para realização de busca
     * @param int $page Número da Página
     * @param string $campoPrincipal Determina um campo como o principal para a busca
     * @param string $totalPorPagina Total de itens por página
     * @return Paginator
     */
    public function getDataPaginator($busca = NULL, $page = 1, $campoPrincipal = 'nome', $totalPorPagina = '5')
    {


            $search = $this->getManager()->getRepository($this->entity)->createQueryBuilder();
            if (is_array($busca)) {
                foreach ($busca as $key => $value) {
                    if ($key == $campoPrincipal) {
                        $search->field($campoPrincipal)->equals(new \MongoRegex('/.*' . $value . '.*/i'));
                    } else {
                        $search->field($key)->equals($value);
                    }
                }
            }
            if (is_string($busca)) {
                $search->field($campoPrincipal)->equals(new \MongoRegex('/.*' . $busca . '.*/i'));
            }

           $fetch = $search->getQuery()
                ->execute();
           $adapter = new ODMPaginator($fetch);

        $paginator = new Paginator($adapter, $fetchJoinCollection = true);
        $paginator->setCurrentPageNumber($page)
            ->setItemCountPerPage($totalPorPagina);

        return $paginator;
    }

    /**
     * Realiza a busca e retorna um array assiciativo
     * @todo Pode ser passa um campo ou um array de dados, no caso de array as chaves serão os campos de do banco e os values os valores
     * @param string|array $busca Parametro(s) para realização de busca
     * @param string $campoPrincipal Determina um campo como o principal para a busca
     * @return Array
     */
    public function filtroBusca($busca = NULL, $campoPrincipal = 'nome')
    {
        $fetch = null;

            $search = $this->getManager()->getRepository($this->entity)->createQueryBuilder();
            if (is_array($busca)) {
                foreach ($busca as $key => $value) {
                    if ($key == $campoPrincipal) {
                        $search->field($campoPrincipal)->equals(new \MongoRegex('/.*' . $value . '.*/i'));
                    } else {
                        $search->field($key)->equals($value);
                    }
                }
            }
            if (is_string($busca)) {
                $search->field($campoPrincipal)->equals(new \MongoRegex('/.*' . $busca . '.*/i'));
            }

            $fetch = $search->getQuery()
                ->execute()->toArray();



        return $fetch;
    }

    /**
     * Realiza busca pelo ID e retorna um entidade utilizada para o Edit
     * @todo Ele faz a busca através da Entidade current caso for passado outro ele sobrescreve o padrão
     * @param $id ID do item a ser consultado
     * @param $newEntity Uma entidade diferente do controller em questão
     * @return $entity Retorna a endidade do ID requisitado
     */
    public function findOneEntity($id, $newEntity = null)
    {
        if (!isset($id)) {
            throw new \InvalidArgumentException('É necessário ter um indentificador para completar esta operação');
        }
        if (is_string($newEntity))
            $this->entity = $newEntity;

        $repository = $this->getManager()->getRepository($this->entity);
        $entity = $repository->find($id);
        return $entity;
    }

    /**
     * Obtem a entidade do usuário em sessao
     * @todo Obtém o objeto do usuário logado no momento
     * @return object
     */
    public function getUserCurrent()
    {
        $this->sessionName = ($this->getSessionName())?$this->getSessionName() : "DTuX";
        $sessionStorage = new SessionStorage($this->sessionName);
        $authService = new AuthenticationService;
        $authService->setStorage($sessionStorage);

        if ($authService->hasIdentity()){

            $user = $authService->getIdentity();
            return $user;

        }else{
            header('Location: http://'.$_SERVER['SERVER_NAME'].'/auth');
            exit();
        }
    }

    /**
     * @return \DTUXBase\Service\String
     */
    public function getSessionName()
    {
        return $this->sessionName;
    }

    /**
     * @param \DTUXBase\Service\String $sessionName
     */
    public function setSessionName($sessionName)
    {
        $this->sessionName = $sessionName;
    }


    /**
     * @param  EventManagerInterface $eventManager
     * @return void
     */
    public function setEventManager(EventManagerInterface $eventManager)
    {
        $eventManager->addIdentifiers(array(
            get_called_class()
        ));
        $this->eventManager = $eventManager;
    }

    /**
     * @return EventManagerInterface
     */
    public function getEventManager()
    {
        if (null === $this->eventManager) {
            $this->setEventManager(new \Zend\EventManager\EventManager());
        }

        return $this->eventManager;
    }


}
