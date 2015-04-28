<?php namespace App\Controllers;

use App\Models\User;

class AuthController extends \Controller {

    /**
     * Method for define data array : token, session.
     */
    public function data()
    {
        return (empty(\Session::flash())) ? array('token' => \Token::getToken()) : array_merge(\Session::flash(), array('token' => \Token::getToken()));
    }

    /**
     * Method for get view login page.
     */
    public function getLogins()
    {
        if(!\Sentry::check()) {
            return $this->render('auth/login.html', $this->data());
        }else {
            return $this->redirect('admin/home');
        }
    }

    public function postLogins()
    {
        if(\Token::validate() && $this->validation('login')) {
            try {
                $credentials = array(
                        'email'     => \Input::post('email'),
                        'password'  => \Input::post('password')
                );
                $remember = (bool) \Input::post('remember', true);
                $user = \Sentry::authenticate($credentials, $remember);
                return $this->redirect('admin/home', \Sentry::getUser());

            } catch(\Exception $e) {
                $this->app->flash('error', \SentryError::errors(get_class($e)));
                return $this->redirect('login');
            }
        } else {
            $this->app->flash('errors', \Validate::$errors);
            return $this->redirect('login');
        }
    }

    public function getLogouts()
    {
        \Sentry::logout();
        return $this->redirect('login');
    }

    public function getRegisters()
    {
        return $this->render('auth/register.html', $this->data());
    }

    public function postRegisters()
    {
        if(\Token::validate() && $this->validation('register')) {
            $user = \Sentry::register(array(
                'email'         => \Input::post('email'),
                'password'      => \Input::post('password'),
                'first_name'    => \Input::post('first_name'),
                'last_name'     => \Input::post('last_name')
            ));

            $activationCode['activation'] = $user->getActivationCode();
            $results = $this->sendEmail('activation', $activationCode);

            if($results){
                $this->app->flash('success', 'Thanks for registering. Check email for account activation.');
                return $this->app->redirect('register');
            }else{
                $this->app->flash('errors', "Can't send on your email.");
                return $this->app->redirect('register');
            }
        }else {
            $this->app->flash('errors', \Validate::$errors);
            return $this->app->redirect('register');
        }
    }

    public function getActivations($code)
    {
        try {
            $user = \Sentry::findUserByActivationCode($code);

            if ($user->attemptActivation($code)) {
                $this->app->flash('success', 'Your account is active and then login.');
                return $this->app->redirect('../login');
            } else {
                $this->app->flash('errors', 'Your account is not active or you forgot password.');
                return $this->app->redirect('../login');
            }
        } catch(\Exception $e) {
            $this->app->flash('error', \SentryError::errors(get_class($e)));
            return $this->redirect('../login');
        }
    }

    public function getResetPasswords()
    {
        return $this->render('auth/reset.html', $this->data());
    }

    public function postResetPasswords()
    {
        if(\Token::validate() && $this->validation('reset')) {
            try {
                $user = \Sentry::findUserByLogin(\Input::post('email'));
                $resetCode['resetCode'] = $user->getResetPasswordCode();
                $results = $this->sendEmail('reset', $resetCode);

                if($results){
                    $this->app->flash('success', 'Check email for get resetting password your account.');
                    return $this->app->redirect('reset');
                }else{
                    $this->app->flash('errors', "Can't send on your email.");
                    return $this->app->redirect('reset');
                }
            } catch (\Exception $e) {
                $this->app->flash('error', \SentryError::errors(get_class($e)));
                return $this->app->redirect('reset');
            }
        }else {
            $this->app->flash('errors', \Validate::$errors);
            return $this->app->redirect('reset');
        }
    }

    public function getResetPasswordCodes($code)
    {
        try {
            $user = \Sentry::findUserByResetPasswordCode($code);
            if ($user->checkResetPasswordCode($code)) {
                $data = array_merge($this->data(), array('code' => $code));
                return $this->render('auth/password.html', $data);
            }else {
                $this->app->flash('error', 'Your account is not active or you forgot password.');
                return $this->app->redirect('../reset');
            }
        } catch(\Exception $e) {
            $this->app->flash('error', \SentryError::errors(get_class($e)));
            return $this->redirect('../reset');
        }
    }

    public function postResetPasswordCodes($code)
    {
        if($this->validation('password')) {
            $user = \Sentry::findUserByResetPasswordCode($code);
            if ($user->attemptResetPassword($code, \Input::post('password'))) {
                $this->app->flash('success', 'Your password account have changing.');
                return $this->app->redirect('../login');
            } else {
                $this->app->flash('error', "Your password account can't changing.");
                return $this->app->redirect($code);
            }
        }else {
            $this->app->flash('errors', \Validate::$errors);
            return $this->redirect($code);
        }
    }

    private function sendEmail($type, $data)
    {
        $email = $this->app->view->fetch('emails/'.$type.'.html', $data);

        $message = \Swift_Message::newInstance((($type == 'reset') ? 'Password Reset Password' : 'Activation Code'))
                        ->setFrom(array('myblck91@gmail.com' => 'Administrator'))
                        ->setTo(array(\Input::post('email') => \Input::post('email')))
                        ->setBody($email, 'text/html');

        return $this->app->mailer->send($message);
    }

    private function validation($method)
    {
        $reset_rules    = [ 'email' => (($method == 'register') ? 'required|email|unique:users' : 'required|email') ];
        $login_rules    = array_merge($reset_rules, ['password' => 'required']);
        $password_rules = [
            'password' => 'required',
            'password_confirmation' => 'required|same:password'
        ];
        $register_rules = array_merge($login_rules , [
            'first_name' => 'required',
            'last_name' => 'required',
            'password_confirmation' => 'required|same:password'
        ]);

        switch ($method) {
            case 'reset':
                return \Validate::make(\Input::post(), $reset_rules);
                break;
            case 'password':
                return \Validate::make(\Input::post(), $password_rules);
                break;
            case 'login':
                return \Validate::make(\Input::post(), $login_rules);
                break;
            case 'register':
                return \Validate::make(\Input::post(), $register_rules);
                break;
            default:
                return false;
                break;
        }
    }
}