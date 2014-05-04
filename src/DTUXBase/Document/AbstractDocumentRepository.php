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

     /**
     * Busca base
     *
     * @param  array $filtros   filtros a serem aplicados
     * @param  boolean $executar           true para que a query seja executada (padrão)
     *                                     false para que seja retornado QueryBuilder
     * @return array|QueryBuilder|null
     */
    public function buscar($filtros, $executar = true)
    {
        $qb = $this->createQueryBuilder();

        // Consulta: http://doctrine-mongodb-odm.readthedocs.org/en/latest/reference/query-builder-api.html#conditional-operators

        if (array_key_exists('id', $filtros) && (!empty($filtros['id'])) ) {
            $id = (string)$filtros['id'];
            $qb->field('id')->equals( $id );
        }

        if (array_key_exists('nome', $filtros) && (!empty($filtros['nome'])) ) {
            $nome = (string)$filtros['nome'];
            $qb->field('nome')->equals( $nome );
        }



        if (array_key_exists('ativo', $filtros) && (!empty($filtros['ativo'])) ) {
            $ativo = (boolean)$filtros['ativo'];
            $qb->field('ativo')->exists($ativo);
        }

        if(array_key_exists('criadoEm', $filtros)) {

            $from = $filtros['criadoEm']['from'] ? $filtros['criadoEm']['from']: null;
            $to = $filtros['criadoEm']['to'] ? $filtros['criadoEm']['to'] : null;

            if((null != $from) && (null != $to)) {

               $qb->field('criadaEm')->gte($from);
               $to->add(new \DateInterval('PT23H59M')); // Adicionar 23:59
               $qb->field('criadaEm')->lte($to);
            }

            if((null != $from) && (null == $to))
                $qb->field('criadaEm')->gte($from);

            if((null == $from) && (null != $to)){
                $to->add(new \DateInterval('PT23H59M')); // Adicionar 23:59
                $qb->field('criadaEm')->lte($to);
            }
        }

        return $executar === true ? $qb->execute() : $qb;
    }


}