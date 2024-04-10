<?php

namespace FlightAuth;

use Flight;
use FlightAuth\Exceptions\AuthUserDetailsException;
use FlightAuth\Exceptions\AuthUserNotExistsException;
use FlightAuth\Middleware\AuthMiddleware;
use FlightAuth\Events\EventChangeEmailSuccess;
use FlightAuth\Events\EventManager;
use PDO;

class Auth extends AuthAbstract
{
    const SESSION_FLASH_REDIRECT = '_flashredirect';
    const SESSION_RESET_PASSWORD = '_reset_password';
    const SESSION_VERIFY_EMAIL = '_verify_email';
    const SESSION_CHANGE_EMAIL = '_change_email';

    protected $eventManager;

    public function __construct( PDO $db )
    {
        $this->setDb($db);
        $this->setAuthInstance($db);
        $this->scaffold();
    }

    public function getDb()
    {
        return $this->db;
    }

    public function setDb( PDO $db )
    {
        $this->db = $db;
        return $this;
    }

    public function setAuthInstance($db)
    {
        $db = $this->getDb();
        $auth = new \Delight\Auth\Auth($db, null, $_ENV['DB_TABLE_AUTH_PREFIX'], FALSE, 15);
        $this->setAuth($auth);
    }

    public function setEventManager(EventManager $eventManager)
    {
        $this->eventManager = $eventManager;
    }

    public function getEventManager()
    {
        return $this->eventManager;
    }

