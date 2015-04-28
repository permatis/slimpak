<?php namespace App\Controllers;

use App\Models\User;

class UserController extends \Controller {

	/**
	 * Display a listing of the resource.
	 * @return Response & Session Flash Message
	 */
	public function indexs()
	{
		$data = (empty(\Session::flash())) ? array('users' => User::all(), 'sentry' => \Sentry::getUser()) : array_merge(\Session::flash(), array('users' => User::all(), 'sentry' => \Sentry::getUser()));

		return $this->render('admin/users/index.html', $data);
	}

	/**
	 * Show the form for creating a new resource.
	 * @return Response & Tokenuser
	 */
	public function creates()
	{
		$data = (empty(\Session::flash())) ? array('token' => \Token::getToken(), 'sentry' => \Sentry::getUser()) : array_merge(\Session::flash(), array('token' => \Token::getToken(), 'sentry' => \Sentry::getUser()));

		return $this->render('admin/users/create.html', $data);
	}

	/**
	 * Store a newly created resource in storage and add new session flash message.
	 * @return Response
	 */
	public function stores()
	{
		if(\Token::validate() && $this->validation('create')){
			try {
				$input = [
					'first_name'	=> \Input::post('first_name'),
					'last_name' 	=> \Input::post('last_name'),
					'email' 		=> \Input::post('email'),
					'password' 		=> \Input::post('password'),
					'activated'		=> true
				];

				$user = \Sentry::createUser($input);

				if($user) {
					$this->app->flash('success', 'Success created user.');
					return $this->redirect('users');
				}
			}catch (\Exception $e) {
				$this->app->flash('error', \SentryError::errors(get_class($e)));
            	return $this->redirect('users');
			}
		}else{
			$this->app->flash('errors', \Validate::$errors);
			return $this->redirect('users/create');
		}
	}

	/**
	 * Show the form for editing the specified resource.
	 * @param  int  $id
	 * @return Response & Token
	 */
	public function edits($id)
	{
		$data = (empty(\Session::flash())) ? array('token' => \Token::getToken(), 'user' => User::FindOrFail($id), 'sentry' => \Sentry::getUser())
				: array_merge(\Session::flash(), array('token' => \Token::getToken(), 'user' => User::FindOrFail($id), 'sentry' => \Sentry::getUser()));

		return $this->render('admin/users/edit.html', $data);
	}

	/**
	 * Update the specified resource in storage and add new session flash message.
	 * @param  int  $id
	 * @return Response
	 */
	public function updates($id)
	{
		if(\Token::validate() && $this->validation('update')){
			try {
				$user = \Sentry::getUserProvider()->findById($id);

				$user->first_name = \Input::post('first_name');
				$user->last_name = \Input::post('last_name');
				$user->password = \Input::post('password');

				if($user->save()) {
					$this->app->flash('success', 'User was updated.');
					return $this->redirect('../users');
				}else {
					$this->app->flash('errror', 'User was not updated.');
					return $this->redirect('../users');
				}
			} catch (\Exception $e) {
				$this->app->flash('error', \SentryError::errors(get_class($e)));
            	return $this->redirect('../users');
			}
		}else{
			$this->app->flash('errors', \Validate::$errors);
			return $this->redirect('../users/'.$id.'/edit');
		}
	}

	/**
	 * Remove the specified resource from storage and add new session flash message.
	 * @param  int  $id
	 * @return Response
	 */
	public function deletes($id)
	{
		User::find($id)->delete();

		$this->app->flash('success', 'Success deleted post!');
		return $this->redirect('../users');
	}

	private function validation($method)
    {
    	$except = ['email' => 'required|email|unique:users'];
        $rules = [
            'first_name'	=> 'required',
            'last_name' 	=> 'required',
            'password' 		=> 'required',
            'password_confirmation' => 'required|same:password'
        ];

        return \Validate::make(\Input::post(), ($method == 'create') ? array_merge($except, $rules) : $rules);

    }
}