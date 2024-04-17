<?php

namespace FlightAuth\Factory;

use Flight;

class User extends UserAbstract {

    public function __construct($db)
    {
        parent::__construct();
        $this->setDb($db);
        $this->scaffold();
    }

    protected function scaffold()
    {
        /***
         *
         * user based pages
         *
         */
        Flight::route('GET /user/list', function () {
            $usermanager = Flight::get('usermanager');
            $records = $usermanager->getAll();
            $this->render('user-list', [
                'users'=>$records
            ], 'content');
            $this->render('layout');
        });

        Flight::route('GET /user/login-as/@id', function ($id) {
            $usermanager = Flight::get('usermanager');
            $auth = Flight::get('auth');
            $auth->getAuth()->admin()->loginAsUserById($id);
            Flight::redirect('/');
        });

    }

    public function getById($id)
    {
        $tablename = $this->getTablename('users');
        $records = $this->getDb()->findOne($tablename, 'id=:userid',[':userid'=>$id]);
        return $records;
    }

    public function getAll()
    {
        $tablename = $this->getTablename('users');
        $records = $this->getDb()->find($tablename);
        return $records;

    }



}