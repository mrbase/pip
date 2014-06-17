<?php
/*
 * This file is part of the pip package.
 *
 * (c) Ulrik Nielsen <un@bellcom.dk>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

function map_jobs($app) {
    $user_roles = $app['security']
        ->getToken()
        ->getUser()
        ->getRoles()
    ;

    $jobs = [];
    foreach ($app['jobs'] as $r => $job) {
        $job_roles = explode(',', $r);

        if (array_intersect($user_roles, $job_roles)) {
            $jobs = array_merge($jobs, $job);
        }
    }

    return $jobs;
}
