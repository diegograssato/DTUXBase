<?php
/**
 * Created by PhpStorm.
 * User: Diego Pereira Grassato
 * Date: 31/10/13
 * Time: 16:43
 */

namespace DTUXBase\Document;
use Doctrine\ODM\MongoDB\Mapping\Annotations as ODM;

/** @ODM\EmbeddedDocument */
class Coordinates {
    /** @ODM\Float */
    public $latitude;

    /** @ODM\Float */
    public $longitude;

} 