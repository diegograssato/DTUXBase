<?php
namespace DTUXBase\Controller;

use Zend\Mvc\Controller\AbstractActionController,
    Zend\View\Model\ViewModel,
    Zend\View\Model\JsonModel;
use Zend\Stdlib\Hydrator;
use DTUXBase\Logger\Logger as Logger;

/**
 * Classe de controle abstrata
 * @category DTUXBase
 * @package DTUXBase
 * @subpackage Controller
 * @author Diego Pereira Grassato <diego.grassato@gmail.com>
 * @data 31/10/13 11:39
 */
abstract class AbstractController extends AbstractActionController
{
    /**
     * @var \DTUXBase\Service\AbstractService $service Serviço responsável pela fucionalidades relacionadas a banco de dados
     */
    protected $service;

    /**
     * @var \DTUXBase\Entity $entity Entidade current
     */
    protected $entity;

    /**
     * @var \Zend\Form\Form $form Formulário
     */
    protected $form;

    /**
     * @var $route Rota para redirecionamento default
     */
    protected $route;

    /**
     * @var \Zend\Mvc\Controller\AbstractActionController $controller Nome do controller
     */
    protected $controller;

    /**
     * Index principal, se for requisição do tipo GET/POST o retorno será uma ViewModel
     * Se a requisição for XmlHttpRequest será retornado um JsonModel
     * @return array|JsonModel|ViewModel
     */
    public function indexAction()
    {
        $busca = $this->params()->fromRoute('busca');
        $page = $this->params()->fromRoute('page');
        $service = $this->getServiceLocator()->get($this->service);

        if ($this->getRequest()->isXmlHttpRequest()) {
            $list = $service->fetchBase($busca);
            return new JsonModel(array('busca' => $list));
        } else {
            $paginator = $service->fetchPaginator($busca,$page);
            return new ViewModel(array('data' => $paginator, 'page' => $page, 'busca' => $busca));
        }


    }

    /**
     * Classe simples para construção única de new e edit em uma única função
     * @return \Zend\Http\Response|ViewModel
     */
    public function cadastroAction()
    {
        //$form = $this->serviceLocator->get('FormElementManager')->get($this->form);
        $serviceForm = $this->getServiceLocator()->get('FormElementManager');
        if(is_string($this->form))
            $form = $serviceForm->get($this->form);
        else
            $form = $serviceForm->get($this->form);

        $request = $this->getRequest();
        $id = $this->params()->fromRoute('id', 0);

        $service = $this->getServiceLocator()->get($this->service);



        if ($id) {
            $entity = $service->findOneEntity($id);
            $array = $entity->toArray();
           // print_r($array);
            $form->setData($array);
            if(empty($array['password']))
                unset($array['password']);
        }

        if ($request->isPost()) {
            $form->setData($request->getPost());
            if ($form->isValid()) {
                $data = $request->getPost()->toArray();
               //$service = $this->getServiceLocator()->get($this->service);
                //print_r($data);exit;
                if($service->insertOrUpdate($data)){
                    $this->flashMessenger()->setNamespace('success')->addMessage('Dados salvos com sucesso!');
                }else{
                        $this->flashMessenger()->setNamespace('info')->addMessage('Falha ao salvar com sucesso!');
                }
                return $this->redirect()->toRoute($this->route, array('controller' => $this->controller));
            }
        }

        return new ViewModel(array('form' => $form));
    }

    /**
     * Classe responsável por cadastrar registros
     * @return \Zend\Http\Response|ViewModel
     */
    public function newAction()
    {
        if(is_string($this->form))
            $form = new $this->form;
        else
            $form = new $this->form();
        $request = $this->getRequest();

        if ($request->isPost()) {
            $form->setData($request->getPost());
            if ($form->isValid()) {
                $service = $this->getServiceLocator()->get($this->service);
                $service->insert($request->getPost()->toArray());

                return $this->redirect()->toRoute($this->route, array('controller' => $this->controller));
            }
        }

        return new ViewModel(array('form' => $form));
    }

    /**
     * Classe responsável por visualizar informações completas quando desejado
     * @return \Zend\Http\Response|ViewModel
     */
    public function viewAction()
    {
        $id = $this->params()->fromRoute('id', 0);
        $service = $this->getServiceLocator()->get($this->service);
        $entity = $service->findOneEntity($id);
        return new ViewModel(array('entity' => $entity));
    }

    /**
     * Classe responsável por editar registros
     * @return \Zend\Http\Response|ViewModel
     */
    public function editAction()
    {
        if(is_string($this->form))
            $form = new $this->form;
        else
            $form = new $this->form();
        $request = $this->getRequest();
        $id = $this->params()->fromRoute('id', 0);
        $service = $this->getServiceLocator()->get($this->service);
        $entity = $service->findOneEntity($this->entity, $id);

        if ($this->params()->fromRoute('id', 0)) {
            $array = $entity->toArray();
            unset($array['password']);
            $form->setData($array);
        }


        if ($request->isPost()) {
            $form->setData($request->getPost());
            if ($form->isValid()) {
                $service = $this->getServiceLocator()->get($this->service);
                if($service->update($request->getPost()->toArray())){
                    $this->flashMessenger()->setNamespace('success')->addMessage('Dados salvos com sucesso!');
                }else{
                    $this->flashMessenger()->setNamespace('info')->addMessage('Falha ao salvar com sucesso!');
                }

                return $this->redirect()->toRoute($this->route, array('controller' => $this->controller));
            }
        }

        return new ViewModel(array('form' => $form));
    }

    /**
     * Classe responsável por remover registros
     * @return \Zend\Http\Response
     */
    public function deleteAction()
    {
        $service = $this->getServiceLocator()->get($this->service);
        if ($service->delete($this->params()->fromRoute('id', 0))){
            $this->flashMessenger()->setNamespace('success')->addMessage('Registro removido com sucesso!');
        }else{
            $this->flashMessenger()->setNamespace('info')->addMessage('Falha ao remover resgistro!');
        }
        return $this->redirect()->toRoute($this->route,array('controller'=>$this->controller));
    }

    /**
     * activeAction
     *
     * Ativa ou desativa o registro
     *
     * @author Jesus Vieira <jesusvieiradelima@gmail.com>
     * @access public
     * @return redirect current controller
     */

    public function activeAction()
    {
        $id = $this->params()->fromRoute('id', 0);
        $service = $this->getServiceLocator()->get($this->service);
        $entity = $service->findOneEntity($id);
        $response = $this->getResponse();
        $data = null;
        if ($entity) {
            $data = $entity->toArray();

            if ($data['active']){
                $data['active'] = 0;
            }else{
                $data['active'] = 1;
            }

            if ($service->update($data)) {
                $response->setStatusCode(200);
            } else {
                $response->setStatusCode(404);
            }


        }
        $response->setContent( $data['active'] );
        return $response;
    }

    /**
     * Classe responsável por editar registros
     * @return \Zend\Http\Response|ViewModel
     */
    public function editableAction()
    {
        $response = $this->getResponse();
        $request = $this->getRequest();
        $service = $this->getServiceLocator()->get($this->service);
        $data = $request->getPost()->toArray();

        if ($service->update($data)) {
            $response->setStatusCode(200);
        } else {
            $response->setStatusCode(404);
        }

        $response->setContent(1);
        return $response;
    }
}
