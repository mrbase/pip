<?php
/*
 * This file is part of the pip package.
 *
 * (c) Ulrik Nielsen <un@bellcom.dk>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

use Symfony\Component\HttpFoundation\Request;

include __DIR__.'/Pip/utils.php';
$config = include __DIR__.'/config.php';

$app = new \Pip\Application();
$app['jobs']  = $config['jobs'];
$app['users'] = $config['users'];
$app['roles'] = $config['roles'];

$app->register(new Silex\Provider\SecurityServiceProvider(), [
    'security.firewalls' => [
        'app' => [
            'pattern' => '^/',
            'http'    => true,
            'users'   => $app['users'],
        ]
    ]
]);

$app['twig'] = $app->share($app->extend('twig', function($twig, $app) {
    $twig->addGlobal('jobs', map_jobs($app));
    $twig->addExtension(new Twig_Extension_Debug());

    return $twig;
}));


// front page
$app->get('/', function(\Pip\Application $app) {
    $default = '';
    foreach (map_jobs($app) as $command => $options) {
        if (isset($options['default']) && $options['default']) {
            $default = $command;
            break;
        }
    }

    return $app['twig']->render('index.html.twig', [
        'default_command' => $default
    ]);
});


// command runner
$app->get('/run/{command}', function(\Pip\Application $app, $command) {
    // eject non defined jobs
    $jobs = map_jobs($app);
    if (empty($jobs[$command])) {
        return 'No such job defined! ("'.$command.'")';
    }

    $command = $jobs[$command]['cmd'];

    ob_end_clean();
    $stream = function () use ($command) {
        echo '<body style="padding:0;margin:0;"><p style="background-color:#d9edf7;padding:10px;margin-top:0;font-weight:bold;border-bottom:1px solid #000;position:fixed;top:0;width:100%;">Running command: '.$command.'</p><ol style="margin-top:0;margin-bottom:10px;margin-top:60px;">';
        @ob_flush();
        flush();

        $fh = popen($command, 'r');
        while (!feof($fh)) {
            echo '<li>'.fgets($fh)."</li><script>window.scrollBy(0,100000);</script>";
            @ob_flush();
            flush();
        }
        fclose($fh);

        echo '</ul>';
        @ob_flush();
        flush();
    };

    return $app->stream($stream);
});


// create new user block
$app->match('/add-user', function(Request $request, \Pip\Application $app) {
    if ('GET' == $request->getMethod()) {
        return $app['twig']->render('add-user.html.twig', ['roles' => $app['roles']]);
    }

    $username = $request->request->get('username');
    $password = $request->request->get('password');
    $roles    = $request->request->get('roles');

    if (empty($username) || empty($password) || empty($roles)) {
        return $app->redirect('/add-user');
    }

    $data = "
      '".$username."' => [
          ['".implode("','", $roles)."'],
          '".$app['security.encoder.digest']->encodePassword($password, '')."' // ".$password."
      ],";


    return $app['twig']->render('add-user.html.twig', [
        'roles'     => $app['roles'],
        'user_data' => $data
    ]);
})
->method('GET|POST');

return $app;
