<?php
/*
 * This file is part of the pip package.
 *
 * (c) Ulrik Nielsen <un@bellcom.dk>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

$app = new \Pip\Application();
$app['job-map'] = include __DIR__.'/config.php';

$app['twig'] = $app->share($app->extend('twig', function($twig, $app) {
    $twig->addGlobal('jobs', $app['job-map']);
    $twig->addExtension(new Twig_Extension_Debug());

    return $twig;
}));


// front page
$app->get('/', function(\Pip\Application $app) {
    return $app['twig']->render('index.html.twig');
});


// command runner
$app->get('/run/{command}', function(\Pip\Application $app, $command) {
    // eject non defined jobs
    if (empty($app['job-map'][$command])) {
        return 'No such job defined! ("'.$command.'")';
    }

    $command = $app['job-map'][$command]['cmd'];

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

return $app;
