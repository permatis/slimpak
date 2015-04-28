<?php

return [
	'Controller'	=> 'SlimController\SlimController',
	'DB'			=> 'Illuminate\Database\Capsule\Manager',
	'Facade'		=> 'SlimFacades\Facade',
	'Input'			=> 'SlimFacades\Input',
	'Model'			=> 'Illuminate\Database\Eloquent\Model',
	'Route'			=> 'Slimpak\Route',
	'Response' 		=> 'Slimpak\Response',
	'Sentry' 		=> 'Cartalyst\Sentry\Facades\Native\Sentry',
	'SentryError' 	=> 'Slimpak\SentryError',
	'Session' 		=> 'Slimpak\Session',
	'Slim'			=> 'SlimController\Slim',
	'Token'			=> 'Slimpak\Token',
	'Validate'		=> 'Slimpak\Validate'
];