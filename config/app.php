<?php

return [
	'mode'                         => 'development',
	'log.enable'                   => false,
	'debug'                        => true,
	'controller.class_prefix'      => '\\App\\Controllers',
	'controller.method_suffix'     => 's',
    'controller.template_suffix'   => 'html',
    'view'                         => new \Slim\Views\Twig(),
	'templates.path'	           => __DIR__.'/../app/views'
];