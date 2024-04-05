<?php

namespace FlightAuth;


use Flight;


class AuthAbstract {

    protected $folder = 'views';
    protected $protected_routes = [];
    protected $protected_admin_routes = [];

    protected $db;

    protected $auth;

    public function __construct()
    {
    }

    /**
     * @return array
     */
    public function getProtectedRoutes(): array
    {
        return $this->protected_routes;
    }

    /**
     * @param array $protected_routes
     * @return $this
     */
    public function setProtectedRoutes(array $protected_routes): AuthAbstract
    {
        $this->protected_routes = $protected_routes;
        return $this;
    }

    /**
     * @return array
     */
    public function getProtectedAdminRoutes(): array
    {
        return $this->protected_admin_routes;
    }

    /**
     * @param array $protected_admin_routes
     * @return AuthAbstract
     */

    /***
     * @param array $protected_admin_routes
     * @return $this
     */
    public function setProtectedAdminRoutes(array $protected_admin_routes): AuthAbstract
    {
        $this->protected_admin_routes = $protected_admin_routes;
        return $this;
    }

    /**
     * @return mixed
     */
    public function getFolder()
    {
        return $this->folder;
    }

    /***
     * @param $folder
     * @return $this
     */
    public function setFolder($folder)
    {
        $this->folder = $folder;
        return $this;
    }

    /**
     * @param string $relativePath
     * @return string
     */
    protected function getViewFile(string $relativePath)
    {
        $views = getcwd().'/views/';
        $partials = getcwd().'/views/partials/';

        if ( glob($views.$relativePath.".*") ) {
            return $views.$relativePath;
        } else if ( glob($partials.$relativePath.".*") ) {
            return $partials.$relativePath;
        }
        return $this->getViewsFolder().$relativePath;
    }

    /**
     * @return string
     */
    protected function getViewsFolder()
    {
        return dirname(__FILE__) . '/'.$this->getFolder().'/';
    }

    /**
     * @return mixed
     */
    public function getAuth()
    {
        return $this->auth;
    }

    /**
     * @param mixed $auth
     * @return Auth
     */
    public function setAuth($auth)
    {
        $this->auth = $auth;
        return $this;
    }

    /**
     * @return mixed
     */
    public function getDb()
    {
        return $this->db;
    }

    /**
     * @param mixed $db
     * @return Auth
     */
    public function setDb( \PDO $db )
    {
        $this->db = $db;
        return $this;
    }

}