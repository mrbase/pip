<?php
/*
 * This file is part of the pip package.
 *
 * (c) Ulrik Nielsen <un@bellcom.dk>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Pip;

use Silex\Application as SilexApplication;
use Silex\Provider\HttpCacheServiceProvider;
use Silex\Provider\SessionServiceProvider;
use Silex\Provider\TwigServiceProvider;
use Silex\Provider\UrlGeneratorServiceProvider;

class Application extends SilexApplication
{
    public function __construct()
    {
        parent::__construct();

        $this->register(new HttpCacheServiceProvider());
        $this->register(new SessionServiceProvider());
        $this->register(new UrlGeneratorServiceProvider());

        // Cache
        $this['cache.path'] = __DIR__ . '/../../tmp/cache';
        $this['http_cache.cache_dir'] = $this['cache.path'] . '/http';

        $this->register(new TwigServiceProvider(), [
            'twig.path' => __DIR__.'/../twig/templates',
            'twig.options' => [
                'cache' => false,
                'strict_variables' => true,
            ],
        ]);

        // error handler.
        $sd = $sd = $ert = 1;
        $this->error(function (\Exception $e, $code) use ($sd, $sd, $ert) {
            error_log($e->getMessage());

            switch ($code) {
                case 404:
                    $message  = 'The requested page could not be found.';
                    break;
                default:
                    $message  = 'We are sorry, but something went terribly wrong.';            }

            return $this['twig']->render('error.html.twig', [
                'code' => $code,
                'site_title' => 'PIP - ' . $code . ' - error encountered',
                'message' => $message
            ]);
        });
    }
}
