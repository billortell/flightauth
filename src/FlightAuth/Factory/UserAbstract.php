<?php

namespace FlightAuth\Factory;

use Flight;

class UserAbstract {

    private $table_prefix;

    private $db;

    protected $folder = '../views';

    protected function __construct()
    {
        $this->table_prefix = $_ENV['DB_TABLE_AUTH_PREFIX'];

    }

    protected function getDb()
    {
        return $this->db;
    }

    protected function setDb($db)
    {
        $this->db = $db;
        return $this;
    }

    protected function getTablePrefix()
    {
        return $this->table_prefix;
    }

    protected function getTablename($table=null)
    {
        if ( empty($table) ) {
            throw new \Exception('Table name not specified.');
        }
        return $this->getTablePrefix().$table;
    }

    /***
     * Overriding Flight's rendering functionality to
     * make use of getViewFile() to get local/overridden template.
     *
     * @param $template
     * @param $params
     * @param $output
     * @return void
     */
    public function render($template, $params=[], $output=null)
    {
        $template_path = $this->getViewFile($template);

        // render using Flight's rendering engine
        Flight::render($template_path, $params, $output);
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
    public function getFolder()
    {
        return $this->folder;
    }


}