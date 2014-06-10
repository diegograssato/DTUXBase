<?php

namespace DTUXBase\Form\Handler;

use Zend\Http\Request ;
use Zend\Form\FormInterface;

/**
 * Manipula formulários de criação.
 */
class AbstracFormHandler
{

     /**
     * @var \DTUXBase\Service\AbstractService
     */
    protected $serviceLocator;

    public function __construct($serviceLocator)
    {
        $this->serviceLocator = $serviceLocator;
    }
    /**
     * Manipula o formulário de cadastro de resgate.
     *
     * @param  FormInterface $form
     * @param  Request       $request
     *
     * @return false se o formulário ou o tipo da requisição é inválido. true se a operação deu certo.
     */
    public function handle(FormInterface $form, Request $request, $dataTransformer )
    {

        try {

            if (!$request->isPost())
                return false;


            $form->setData($request->getPost());

            if(! $form->setData( $request->getPost() ) )
                return false;

            if (! $form->isValid() )
                return false;

            $data = $form->getData();

            if( null !== $dataTransformer)
                $data = $this->transformer($data, $dataTransformer);

            $this->getServiceLocator()->salvar( $data );

        } catch (\Exception $e) {
            return false;
        }

        return true;
    }


    public function transformer($data, $dataTransformer)
    {

        foreach ($dataTransformer as $field => $entity) {

                $dataTransformer = $this->getServiceLocator()->getDataTransformer();
                $dataTransformer->setEntity($entity);
                $filtro = $dataTransformer->filter( $data[$field] );

                $data[$field] = $filtro;

        }

        return $data;
    }

    /**
     * Gets the value of serviceLocator.
     *
     * @return \DTUXBase\Service\AbstractService
     */
    public function getServiceLocator()
    {
        return $this->serviceLocator;
    }

    /**
     * Sets the value of serviceLocator.
     *
     * @param \DTUXBase\Service\AbstractService $serviceLocator the service locator
     *
     * @return self
     */
    public function setServiceLocator(\DTUXBase\Service\AbstractService $serviceLocator)
    {
        $this->serviceLocator = $serviceLocator;

        return $this;
    }
}