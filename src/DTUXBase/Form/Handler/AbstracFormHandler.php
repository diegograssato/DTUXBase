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
     * @var Entidade responsavel pela manipulacao
     */
    protected $entity;

    public function __construct()
    {

    }
    /**
     * Manipula o formulário de cadastro de resgate.
     *
     * @param  FormInterface $form
     * @param  Request       $request
     *
     * @throws \RuntimeException se ocorrer uma falha durante o processo de inserção na base do PontuacaoResgate.
     *
     * @return false se o formulário ou o tipo da requisição é inválido. true se a operação deu certo.
     */
    public function handle(FormInterface $form, Request $request)
    {
        $request = $this->getRequest();
        try {

            if (!$request->isPost())
                return false;

            $form->setData($request->getPost());


            if (!$form->isValid())
                return false;

            $data = $request->getPost()->toArray();

            $service->insertOrUpdate($data));


        } catch (\Exception $e) {
            //echo $e->getMessage(); exit;
            return false;
        }

        return true;
    }
}