    public function scaffold()
    {
        // this event manager will notify different events (observers)
        // one can add to this at any time
//        Flight::register('eventManager', '\FlightAuth\Events\EventManager');
        $this->setEventManager((new EventManager()));


        /***
         * Session management throughout the application
         * -----------------------------------------------------------------
         * Uses normal $_SESSION (in php) but manages the array within it
         * Use this for .css, .js and _flash types
         */
        Flight::register('session','\FlightAuth\Helpers\Session',['_normal']);
        Flight::register('sessionFlash','\FlightAuth\Helpers\Session',['_flashdata']);
        Flight::register('sessionPage','\FlightAuth\Helpers\Session',['_flashpage']);


        //----------------------------
        //
        // All authentication-based routes/processes
        //
        //----------------------------
        Flight::route('GET /change-email', function () {
            $this->isLoggedInRedirect();
            $this->render('change-email', [], 'content');
            $this->render('layout');
        });

        Flight::route('POST /change-email', function () {
            $this->isLoggedInRedirect();
            $auth = $this->getAuth();
            try {
                /** @todo - add password confirmation before changing email */
                try {
                    $auth->changeEmail($_POST['email'], [$this, 'changeEmailConfirmation']);
                    $_SESSION[self::SESSION_VERIFY_EMAIL] = true;
                    Flight::redirect('/change-email-verify');
                    exit();
                } catch ( \Exception $e ) {
                    Flight::sessionFlash()->addError('We were not able to send verification email.');
                    Flight::sessionFlash()->addError($e->getMessage());
                }

                Flight::redirect('/change-email');
                exit();
            } catch (\Delight\Auth\InvalidEmailException $e) {
                Flight::sessionFlash()->addError('Invalid email address.');
                Flight::redirect('/change-email');
                exit();
            } catch (\Delight\Auth\UserAlreadyExistsException $e) {
                Flight::sessionFlash()->addError('Email address already exists.');
                Flight::redirect('/change-email');
                exit();
            } catch (\Delight\Auth\EmailNotVerifiedException $e) {
                Flight::sessionFlash()->addError('Account not verified.');
                Flight::redirect('/change-email');
                exit();
            } catch (\Delight\Auth\NotLoggedInException $e) {
                Flight::sessionFlash()->addError('Not logged in.');
                Flight::redirect('/change-email');
                exit();
            } catch (\Delight\Auth\TooManyRequestsException $e) {
                Flight::sessionFlash()->addError('Too many requests');
                Flight::redirect('/change-email');
                exit();
            } catch ( \Exception $e ) {
                Flight::sessionFlash()->addError($e->getMessage());
                Flight::redirect('/change-email');
                exit();
            }
        })->addMiddleware([ new AuthMiddleware() ]);

        Flight::route('GET /register', function () {
            $this->render('register', [], 'content');
            $this->render('layout');
        });

        /***
         * This is used for both
         *
         * Admin - allowing them to choose whether to override email verification
         * Non-Admin - allowing them register and require email verification (based upon REQUIRE_EMAIL_VERIFICATION)
         */
        Flight::route('POST /register', function () {
            $auth = $this->getAuth();
            $request = Flight::request();
            $request_data = (object)$request->data->getData();

            // default set at app level
            $require_email_validation = REQUIRE_EMAIL_VERIFICATION;
            if ( $this->isAdmin() ) {
                // enable override on registration
                $require_email_validation = (bool)$request_data->require_email_verification;
            }

            try {
                /***
                 * verification actually needed?
                 *
                 */
                $callback = null;
                if (REQUIRE_EMAIL_VERIFICATION) {
                    $callback = [$this, 'registrationConfirmationEmail'];
                }

                /***
                 * unique username needed?
                 * not necessary but may want if you're using usernames :)
                 */
                // @todo - add a 'uniqueUsernameChecker' that includes case-insensitive/sensitive
                if (!REQUIRE_UNIQUE_USERNAME) {
                    $user_id = $auth->register($_POST['email'], $_POST['password'], $_POST['username'], $callback);
                } else {
                    $user_id = $auth->registerWithUniqueUsername($_POST['email'], $_POST['password'], $_POST['username'], $callback);
                }

                Flight::redirect('/register-verify');
                exit();
            } catch (\Delight\Auth\InvalidEmailException $e) {
                Flight::sessionFlash()->addError('Invalid email address.');
                Flight::redirect('/register');
                exit();
            } catch (\Delight\Auth\InvalidPasswordException $e) {
                Flight::sessionFlash()->addError('Invalid password.');
                Flight::redirect('/register');
                exit();
            } catch (\Delight\Auth\UserAlreadyExistsException $e) {
                Flight::sessionFlash()->addError('Email address already exists.');
                Flight::redirect('/register');
                exit();
            } catch (\Delight\Auth\DuplicateUsernameException $e) {
                Flight::sessionFlash()->addError('Username already exists.');
                Flight::redirect('/register');
                exit();
            } catch (\Delight\Auth\TooManyRequestsException $e) {
                Flight::sessionFlash()->addError('Too many requests.');
                Flight::redirect('/register');
                exit();
            }
        })->addMiddleware([ new AuthMiddleware() ]);

        Flight::route('GET /register-verify', function () {
            $this->render('register-verify', [], 'content');
            $this->render('layout');
        });

        Flight::route('GET /change-email-verify', function () {
            // put in to keep people out that shouldn't be here :)
            if (empty($_SESSION[self::SESSION_VERIFY_EMAIL])) {
                Flight::redirect('/oops');
                exit();
            }
            unset($_SESSION[self::SESSION_VERIFY_EMAIL]);
            $this->render('change-email-verify', [], 'content');
            $this->render('layout');
        });

        Flight::route('GET /verification-success', function () {
            // put in to keep people out that shouldn't be here :)
            if (empty($_SESSION[self::SESSION_VERIFY_EMAIL])) {
                Flight::redirect('/oops');
                exit();
            }
            unset($_SESSION[self::SESSION_VERIFY_EMAIL]);
            $this->render('verification-success', [], 'content');
            $this->render('layout');
        });

        Flight::route('GET /verification-failed', function () {
            // put in to keep people out that shouldn't be here :)
            if (empty($_SESSION[self::SESSION_VERIFY_EMAIL])) {
                Flight::redirect('/oops');
                exit();
            }
            unset($_SESSION[self::SESSION_VERIFY_EMAIL]);
            $this->render('verification-failed', [], 'content');
            $this->render('layout');
        });

        Flight::route('GET /verify-email', function () {
            $auth = $this->getAuth();
            if ($_GET['selector'] || $_GET['token']) {
                $_SESSION[self::SESSION_VERIFY_EMAIL] = true;
                try {
                    $auth->confirmEmail($_GET['selector'], $_GET['token']);
                    Flight::redirect('/verification-success');
                    exit();
                } catch (\Delight\Auth\InvalidSelectorTokenPairException $e) {
                    Flight::sessionFlash()->addError('Invalid token');
                    Flight::redirect('/verification-failed');
                    exit();
                } catch (\Delight\Auth\TokenExpiredException $e) {
                    Flight::sessionFlash()->addError('Token expired');
                    Flight::redirect('/verification-failed');
                    exit();
                } catch (\Delight\Auth\UserAlreadyExistsException $e) {
                    Flight::sessionFlash()->addError('Email address already exists');
                    Flight::redirect('/verification-failed');
                    exit();
                } catch (\Delight\Auth\TooManyRequestsException $e) {
                    Flight::sessionFlash()->addError('Too many requests');
                    Flight::redirect('/verification-failed');
                    exit();
                }
            }
            $this->render('verification-waiting', [], 'content');
            $this->render('layout');
        });

        Flight::route('GET /verification-resend', function () {
            $this->render('verification-resend', [], 'content');
            $this->render('layout');
        });

        Flight::route('POST /verification-resend', function () {
            $auth = $this->getAuth();
            try {
                $callback = [$this, 'verificationEmailResend'];
                $auth->resendConfirmationForEmail($_POST['email'], $callback);
                Flight::sessionFlash()->addSuccess('Verification email re-sent.');
                $this->render('verification-waiting', [], 'content');
                $this->render('layout');
            } catch (\Delight\Auth\ConfirmationRequestNotFound $e) {
                Flight::sessionFlash()->addWarning('No earlier request found that could be re-sent.');
                Flight::redirect('/verification-resend');
                exit();
            } catch (\Delight\Auth\TooManyRequestsException $e) {
                Flight::sessionFlash()->addWarning('There have been too many requests -- try again later.');
                Flight::redirect('/verification-resend');
                exit();
            } catch ( \Exception $e ) {
                Flight::sessionFlash()->addError('Verification email <b>not</b> re-sent.');
                Flight::sessionFlash()->addError($e->getMessage());
            }
        })->addMiddleware([ new AuthMiddleware() ]);

        Flight::route('GET /change-password', function () {
            $this->isLoggedInRedirect();
            $this->render('change-password', [], 'content');
            $this->render('layout');
        });

        Flight::route('POST /change-password', function () {
            $this->isLoggedInRedirect();
            $auth = $this->getAuth();
            try {
                // check current (old) password
                if ( !$auth->reconfirmPassword($_POST['old_password']) ) {
                    Flight::sessionFlash()->addError("Old password incorrect.");
                }
                // check new password
                $errors = $this->validatePassword($_POST['new_password']);
                if (!empty($errors)) {
                    foreach ( $errors as $error ) {
                        Flight::sessionFlash()->addError($error);
                    }
                    Flight::redirect('/change-password');
                    exit();
                } else {
                    $auth->changePassword($_POST['old_password'], $_POST['new_password']);

                    Flight::sessionFlash()->addSuccess('You have successfully updated your password.');
                    Flight::sessionFlash()->addSuccess('We have sent you an email for your records.');

                    // fire off an observer notify
                    $this->changePasswordSuccess($auth->getEmail());

                    $_SESSION[self::SESSION_RESET_PASSWORD] = true;
                    Flight::redirect('/change-password-success');
                    exit();
                }
            } catch (\Delight\Auth\NotLoggedInException $e) {
                Flight::sessionFlash()->addError('Not logged in.');
                Flight::redirect('/change-password');
                exit();
            } catch (\Delight\Auth\InvalidPasswordException $e) {
                Flight::sessionFlash()->addError('Invalid password(s).');
                Flight::redirect('/change-password');
                exit();
            } catch (\Delight\Auth\TooManyRequestsException $e) {
                Flight::sessionFlash()->addWarning('Too many requests.');
                Flight::redirect('/change-password');
                exit();
            }
        })->addMiddleware([ new AuthMiddleware() ]);


        Flight::route('GET /change-password-success', function () {
            // put in to keep people out that shouldn't be here :)
            if (empty($_SESSION[self::SESSION_RESET_PASSWORD])) {
                Flight::redirect('/oops');
                exit();
            }
            unset($_SESSION[self::SESSION_RESET_PASSWORD]);
            $this->render('reset-password-success', [], 'content');
            $this->render('layout');

        });

        Flight::route('GET /forgot-password', function () {
            if ( $this->getAuth()->isLoggedIn() ) {
                Flight::redirect(SITE_REL_URI.'/change-password');
            }
            $this->render('forgot-password', [], 'content');
            $this->render('layout');
        });

        Flight::route('POST /forgot-password', function () {
            $auth = $this->getAuth();
            try {
                /** @todo - check for hidden field parameter */
                /** if no param, then bounce them back */

//                Flight::pre($_POST['email']);
//                exit();

                $callback = [$this, 'forgotPasswordConfirmation'];
                $auth->forgotPassword($_POST['email'], $callback);
                Flight::redirect('/forgot-password-success');
                exit();
            } catch (\Delight\Auth\InvalidEmailException $e) {
                Flight::sessionFlash()->addError('Invalid email address');
                Flight::redirect('/forgot-password');
                exit();
            } catch (\Delight\Auth\EmailNotVerifiedException $e) {
                Flight::sessionFlash()->addError('Email not verified.');
                Flight::redirect('/forgot-password');
                exit();
            } catch (\Delight\Auth\ResetDisabledException $e) {
                Flight::sessionFlash()->addError('Password reset is disabled.');
                Flight::redirect('/forgot-password');
                exit();
            } catch (\Delight\Auth\TooManyRequestsException $e) {
                Flight::sessionFlash()->addWarning('Too many requests.');
                Flight::redirect('/forgot-password');
                exit();
            }
        })->addMiddleware([ new AuthMiddleware() ]);

        Flight::route('GET /forgot-password-success', function(){
            $this->render('forgot-password-success', [], 'content');
            $this->render('layout');
        });

        Flight::route('GET /reset-password-form', function () {
            $auth = $this->getAuth();
            // put in to keep people out that shouldn't be here :)
            if (empty($_SESSION[self::SESSION_RESET_PASSWORD])) {
                Flight::redirect('/oops');
                exit();
            }
            if (empty($_SESSION[self::SESSION_RESET_PASSWORD]['selector']) || empty($_SESSION[self::SESSION_RESET_PASSWORD]['token'])) {
                Flight::redirect('/oops');
                exit();
            }
            $this->render('reset-password', [], 'content');
            $this->render('layout');
        });

        Flight::route('POST /reset-password-form', function () {
            $auth = $this->getAuth();
            if (empty($_SESSION[self::SESSION_RESET_PASSWORD]['selector']) || empty($_SESSION[self::SESSION_RESET_PASSWORD]['token'])) {
                Flight::redirect('/oops');
                exit();
            }
            try {
                $errors = $this->validatePassword($_POST['password']);
                if (!empty($errors)) {
                    foreach ( $errors as $error ) :
                        Flight::sessionFlash()->addError($error);
                    endforeach;
                } else {
                    $user = $auth->resetPasswordAndSignIn($_POST['selector'], $_POST['token'], $_POST['password']);
                    unset($_SESSION[self::SESSION_RESET_PASSWORD]);
                    Flight::redirect('/reset-password-success');
                    exit();
                }
            } catch (\Delight\Auth\InvalidSelectorTokenPairException $e) {
                Flight::sessionFlash()->addError('Invalid token.');
            } catch (\Delight\Auth\TokenExpiredException $e) {
                Flight::sessionFlash()->addError('Token expired.');
            } catch (\Delight\Auth\ResetDisabledException $e) {
                Flight::sessionFlash()->addError('Password reset is disabled.');
            } catch (\Delight\Auth\InvalidPasswordException $e) {
                Flight::sessionFlash()->addError('Invalid password.');
            } catch (\Delight\Auth\TooManyRequestsException $e) {
                Flight::sessionFlash()->addWarning('Too many requests.');
            }
            $this->render('reset-password', [], 'content');
            $this->render('layout');
        })->addMiddleware([ new AuthMiddleware() ]);

        Flight::route('GET /reset-password-success', function () {
            $auth = $this->getAuth();
            $this->render('reset-password-success', [], 'content');
            $this->render('layout');
        });

        Flight::route('GET /reset-password', function () {
            $auth = $this->getAuth();
            if (empty($_GET['token']) || empty($_GET['selector'])) {
                Flight::sessionFlash()->addError("Your password reset link was not valid. Please re-try.");
                Flight::redirect('/forgot-password');
                exit();
            }
            try {
                if ($auth->canResetPassword($_GET['selector'], $_GET['token'])) {
                    // set session variable
                    // store selector
                    // store token
                    $_SESSION[self::SESSION_RESET_PASSWORD] = [
                        'selector' => $_GET['selector'],
                        'token' => $_GET['token']
                    ];
                    Flight::redirect('/reset-password-form');
                    exit();
                } else {
                    Flight::sessionFlash()->addInfo("Let's try this again.");
                    Flight::redirect('/forgot-password');
                    exit();
                }
            } catch (\Delight\Auth\InvalidSelectorTokenPairException $e) {
                Flight::sessionFlash()->addWarning("Invalid token.");
                Flight::redirect('/forgot-password');
                exit();
            } catch (\Delight\Auth\TokenExpiredException $e) {
                Flight::sessionFlash()->addWarning("Token expired.");
                Flight::redirect('/forgot-password');
                exit();
            } catch (\Delight\Auth\ResetDisabledException $e) {
                Flight::sessionFlash()->addWarning("Password reset is disabled.");
                Flight::redirect('/forgot-password');
                exit();
            } catch (\Delight\Auth\TooManyRequestsException $e) {
                Flight::sessionFlash()->addWarning("Too many requests.");
                Flight::redirect('/forgot-password');
                exit();
            }

        });

        /**
         * @OA\Get(
         *     path="/login",
         *     summary="Display login page",
         *     tags={"Authentication"},
         *     @OA\Response(response="200", description="Login page rendered successfully"),
         *     @OA\Response(response="404", description="Not found")
         * )
         */
        Flight::route('GET /login', function () {
            $this->render('login', [], 'content');
            $this->render('layout');
        });

        /**
         * @OA\Post(
         *     path="/login",
         *     summary="Login user",
         *     tags={"Authentication"},
         *     @OA\RequestBody(
         *         required=true,
         *         @OA\MediaType(
         *             mediaType="application/x-www-form-urlencoded",
         *             @OA\Schema(
         *                 type="object",
         *                 @OA\Property(property="email", type="string", description="User's email"),
         *                 @OA\Property(property="password", type="string", description="User's password")
         *             )
         *         )
         *     ),
         *     @OA\Response(response="302", description="Redirect to homepage"),
         *     @OA\Response(response="400", description="Invalid email or password"),
         *     @OA\Response(response="401", description="Email not verified"),
         *     @OA\Response(response="429", description="Too many requests"),
         *     @OA\Response(response="500", description="Internal Server Error")
         * )
         */
        Flight::route('POST /login', function () {
            $auth = $this->getAuth();
            if ( $auth->isLoggedIn() ) {
                $auth->logOutEverywhere();
            }
            try {
                $auth->login($_POST['email'], $_POST['password']);
                if ($_SESSION[self::SESSION_FLASH_REDIRECT]) {
                    $redirect_uri = $_SESSION[self::SESSION_FLASH_REDIRECT];
                    unset($_SESSION[self::SESSION_FLASH_REDIRECT]);
                    Flight::redirect($redirect_uri);
                    exit();
                }
                Flight::redirect('/');
                exit();
            } catch (\Delight\Auth\InvalidEmailException $e) {
                Flight::sessionFlash()->addError('Wrong email address.');
                Flight::redirect('/login');
                exit();
            } catch (\Delight\Auth\InvalidPasswordException $e) {
                Flight::sessionFlash()->addError('Wrong password.');
                Flight::redirect('/login');
                exit();
            } catch (\Delight\Auth\EmailNotVerifiedException $e) {
                Flight::sessionFlash()->addError('Email not verified.');
                Flight::redirect('/login');
                exit();
            } catch (\Delight\Auth\TooManyRequestsException $e) {
                Flight::sessionFlash()->addError('Too many requests.');
                Flight::redirect('/login');
                exit();
            }
        })->addMiddleware([ new AuthMiddleware() ]);


        /**
         * @OA\Post(
         *     path="/logout",
         *     summary="Logout the user",
         *     tags={"Authentication"},
         *     @OA\Response(response="200", description="Logout successful"),
         *     @OA\Response(response="401", description="Unauthorized")
         * )
         */
        Flight::route('/logout', function () {
            $auth = $this->getAuth();
            try {
                $auth->logOutEverywhere();
                Flight::redirect("/login");
            } catch (\Delight\Auth\NotLoggedInException $e) {
                Flight::sessionFlash()->addWarning('Not logged in.');
                Flight::redirect("/login");
            }
        });


        //----------------------------
        //
        // oops/not found pages.
        // adding *JUST* in case user hasn't already
        //
        //----------------------------
        Flight::route('/oops',function(){
            $this->render('not-found', [], 'content');
            $this->render('layout');
            exit();
        });

        // method called when flightphp cannot resolve route
        Flight::map('notFound', function() {
            $this->render('not-found', [], 'content');
            $this->render('layout');
            exit();
        });



//        /***
//         *
//         * Do some post-registration stuff
//         * Eg. give the person an actual 'role' :)
//         *
//         */
//        Flight::map('registerSuccess', function ($user_id) {
//            $auth = $this->getAuth();
//            try {
//                $auth->admin()->addRoleForUserById($user_id, \Delight\Auth\Role::CONSUMER);
//                // $auth->admin()->removeRoleForUserById($user_id, \Delight\Auth\Role::ADMIN);
//            } catch (\Delight\Auth\UnknownIdException $e) {
//                /** @todo - send admin emial if this fails */
//                /** @todo - or log it :) */
//                // mail()
//            }
//        });

//        /***
//         * Triggered upon successful submission of email to get password reset.
//         *
//         *
//         * add the 'use ($mailer)' to add the mailer class
//         * eventually move this into an observer class or Flight::map'd method in index.php file
//         */
//        Flight::map('forgotPasswordSuccess', function($selector, $token) {
//            $email = $_POST['email'];
//
//            $mgHelper = Flight::get('mailer');
//            $mgHelper->setupEmail();
//            $mgHelper->addAddress($email);
//            $mgHelper->setSubject('Please check your email...');
//
//            // content starts
//            ob_start();
//            $verification_link = SITE_DOMAIN_HTTP . SITE_REL_URI . '/reset-password?selector=' . \urlencode($selector) . '&token=' . \urlencode($token);
//            $this->render('email/email-reset-password', array('verification_link' => $verification_link));
//            $body = ob_get_clean();
//            // content ends
//
//            // send content to user in email
//            $mgHelper->setBody($body);
//            $response = $mgHelper->sendEmail();
//        });

//        /***
//         * Triggered upon successful changing of password, allowing site owner to send user email
//         *
//         * add the 'use ($mailer)' to add the mailer class
//         * eventually move this into an observer class or Flight::map'd method in index.php file
//         */
//        Flight::map('changePasswordSuccess', function() {
//            $auth = $this->getAuth();
//            $email = $auth->getEmail();
//
//            $mgHelper = Flight::get('mailer');
//            $mgHelper->setupEmail();
//            $mgHelper->addAddress($email);
//            $mgHelper->setSubject('Your password was reset');
//
//            // content starts
//            ob_start();
//            $this->render('email/email-reset-password-success');
//            $body = ob_get_clean();
//            // content ends
//
//            // send content to user in email
//            $mgHelper->setBody($body);
//            $response = $mgHelper->sendEmail();
//        });

        /***
         *
         * create email verification link
         * (uri - verify-email)
         *
         */
        Flight::map('createVerificationLink', function($selector, $token) {
            return SITE_DOMAIN_HTTP . SITE_REL_URI . '/verify-email?selector=' . \urlencode($selector) . '&token=' . \urlencode($token);
        });

        /***
         *
         * create password reset link
         * (uri - reset-password)
         *
         */
        Flight::map('createPasswordResetLink', function($selector, $token) {
            return SITE_DOMAIN_HTTP . SITE_REL_URI . '/reset-password?selector=' . \urlencode($selector) . '&token=' . \urlencode($token);
        });

//        Flight::map('changeEmailSuccess', function($selector, $token) {
//            $verification_link = Flight::createVerificationLink($selector, $token);
//            try {
//                $email = $_POST['email'];
//
//                $mailer = Flight::get('mailer');
//                $mailer->setupEmail();
//                $mailer->addAddress($email);
//                $mailer->setSubject('Please verify your email...');
//
//                // content starts
//                ob_start();
//                $this->render('email/email-verify-email', ['verification_link' => $verification_link]);
//                $body = ob_get_clean();
//                // content ends
//
//                // send content to user in email
//                $mailer->setBody($body);
//                $response = $mailer->sendEmail();
//
//                $_SESSION[self::SESSION_VERIFY_EMAIL] = true;
//                Flight::redirect('/change-email-verify');
//                exit();
//            } catch ( \Exception $e ) {
//                Flight::sessionFlash()->addError('We were not able to send verification email.');
//                Flight::sessionFlash()->addError($e->getMessage());
//            }
//        });


//        /***
//         * Triggered upon successful creation of selector, token for email specified
//         *
//         * add the 'use ($mailer)' to add the mailer class
//         * eventually move this into an observer class or Flight::map'd method in index.php file
//         */
//        Flight::map('resendConfirmationEmail', function($selector, $token) {
//
//            $email = $_POST['email'];
//
//            $mgHelper = Flight::get('mailer');
//            $mgHelper->setupEmail();
//            $mgHelper->addAddress($email);
//            $mgHelper->setSubject('[Re-send] Please verify your email...');
//
//            // content starts
//            ob_start();
//            $verification_link = SITE_DOMAIN_HTTP . SITE_REL_URI . '/verify-email?selector=' . \urlencode($selector) . '&token=' . \urlencode($token);
//            $this->render('email/email-verify-email', ['verification_link' => $verification_link]);
//            $body = ob_get_clean();
//            // content ends
//
//            $mgHelper->setBody($body);
//
//            try {
//                $response = $mgHelper->sendEmail();
//                Flight::sessionFlash()->addSuccess('Verification email re-sent.');
//            } catch ( \Exception $e ) {
//                Flight::sessionFlash()->addError('Verification email <b>not</b> re-sent.');
//                Flight::sessionFlash()->addError($e->getMessage());
//            }
//        });
//






        //----------------------------
        //
        // Install tables
        // @todo - fix this so that it grabs it from a prs-4 src/ foldder
        // @todo - make this a composerable module
        //
        //----------------------------
        Flight::route('GET /install-tables', function(){
            $this->isLoggedInRedirect();

            if ( !$this->isAdmin() ) {
                Flight::notFound();
                exit();
            }

            $auth = $this->getAuth();

            try {
                // get the file (by db type)
                $type = strtolower($_ENV['DB_TYPE']);
                $path = getcwd()."/../vendor/delight-im/auth/Database/".$type.".sql";
                $results = file_get_contents($path);

                // get create tables from .sql file
                $db_prefix = $_ENV['DB_TABLE_AUTH_PREFIX'];
                $results = str_replace("`users`","`{$db_prefix}users`", $results);
                $results = str_replace("`users_confirmations`","`{$db_prefix}users_confirmations`", $results);
                $results = str_replace("`users_remembered`","`{$db_prefix}users_remembered`", $results);
                $results = str_replace("`users_resets`","`{$db_prefix}users_resets`", $results);
                $results = str_replace("`users_throttling`","`{$db_prefix}users_throttling`", $results);

                try {
                    $db = $this->getDb();
                    $result = $db->exec($results);
                    $message = 'Database tables successfully installed.';
                } catch ( \Exception $e ) {
                    $message = "Seems tables were already installed.";
                }

            } catch ( \Exception $e ) {

                $message = "Tables were not installed.";
                $message = $e->getMessage();
            }
            $this->render('../admin/install-tables',[
                'title' => 'DB Installation',
                'content' => $message
            ],'content');
            $this->render('layout');
        });

    }


