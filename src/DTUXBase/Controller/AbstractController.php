<?php
namespace DTUXBase\Controller;

use Zend\Mvc\Controller\AbstractActionController,
    Zend\View\Model\ViewModel,
    Zend\View\Model\JsonModel,
    Zend\Stdlib\Hydrator,
    Zend\Authentication\AuthenticationService,
    Zend\Authentication\Storage\Session as SessionStorage;

use DTUXBase\Logger\Logger as Logger;
use Zend\Form\Annotation\AnnotationBuilder;
use Zend\Form\Factory;
use DoctrineModule\Stdlib\Hydrator\DoctrineObject as DoctrineHydrator;

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
     * @var \Zend\Form\Form $form Formulário
     */
    protected $formHandler;

    /**
     * @var $route Rota para redirecionamento default
     */
    protected $route;

    /**
     * @var $dataTransformer Campo e stringEntity que irao ser transformados
     */
    protected $dataTransformer = array();

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
     * @return \Zend\Form\Form
    */
    public function createForm()
    {


        $service = $this->getServiceLocator()->get( $this->service );
        $serviceForm = $this->getServiceLocator()->get( 'FormElementManager' );


        $builder = new AnnotationBuilder( $serviceForm );

        $entity = new $this->entity;

        /** @var $form \Zend\Form\Form **/
        $form = $builder->createForm( $this->entity );
        $form->add(new \Zend\Form\Element\Csrf('security'));

        foreach ($this->dataTransformer as $class => $stringEntity) {

            $form->get( $class )->setOptions(
                array(
                    'object_manager' => $service->getManager(),
                    'target_class' => $stringEntity
                )
            );
        }

        return $form;

    }

    /**
     * Classe simples para construção única de new e edit em uma única função
     * @return \Zend\Http\Response|ViewModel
    */
    public function cadastroAction()
    {


        $service = $this->getServiceLocator()->get( $this->service );
        $formHandle = $this->getServiceLocator()->get( $this->formHandler );

        $request = $this->getRequest();

        $form = $this->createForm();

        if( $request->isPost() ) {
            if ( $formHandle->handle($form, $request, $this->dataTransformer)  ){
                $this->flashMessenger()->setNamespace('success')->addMessage('Dados salvos com sucesso!');

             return $this->redirect()->toRoute( $this->route );

            }

        }

        if ( 0 !== ( $id = $this->params()->fromRoute('id', 0) ) ) {



            if (! is_object( $entity = $service->findOneEntity($id) ) ) {

                $this->flashMessenger()->setNamespace('danger')->addMessage('Entidade nao encontrada!');

                return $this->redirect()->toRoute( $this->route );
            }

            $array = $entity->toArray();

            if( array_key_exists('ip_address',$array) )
                $array['ipAddress'] = $array['ip_address'];

            $form->setData($array);

            if( array_key_exists('password',$array) )
                unset($array['password']);
        }



        $viewModel = new ViewModel();
        $viewModel->setVariable('form',$form);

        return $viewModel;
    }

    /**
     * Classe simples para construção única de new e edit em uma única função
     * @return \Zend\Http\Response|ViewModel
     */
    public function cadastroxAction()
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



        if (null != $id) {
            if (!is_object($entity = $service->findOneEntity($id))) {
                $this->flashMessenger()->setNamespace('danger')->addMessage('Entidade nao encontrada!');
                return $this->redirect()->toRoute($this->route, array('controller' => $this->controller));
            }
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
     * Classe responsável por visualizar informações completas quando desejado
     * @return \Zend\Http\Response|ViewModel
     */
    public function viewAction()
    {
        $id = $this->params()->fromRoute('id', 0);

        $service = $this->getServiceLocator()->get($this->service);

        if (!is_object($entity = $service->findOneEntity($id))) {
            $this->flashMessenger()->setNamespace('danger')->addMessage('Entidade nao encontrada!');
            return $this->redirect()->toRoute($this->route, array('controller' => $this->controller));
        }

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

         $response = null ;
         $request = $this->getRequest();

         $id = $this->params()->fromRoute('id', 0);
         if ( "DELETE" == $request->getMethod() && ("undefined" != $id) ){

                 // Faz a deleção
                $response['code'] = 200;
                $response['id'] = $id;
                $service = $this->getServiceLocator()->get($this->service);
                $service->delete($id);

         }else{
                $response['code'] = 400;
                if(null == $id){
                    $response['id'] = $id;
                    $response['mensage'] = "O ID não pode ser nulo";
                }
                $response['id'] = $id;
                $response['mensage'] = "Função só aceita DELETE";


         }
         return new JsonModel($response );
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
