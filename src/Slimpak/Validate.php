<?php namespace Slimpak;

use Illuminate\Database\Capsule\Manager as DB;

class Validate {

	public static $errors;

	public static function make (array $data, array $rules)
	{
		//remove token
		$data = (isset($data['token'])) ? array_slice($data, 1) : $data;

		foreach ($rules as $key => $rule) {

			$rule = explode('|', $rule);

			//Check if not empty
			if(in_array('required', $rule)){
				if(empty($data[$key])){
					static::$errors[] = 'The '.$key.' field is required';
				}
			}

			//Check if valid email
			if(in_array('email', $rule)){
				if (!filter_var($data[$key], FILTER_VALIDATE_EMAIL)) {
					static::$errors[] = 'The '.$key.' must be a valid email address.';
				}
			}

			//Check if valid URL
			if(in_array('url', $rule)){
				if (!preg_match("/\b(?:(?:https?|ftp):\/\/|www\.)[-a-z0-9+&@#\/%?=~_|!:,.;]*[-a-z0-9+&@#\/%=~_|]/i", $data[$key])) {
					static::$errors[] = 'The '.$key.' format is invalid.';
				}
			}

			//Check if use min in rules
			$search_min = 'min';
			$min = array_filter($rule, function($r) use ($search_min) { return (strpos($r, $search_min) !== false ); });

			if($min)
			{
				$min = (!empty($min[key($min)])) ? explode(':', $min[key($min)]) : '';

				if(strlen($data[$key]) <= $min[1]){
					static::$errors[] = 'The '.$key.' must be at least '.$min[1].' characters.';
				}
			}

			//Check if unique
			$search_unique = 'unique';
			$unique = array_filter($rule, function($r) use ($search_unique) { return (strpos($r, $search_unique) !== false ); });

			if($unique)
			{
				$unique = (!empty($unique[key($unique)])) ? explode(':', $unique[key($unique)]) : '';
				$query = (bool) DB::table($unique[1])->where($key, '=', $data[$key])->first();
				if($query){
					static::$errors[] = 'The '.$key.' has already been taken.';
				}
			}

			//Check if confirmed
			$search_same = 'same';
			$same = array_filter($rule, function($r) use ($search_same) { return (strpos($r, $search_same) !== false ); });
			if($same)
			{
				$same = (!empty($same[key($same)])) ? explode(':', $same[key($same)]) : '';
				if($data[$same[1]] != $data[$key])
				{
					static::$errors[] = 'The '.$same[1].' and '.$key.' must match.';
				}
			}
		}

		return (empty(static::$errors)) ? true : false;
	}
}