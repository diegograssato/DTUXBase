<?php
/**
 * Classe de From abstrata
 * @category DTUXBase
 * @package DTUXBase
 * @subpackage From
 * @author Diego Pereira Grassato <diego.grassato@gmail.com>
 * @data 10/12/13 13:35
 */
namespace DTUXBase\Form;
use Zend\ServiceManager\ServiceLocatorAwareInterface,
    Zend\ServiceManager\ServiceLocatorInterface,
    Zend\Form\Form;

use Doctrine\Common\Persistence\ObjectManager,
    DoctrineModule\Persistence\ObjectManagerAwareInterface;
abstract class GenerateForm extends AbstractAdvancedForm implements ServiceLocatorAwareInterface, ObjectManagerAwareInterface
{
    protected $entity;

    public function init()
    {
        parent::init();

        $id = new \Zend\Form\Element\Hidden('id');
        $this->add($id);

        $csrf = new \Zend\Form\Element\Csrf("security");
        $this->add($csrf);

        $csrfs = new \Zend\Form\Element\Csrf("cached");
        $this->add($csrfs);


        $this->add(array
        (
            'name' => "grupo",
            //'type' => 'DoctrineORMModule\Form\Element\EntityRadio',// Radio já traz selecionado - https://github.com/doctrine/DoctrineORMModule/tree/master/src/DoctrineORMModule/Form/Element
            // 'type' => 'DoctrineORMModule\Form\Element\EntityMultiCheckbox', // EntityMultiCheckbox já traz selecionado - https://github.com/doctrine/DoctrineORMModule/tree/master/src/DoctrineORMModule/Form/Element
            'type' => 'DoctrineORMModule\Form\Element\EntitySelect', // Select já traz selecionado - https://github.com/doctrine/DoctrineORMModule/tree/master/src/DoctrineORMModule/Form/Element
            'options' => array (
                'object_manager' =>$this->getObjectManager(),
                'target_class' => 'DTUXAdmin\Document\Grupo',
                'property' => 'nome',
                /* 'find_method' => array(
                     'name'   => 'findBy',
                     'params' => array(
                         'criteria' => array('status' => 1),
                         'orderBy'  => array('nome' => 'ASC'),
                     ),
                 ),*/
            ),
            'attributes' => array(
                'id' => 'grupo',
                'class' => 'form-control selectpicker show-tick show-menu-arrow',
                'data-live-search'=>true,
                'required' => true
            )
        ));



        $this->add(array(
            'name' => 'submit',
            'type'=>'Zend\Form\Element\Submit',
            'attributes' => array(
                'value'=>'Salvar',
                'class' => 'btn btn-primary'
            )
        ));

    }


}
?>