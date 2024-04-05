<?php

namespace FlightAuth\Middleware;

use Flight;

class AuthMiddleware {

    protected $csrf_token_name = 'csrf_token';

    public function __construct()
    {

    }

    /**
     * @return string
     */
    public function getCsrfTokenName(): string
    {
        return $this->csrf_token_name;
    }

    /**
     * @param string $csrf_token_name
     * @return AuthMiddleware
     */
    public function setCsrfTokenName(string $csrf_token_name): AuthMiddleware
    {
        $this->csrf_token_name = $csrf_token_name;
        return $this;
    }

    /***
     * Before route is processed, this will insure if the csrf used is valid
     * and we'll halt or allow them accordingly.
     *
     * @param array $params
     * @return void
     */
    public function before(array $params): void
    {
        $request = Flight::request();
        $request_data = (object)$request->data->getData();

        if(Flight::request()->method == 'POST') {

            $session_csrf_token = Flight::session()->getValue($this->getCsrfTokenName());

            // clear it so it can be reset
            Flight::session()->deleteLiteral('csrf_token');

            if( $request_data->csrf_token !== $session_csrf_token ) {
                if ( Flight::request()->ajax ) {
                    Flight::halt(403, 'Invalid CSRF token');
                    exit();
                }
                Flight::notFound();
            }

        }
    }

}