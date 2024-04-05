<?php

namespace FlightAuth\Helpers;

/**
 * Class Session
 *
 * Seeks to help manage sessions by key.
 * Perfect for flash messages that get disposed after single-use.
 */
class Session {

    const SUCCESS = 'success';
    const INFO = 'info';
    const WARNING = 'warning';
    const ERROR = 'error';

    public $sessionKey = '_flashdata';

    /**
     * Session constructor.
     *
     * @param null $sessionKey
     */
    public function __construct($sessionKey=NULL)
    {
        if ( !empty($sessionKey) ) {
            $this->setSessionKey($sessionKey);
        }
        if ( !array_key_exists($this->getSessionKey(), $_SESSION) ) {
            $_SESSION[$this->getSessionKey()] = [];
        }
    }

    /**
     * Get all flash messages under set session key
     *
     * @return mixed Session
     */
    public function get(){
        return $_SESSION[$this->getSessionKey()];
    }

    /**
     * Get flash messages by type
     *
     * @param $type
     * @return array
     */
    public function getByType($type)
    {
        if ( !empty($_SESSION[$this->getSessionKey()][$type]) ) {
            return $_SESSION[$this->getSessionKey()][$type];
        }
        return [];
    }


    /**
     * Add flash message by type
     *
     * @param $type
     * @param $value
     * @return Session
     */
    public function add($type, $value){
        if ( empty($_SESSION[$this->getSessionKey()][$type]) ) {
            $_SESSION[$this->getSessionKey()][$type] = [];
        }
        $_SESSION[$this->getSessionKey()][$type][] = $value;
    }

    /**
     * Get session value by type
     *
     * @param $type
     * @return mixed
     */
    public function getValue($type){
        return $_SESSION[$this->getSessionKey()][$type];
    }


    /**
     * Add non-array specific value by type
     *
     * @param $type
     * @param $value
     * @return Session
     */
    public function addLiteral($type, $value){
        $_SESSION[$this->getSessionKey()][$type] = $value;
    }

    /**
     * Add non-array specific value by type
     *
     * @param $type
     * @param $value
     * @return Session
     */
    public function deleteLiteral($type){
        unset($_SESSION[$this->getSessionKey()][$type]);
    }


    /**
     * Clear stored flash messages by type (eg. INFO, WARNING, ERROR, SUCCESS)
     *
     * @param $type
     * @ Session
     */
    public function clearByType($type){
        if ( array_key_exists($type, $_SESSION[$this->getSessionKey()]) ) {
            $_SESSION[$this->getSessionKey()][$type] = [];
        }

    }

    /**
     * Clear all stored flash messages
     *
     * @return Session
     */
    public function clearAll(){
        foreach ( $this->getTypes() as $type ) {
            $this->clearByType($type);
        }

    }

    /**
     * Get session key to store flash messages
     *
     * @return string
     */
    public function setSessionKey($sessionKey)
    {
        return $this->sessionKey = $sessionKey;
    }

    /**
     * Get session key to store flash messages
     *
     * @return string
     */
    public function getSessionKey()
    {
        return $this->sessionKey;
    }

    /**
     * Add success
     *
     * @param $value
     * @
     */
    public function addSuccess($value)
    {
        $type = self::SUCCESS;
        $this->add($type, $value);
    }

    /**
     * Add info
     *
     * @param $value
     * @
     */
    public function addInfo($value)
    {
        $type = self::INFO;
        $this->add($type, $value);
    }

    /**
     * Add warning
     *
     * @param $value
     * @
     */
    public function addWarning($value)
    {
        $type = self::WARNING;
        $this->add($type, $value);

    }

    /**
     * Add error
     *
     * @param $value
     * @
     */
    public function addError($value)
    {
        $type = self::ERROR;
        $this->add($type, $value);

    }

    /**
     * Get list (array) of all flash message types
     *
     * @return array of session types (key arrays to be set)
     */
    public function getTypes()
    {
        return [
            self::SUCCESS,
            self::INFO,
            self::WARNING,
            self::ERROR,
        ];
    }

}
