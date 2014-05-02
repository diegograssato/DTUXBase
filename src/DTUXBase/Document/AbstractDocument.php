<?php
namespace DTUXBase\Document;

use Doctrine\ODM\MongoDB\Mapping\Annotations as ODM;

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
    protected $changes = 0;

    /**
     * @ODM\Field(type="string")
     * @ODM\UniqueIndex(order="asc")
     */
    protected $nome;

    /** @ODM\Field(type="date") */
    protected $criado_em;

    /** @ODM\Field(type="date") */
    protected $atualizado_em;

    /**
     * @ODM\PreUpdate
     */
    public function preUpdate()
    {
        $this->changes++;
        $this->atualizado_em = new \MongoDate();
    }

    /**
     * @ODM\PreFlush
     */
    public function preFlush()
    {
        ($this->atualizado_em)? null :$this->atualizado_em = new \MongoDate();
        $this->atualizado_em = new \MongoDate();
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
    public function toJson($debug = true)
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

    public function toDeserializado($debug = true)
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
     * Gets the value of criado_em.
     *
     * @return mixed
     */
    public function getCriado_em()
    {
        return $this->criado_em;
    }

    /**
     * Sets the value of criado_em.
     *
     * @param mixed $criado_em the criado_em
     *
     * @return self
     */
    public function setCriado_em($criado_em)
    {
        $this->criado_em = $criado_em;

        return $this;
    }

    /**
     * Gets the value of atualizado_em.
     *
     * @return mixed
     */
    public function getAtualizado_em()
    {
        return $this->atualizado_em;
    }

    /**
     * Sets the value of atualizado_em.
     *
     * @param mixed $atualizado_em the atualizado_em
     *
     * @return self
     */
    public function setAtualizado_em($atualizado_em)
    {
        $this->atualizado_em = $atualizado_em;

        return $this;
    }
}