    /***
     * Finding the status value by status name
     *
     * \Delight\Auth\Status::NORMAL;
     * \Delight\Auth\Status::ARCHIVED;
     * \Delight\Auth\Status::BANNED;
     * \Delight\Auth\Status::LOCKED;
     * \Delight\Auth\Status::PENDING_REVIEW;
     * \Delight\Auth\Status::SUSPENDED;
     *
     * @param $statusByName
     * @return void
     */
    public function setUserStatus($statusByName=null)
    {
        $statusByNameUC = strtoupper($statusByName);
        $statusCode =( \Delight\Auth\Status::$statusByNameUC ) ?: false;
        if ( !empty($status) ) {
            // let's do some magic here :)
        }
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


    /***
     * Checks and returns if logged-in user has the Admin role.
     *
     * @return int
     */
    public function isAdmin()
    {
        $auth = $this->getAuth();
        return (int)$auth->hasAnyRole(
            \Delight\Auth\Role::ADMIN
        );
    }

    /***
     * Automatic check and redirect to 'oops' if logged-in user does not have the Admin role.
     *
     * @return void
     */
    public function onlyAdmins()
    {
        if ( !$this->isAdmin() ) {
            Flight::redirect('/oops');
        }
    }

    /***
     * Checks and returns if user is logged-in.
     *
     * @return bool
     */
    public function isLoggedIn()
    {
        $auth = $this->getAuth();
        return $auth->isLoggedIn();
    }


    /***
     * Check of user is logged in, if not, ask them them to login
     * and upon successful login [redirect] them to the initially requested path.
     *
     * @return void
     */
    public function isLoggedInRedirect()
    {
        // gets delight-im/php-auth library
        if (!$this->getAuth()->isLoggedIn()) {
            Flight::sessionFlash()->addError('You must be logged in to use this feature.');
            $_SESSION[self::SESSION_FLASH_REDIRECT] = $_SERVER['REQUEST_URI'];
            Flight::redirect("/login");
            exit();

        }
    }

    /***
     * Check session user id against id in parameter
     *
     * @param $user_id integer
     * @return bool
     */
    public function isSameuser($user_id)
    {
        $auth = $this->getAuth();
        return $user_id == $auth->getUserId();
    }


    /***
     * Check if user logged in is same as user parameter checked
     * or if the person is an admin (overriding the check). If not,
     * user is automatically redirected to the 'oops' page.
     *
     * @param $user_id
     * @return bool
     */
    public function isSameUserOrAdmin($user_id=null)
    {
        $auth = $this->getAuth();
        if (empty($user_id)){
            $user_id = $auth->getUserId();
        }
        if ( !$this->isAdmin() && !$this->isSameUser($user_id) ) {
            Flight::redirect('/oops');
        }
        return true;
    }


    /***
     * Check if user logged in is same as user parameter checked
     * or if the person is an admin (overriding the check).
     *
     * @param $user_id
     * @return bool
     */
    public function isSameUserOrAdminSimple($user_id=null)
    {
        $auth = $this->getAuth();
        if (empty($user_id)){
            $auth = $this->getAuth();
            $user_id = $auth->getUserId();
        }
        return $this->isAdmin() || $this->isSameUser($user_id);

    }


    /***
     * Method to validate the password and returns the various
     * errors in the password submitted.
     *
     * @param $password string
     * @return array
     */
    public function validatePassword($password=null)
    {
        if ( empty($password) ) {
            $errors[] = 'Password cannot be blank :).';
        }
        $errors = [];
        if (\strlen($password) < 8) {
            $errors[] = 'Must be at least 8 characters.';
        }
        $blacklist = ['password', '123456', 'qwerty'];
        if (\in_array($password, $blacklist)) {
            $errors[] = 'That particular password is not allowed.';
        }
        return $errors;
    }



    /***
     *
     * preparing for the observers!!
     *
     */

    /***
     * Callback required for changeEmail functionality in Delight library
     * Need callback to accept selector and token parameters.
     *
     * @param $selector
     * @param $token
     * @return void
     */
    public function changeEmailConfirmation($selector, $token)
    {
        $verification_link = Flight::createVerificationLink($selector, $token);
        $this->getEventManager()->notify('changeemailsuccess', ['verification_link'=>$verification_link]);
    }

    /***
     * Callback required for doing any post-registration activities
     * eg. raising/adding privilege, sending email
     *
     * @param $selector
     * @param $token
     * @return void
     */
    public function registrationConfirmationEmail($selector, $token)
    {
        $verification_link = Flight::createVerificationLink($selector, $token);
        $this->getEventManager()->notify('registrationconfirmation', ['verification_link'=>$verification_link]);
    }

    /***
     * Callback required for changeEmail functionality in Delight library
     * Need callback to accept selector and token parameters.
     *
     * @param $selector
     * @param $token
     * @return void
     */
    public function verificationEmailResend($selector, $token)
    {
        $verification_link = Flight::createVerificationLink($selector, $token);
        $this->getEventManager()->notify('verificationemailresend', ['verification_link'=>$verification_link]);
    }

    /***
     * Callback required for changeEmail functionality in Delight library
     * Need callback to accept selector and token parameters.
     *
     * @param $selector
     * @param $token
     * @return void
     */
    public function changePasswordSuccess($email)
    {
        $this->getEventManager()->notify('changepasswordsuccess', ['email'=>$email]);
    }

    /***
     * Callback for forgot password confirmation
     * whereby you should send a password reset link.
     *
     * @param $selector
     * @param $token
     * @return void
     */
    public function forgotPasswordConfirmation($selector, $token)
    {
        $reset_link = Flight::createPasswordResetLink($selector, $token);
        $this->getEventManager()->notify('forgotpasswordconfirmation', ['reset_link'=>$reset_link]);
//        Flight::eventManager()->notify('forgotpasswordconfirmation', ['reset_link'=>$reset_link]);
    }





    /***
     *
     *
     *
     * proifile static
     *
     *
     */
    static public function getOrm()
    {
        $orm = Flight::orm();
        return $orm;
    }

    static public function getAll()
    {
        $orm = self::getOrm();

        try {
            $records = self::getOrm()->find('userdetails');
            return $records;
        } catch ( \Exception $e ) {
            $message = "Unable to get all users' details.";
            throw new AuthUserDetailsException($message);
        }
    }

    static public function getByUserId($user_id)
    {

        $orm = Flight::orm();

        $record = $orm->findOne('userdetails', 'userid=:userid',[':userid'=>$user_id]);

        try {
            $record = self::getOrm()->findOne('userdetails','userid=:userid',[':userid'=>$user_id]);
            return $record;
        } catch ( \Exception $e ) {
            $message = "Details do not exists for this user (id: $user_id).";
            throw new AuthUserNotExistsException($message);
        }
    }

    static public function getFull() {
        try {
            $sql = "
            select u.*, ud.firstname, ud.lastname, ud.phone, ud.pic, ud.created, ud.updated
            from pa_users u
            left join userdetails ud on ud.userid = u.id
            ";
            $records = self::getOrm()->getAll($sql);
            return $records;
        } catch ( \Exception $e ) {
            $message = "Unable to get all users and details.";
            throw new AuthUserDetailsException($message);
        }
    }

    // \Delight\Auth\Role
    static public function getFullUsersByRole($bitwise_role) {
        try {
            $sql = "
            select u.*, ud.firstname, ud.lastname, ud.phone, ud.pic, ud.created, ud.updated
            from pa_users u
            left join userdetails ud on ud.userid = u.id
            where 1=1
              and roles_mask & :bitwise_role
            ";
            $records = self::getOrm()->getAll($sql, [':bitwise_role'=>$bitwise_role]);
            return $records;
        } catch ( \Exception $e ) {
            $message = "Unable to get all users and details.";
            throw new AuthUserDetailsException($message);
        }
    }

    static public function getFullByUserId($user_id) {
        try {
            $sql = "
            select u.*, ud.firstname, ud.lastname, ud.phone, ud.pic, ud.created, ud.updated
            from pa_users u
            left join userdetails ud on ud.userid = u.id
            where 1=1
              and u.id = :user_id
            ";
            $records = self::getOrm()->getAll($sql, [
                ':user_id'=>$user_id,
            ]);
            return $records;
        } catch ( \Exception $e ) {
            $message = "Unable to get event.";
            throw new AuthUserDetailsException($message);
        }
    }

}