<?php
/**
 * Classe responsável por centralizar os logs do sistema
 * @todo Classe responsável por centralizar os logs do sistema
 * @category DTUXBase
 * @package DTUXBase
 * @subpackage Logger
 * @author Diego Pereira Grassato <diego.grassato@gmail.com>
 * @data 11/11/13 15:16
 */

namespace DTUXBase\Document;

use DTUXBase\Document\AbstractDocument as AbstractDocument;
use Doctrine\ODM\MongoDB\Mapping\Annotations as ODM;
/**
 * Logger
 *
 * @ODM\Document(
 *     collection="Logger"
 * )
 */
class Logger extends AbstractDocument{
    /**
     * @ODM\String
     */
    private $message;

    /**
     * @ODM\String
     */
    private $controller;

    /**
     * @ODM\String
     */
    private $action;


    /**
     * @ODM\String
     */
    private $module;


    /**
     * @ODM\String
     */
    private $level;

    /**
     * @ODM\String
     */
    private $level_name;

    /**
     * @ODM\String
     */
    private $channel;

    /**
     * @ODM\String
     */
    private $extra;

    /**
     * @ODM\String
     */
    private $ip;

    /**
     * @param mixed $ip
     */
    public function setIp($ip)
    {
        $this->ip = $ip;
    }

    /**
     * @return mixed
     */
    public function getIp()
    {
        return $this->ip;
    }



    /**
     * @param mixed $action
     */
    public function setAction($action)
    {
        $this->action = $action;
    }

    /**
     * @return mixed
     */
    public function getAction()
    {
        return $this->action;
    }

    /**
     * @param mixed $channel
     */
    public function setChannel($channel)
    {
        $this->channel = $channel;
    }

    /**
     * @return mixed
     */
    public function getChannel()
    {
        return $this->channel;
    }

    /**
     * @param mixed $controller
     */
    public function setController($controller)
    {
        $this->controller = $controller;
    }

    /**
     * @return mixed
     */
    public function getController()
    {
        return $this->controller;
    }

    /**
     * @param mixed $extra
     */
    public function setExtra($extra)
    {
        $this->extra = $extra;
    }

    /**
     * @return mixed
     */
    public function getExtra()
    {
        return $this->extra;
    }

    /**
     * @param mixed $level
     */
    public function setLevel($level)
    {
        $this->level = $level;
    }

    /**
     * @return mixed
     */
    public function getLevel()
    {
        return $this->level;
    }

    /**
     * @param mixed $level_name
     */
    public function setLevelName($level_name)
    {
        $this->level_name = $level_name;
    }

    /**
     * @return mixed
     */
    public function getLevelName()
    {
        return $this->level_name;
    }

    /**
     * @param mixed $message
     */
    public function setMessage($message)
    {
        $this->message = $message;
    }

    /**
     * @return mixed
     */
    public function getMessage()
    {
        return $this->message;
    }

    /**
     * @param mixed $module
     */
    public function setModule($module)
    {
        $this->module = $module;
    }

    /**
     * @return mixed
     */
    public function getModule()
    {
        return $this->module;
    }






} 