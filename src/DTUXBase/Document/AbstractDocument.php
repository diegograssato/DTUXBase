<?php
namespace DTUXBase\Document;

use Doctrine\ODM\MongoDB\Mapping\Annotations as ODM;
use Zend\Stdlib\Hydrator;

/**
 * @ODM\MappedSuperclass
 * @ODM\HasLifecycleCallbacks
 */
abstract class AbstractDocument
{
    /**
     * @ODM\Id
     * @ODM\Index
     */
    protected $id;

    /**
     * @ODM\Increment
     */
    public $alteracoes = 0;

    /**
     * @ODM\Field(type="string")
     * @ODM\UniqueIndex(order="asc")
     */
    protected $nome;

    /** @ODM\Field(type="boolean") */
    private $ativo = true;

    /** @ODM\Field(type="date") */
    protected $criadoEm;

    /** @ODM\Field(type="date") */
    protected $atualizadoEm;

    /**
     * @ODM\PreUpdate
     */
    public function preUpdate()
    {
        $this->alteracoes++;
        $this->atualizadoEm = new \MongoDate();
    }

    /**
     * @ODM\PreFlush
     */
    public function preFlush()
    {
        ($this->criadoEm)? null :$this->criadoEm = new \MongoDate();
        $this->atualizadoEm = new \MongoDate();
    }


    /************************************************************************************************
     * *************************** Carrega uma entidade ou clona  ***********************************
     * **********************************************************************************************
     */
    /**
     * Load document
     * @param \Doctrine\ODM\MongoDB\DocumentManager $dm
     * @param mixed $id
     * @param bool $require
     * @throws \Exception
     */
    public static function load(\Doctrine\ODM\MongoDB\DocumentManager $dm, $id = '', $require = false)
    {
        $documentName = get_called_class();
        $document = null;

        if (!empty($id)) {
            $document = $dm->find($documentName, $id);
        }

        if ($require && is_null($document)) {
            throw new \Exception('Document not found');
        }

        return $document;
    }

    /************************************************************************************************
     * ************************* Transforma Entidade em um Array  ***********************************
     * **********************************************************************************************
     */
    public function toArray()
    {
        return (new Hydrator\ClassMethods())->extract($this);
    }

    /************************************************************************************************
     * ************************* Transforma Entidade em um JSon  ***********************************
     * **********************************************************************************************
     */
    public function toJson($debug = false)
    {
        $json = \Zend\Json\Json::encode((new Hydrator\ClassMethods())->extract($this), true);
        if ($debug)
            $json = \Zend\Json\Json::prettyPrint($json, array("indent" => " # "));
        return $json;
    }

    public function toSerializado()
    {
        $serializer =  \Zend\Serializer\Serializer::factory('phpserialize');
        $objSerializado =  $serializer->serialize($this);
        return $objSerializado;
    }

    public function toDeserializado()
    {
        $serializer =  \Zend\Serializer\Serializer::factory('phpserialize');
        $objSerializado =  $serializer->unserialize(self::toSerializado());
        return $objSerializado;
    }

    /************************************************************************************************
     * ***************************** Getter and Setter Property  ************************************
     * **********************************************************************************************
     */
    /**
     * @param $property
     * @return mixed
     */
    public function getProperty($property)
    {
        if (property_exists($this, $property)) {
            return $this->$property;
        }
    }

    /**
     * @param $property
     * @param $value
     * @return $this
     */
    public function setProperty($property, $value)
    {
        if (property_exists($this, $property)) {
            $this->$property = $value;
        }
        return $this;
    }


    /************************************************************************************************
     * *********************************** Getters and Setters  *************************************
     * **********************************************************************************************
     */
    /**
     * @param mixed $changes
     */
    public function setChanges($changes)
    {
        $this->changes = $changes;
    }

    /**
     * @return mixed
     */
    public function getChanges()
    {
        return $this->changes;
    }


    /**
     * Gets the value of id.
     *
     * @return mixed
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * Sets the value of id.
     *
     * @param mixed $id the id
     *
     * @return self
     */
    public function setId($id)
    {
        $this->id = $id;

        return $this;
    }

    /**
     * Gets the value of nome.
     *
     * @return mixed
     */
    public function getNome()
    {
        return $this->nome;
    }

    /**
     * Sets the value of nome.
     *
     * @param mixed $nome the nome
     *
     * @return self
     */
    public function setNome($nome)
    {
        $this->nome = $nome;

        return $this;
    }

    /**
     * Gets the value of criadoEm.
     *
     * @return mixed
     */
    public function getCriadoEm()
    {
        return $this->criadoEm;
    }

    /**
     * Sets the value of criadoEm.
     *
     * @param mixed $criadoEm the criado em
     *
     * @return self
     */
    public function setCriadoEm($criadoEm)
    {
        $this->criadoEm = $criadoEm;

        return $this;
    }

    /**
     * Gets the value of atualizadoEm.
     *
     * @return mixed
     */
    public function getAtualizadoEm()
    {
        return $this->atualizadoEm;
    }

    /**
     * Sets the value of atualizadoEm.
     *
     * @param mixed $atualizadoEm the atualizado em
     *
     * @return self
     */
    public function setAtualizadoEm($atualizadoEm)
    {
        $this->atualizadoEm = $atualizadoEm;

        return $this;
    }


    /**
     * Gets the value of ativo.
     *
     * @return mixed
     */
    public function getAtivo()
    {
        return $this->ativo;
    }

    /**
     * Sets the value of ativo.
     *
     * @param mixed $ativo the ativo
     *
     * @return self
     */
    public function setAtivo($ativo)
    {
        $this->ativo = $ativo;

        return $this;
    }

}