<?php
namespace DTUXBase\Document;

use Zend\Stdlib\Hydrator;
use Doctrine\ODM\MongoDB\Mapping\Annotations as ODM;

/**
 * @ODM\MappedSuperclass
 * @HasLifecycleCallbacks
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
     * @ODM\Index
     */
    protected $changes = 0;

    /**
     * @ODM\String
     * @ODM\Index
    */
    protected $nome;

    /**
     * @ODM\Date
     * @ODM\Index
     */
    protected $createdAt;

    /**
     * @ODM\Date
     * @ODM\Index
     */
    protected $updatedAt;


    /**
     * @ODM\PreUpdate
     */
    public function preUpdate()
    {
        $this->changes++;
        $this->updatedAt = new \MongoDate();
    }

    /**
     * @ODM\PreFlush
     */
    public function preFlusht()
    {
        ($this->createdAt)? null :$this->createdAt = new \MongoDate();
        $this->updatedAt = new \MongoDate();
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
     * @param mixed $createdAt
     */
    public function setCreatedAt($createdAt)
    {
        $this->createdAt = $createdAt;
    }

    /**
     * @return mixed
     */
    public function getCreatedAt()
    {
       return $this->createdAt;
    }

    /**
     * @param mixed $id
     */
    public function setId($id)
    {
        $this->id = $id;
    }

    /**
     * @return mixed
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * @param mixed $nome
     */
    public function setNome($nome)
    {
        $this->nome = $nome;
    }

    /**
     * @return mixed
     */
    public function getNome()
    {
        return $this->nome;
    }

    /**
     * @param mixed $updatedAt
     */
    public function setUpdatedAt($updatedAt)
    {
        $this->updatedAt = $updatedAt;
    }

    /**
     * @param string $format
     * @return mixed
     */
    public function getUpdatedAt()
    {
       return $this->updatedAt;
    }

}