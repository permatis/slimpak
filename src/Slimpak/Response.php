<?php namespace Slimpak;

class Response extends \SlimFacades\Response {

	/**
	 * Set output response to json
	 * Reference from https://github.com/xsanisty/SlimStarter/blob/master/src/SlimStarter/Facade/ResponseFacade.php
	 * @param  string  $data
	 * @param  integer $status
	 * @return string
	 */
	public static function json($data, $status = 200){
		$app = static::$slim;
		$app->response->headers->set('Content-Type', 'application/json');
		$app->response->setStatus($status);

		if($data instanceof \Illuminate\Support\Contracts\JsonableInterface){
			$app->response->setBody($data->toJson());
		}else{
			return $app->response->setBody(json_encode($data));
		}
   	}
}