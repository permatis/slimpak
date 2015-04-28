<?php namespace App\Controllers;

use App\Models\Post;

class PostController extends \Controller {

	protected $rules = [
		'title'	=> 'required|min:3'
	];

	/**
	 * Display a listing of the resource.
	 * @return Response & Session Flash Message
	 */
	public function indexs()
	{
		$data = (empty(\Session::flash())) ? array('posts' => Post::all(), 'sentry' => \Sentry::getUser()) : array_merge(\Session::flash(), array('posts' => Post::all(), 'sentry' => \Sentry::getUser()));

		return $this->render('admin/posts/index.html', $data);
	}

	/**
	 * Show the form for creating a new resource.
	 * @return Response & Token
	 */
	public function creates()
	{
		$data = (empty(\Session::flash())) ? array('token' => \Token::getToken(), 'sentry' => \Sentry::getUser()) : array_merge(\Session::flash(), array('token' => \Token::getToken(), 'sentry' => \Sentry::getUser()));

		return $this->render('admin/posts/create.html', $data);
	}

	/**
	 * Store a newly created resource in storage and add new session flash message.
	 * @return Response
	 */
	public function stores()
	{
		if(\Token::validate() && \Validate::make(\Input::post(), $this->rules)){
			$post = new Post();
			$post->title = \Input::post('title');
			$post->slug = str_replace(' ', '-', \Input::post('title'));
			$post->save();

			$this->app->flash('success', 'Success created post.');
			return $this->redirect('posts');
		}else{
			$this->app->flash('errors', \Validate::$errors);
			return $this->redirect('posts/create');
		}
	}

	/**
	 * Show the form for editing the specified resource.
	 * @param  int  $id
	 * @return Response & Token
	 */
	public function edits($id)
	{
		$data = (empty(\Session::flash())) ? array('token' => \Token::getToken(), 'post' => Post::FindOrFail($id), 'sentry' => \Sentry::getUser())
				: array_merge(\Session::flash(), array('token' => \Token::getToken(), 'post' => Post::FindOrFail($id), 'sentry' => \Sentry::getUser()));

		return $this->render('admin/posts/edit.html', $data);
	}

	/**
	 * Update the specified resource in storage and add new session flash message.
	 * @param  int  $id
	 * @return Response
	 */
	public function updates($id)
	{
		$validation = \Validate::make(\Input::post(), $this->rules);

		if(\Token::validate() && $validation){

			$input = [
				'title' 	=> \Input::post('title'),
				'slug' 	=> str_replace(' ', '-', \Input::post('title'))
			];

			Post::find($id)->update($input);

			$this->app->flash('success', 'Success updated post!');
			return $this->redirect('../posts');
		}else{
			$this->app->flash('errors', \Validate::$errors);
			return $this->redirect('../posts/'.$id.'/edit');
		}
	}

	/**
	 * Remove the specified resource from storage and add new session flash message.
	 * @param  int  $id
	 * @return Response
	 */
	public function deletes($id)
	{
		Post::find($id)->delete();

		$this->app->flash('success', 'Success deleted post!');
		return $this->redirect('../posts');
	}
}