<?php
/**
 * Route for auth.
 */
Route::addRoutes([
    '/login'       => ['get' => 'AuthController:getLogin', 'post' => 'AuthController:postLogin'],
    '/logout'      => ['get' => 'AuthController:getLogout'],
	'/register'    => ['get' => 'AuthController:getRegister', 'post' => 'AuthController:postRegister'],
    '/reset'        => ['get' => 'AuthController:getResetPassword', 'post' => 'AuthController:postResetPassword'],
    '/resetpassword/:code'  => ['get' => 'AuthController:getResetPasswordCode', 'post' => 'AuthController:postResetPasswordCode'],
    '/activation/:code'     => ['get' => 'AuthController:getActivation']
]);

/**
 * Route group for admin pages.
 */
Route::group('/admin', $authenticateUsers(), function() use($app) {
    Route::resource('/posts', 'PostController');
    Route::resource('/users', 'UserController');
    Route::get('/home', function() use($app) {
       $app->render('admin/index.html', array('sentry' => Sentry::getUser()));
    });
});

/**
 * Route for welcome pages.
 */
Route::get('/', function() use($app) {
    $app->render('home.html');
});
