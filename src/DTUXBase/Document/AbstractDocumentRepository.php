<?php
namespace DTUXBase\Document;
use Doctrine\ODM\MongoDB\DocumentRepository;

/**
 * Class AbstractDocumentRepository
 * @package DTUXBase\Document
 */
class AbstractDocumentRepository extends DocumentRepository{

    public function fetchParent()
    {
        $entities = $this->findAll();
        $array = array();

        foreach($entities as $entity)
        {
            $array[$entity->getId()]=$entity->getNome();
        }

        return $array;
    }


} 