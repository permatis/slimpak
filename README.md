# SlimpaK
Slimpak built with Slim Framework in MVC Architecture. Slimpak inspired laravel, the database provider (Model) also use Eloquent but the template engine (View) use Twig.

# Features
- Example CRUD posts and users.
- Example Login, Register, ForgetPassword, ResetPassword.
- Example Sending mail activation and reset password.

# Demo
Coming soon...

# Installation
<h3>Install Manually</h3>
<pre>
git clone https://github.com/defriblackertz/SlimpaK.git
</pre>

<h3>Install via Composer</h3>
<pre>
composer create-project defriblackertz/SlimpaK --prefer-dist
</pre>


# Setup Permission
<pre>
sudo chgrp -R www-data storage/
or
sudo chown -R 755 storage/
</pre>

# Configuration and Setup Database
Configuration file of Slimpak located in <code>config</code> folder, edit the app.php, database.php, mail.php and session.php

#Router
Router file located in <code>app/router.php</code>.
``` php
//Route http get request
Route::get('/', function()($app){
    $app->render('index.html');
});

//Route controller
Route::addRoutes([
    '/login'       => ['get' => 'AuthController:getLogin', 'post' => 'AuthController:postLogin'],
    '/activation/:code'     => ['get' => 'AuthController:getActivation']
]);

//Route resource like Laravel Route::resource.
Route::resource('/posts', 'PostController');
```
And other example like slim doc <a href="http://docs.slimframework.com/routing/overview/">route</a>.

#Controller
Controller are located in <code>app/controllers</code>  After create file model class you must extends the <code>\Controller</code> to get access to predefined helper. And then set namespace like this <code>namespace App\Controllers;</code>. Keep in mind, if you create a new method you should add the suffix method. The default method is the suffix <code>s</code>, if you want to change your configuration to the app in <code>config/app.php</code> in <code>'controller.method_suffix'</code>

``` php

<?php namespace App\Controllers;

//if use your model.
use App\Model\MyModel;


class MyController extends \Controller {

    public function indexs()
    {
        $data = MyModel::all();

        //this render for view
        return $this->render('index.html', array('data' => $data);
    }

}

```

#Model
Model are located in <code>app/models</code>. After create file model class you must extends the <code>\Models</code>. For complete documentation about eloquent, please refer to http://laravel.com/docs/eloquent

``` php

<?php namespace App\Models;

class MyModel extends \Model {

    protected $table = 'table';

}

```

#Mail
The project is the use of mail using the <a href="https://github.com/swiftmailer/swiftmailer">Swift Mailer</a> libary.
<h3>Using in Route</h3>
``` php
Route::get('/send', function() use($app, $mailer) {

    $data['name'] = 'Defri';
    $email = $app->view->fetch('emails/activation.html', $data);

    $message = Swift_Message::newInstance('Test Email')
        ->setFrom(array('example@gmail.com' => 'Example'))
        ->setTo(array('example2@gmail.com' => 'Example2'))
        ->setBody($email, 'text/html');

    return $mailer->send($message);
}

```

<h3>Using in Controllers</h3>
``` php

public function postSendEmails()
{

    $data['name'] = 'Defri';
    $email = $this->app->view->fetch('emails/'.$type.'.html', $data);

    $message = Swift_Message::newInstance('Test Email')
        ->setFrom(array('example@gmail.com' => 'Example'))
        ->setTo(array(\Input::post('email') => \Input::post('email')))
        ->setBody($email, 'text/html');

    return $this->app->mailer->send($message);
}

```


