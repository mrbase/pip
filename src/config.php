<?php
/*
 * This file is part of the pip package.
 *
 * (c) Ulrik Nielsen <un@bellcom.dk>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

/** ----------------------------------------
 *  Add new jobs by adding an element to the
 *  array here.
 *
 *  'safe-key' => [
 *    'name' => 'Display name',
 *    'cmd'  => 'the actual command to run',
 *  ]
 *
 *  the "safe key" is used as url trigger, so
 *  jobs not defined below will not run.
 *
 *  "name" is a label that is used to display
 *  to the user in the job list.
 *
 *  "cmd" is the command to run.
 * ----------------------------------------- */

return [
    'ping-google' => [
        'name' => 'Ping Google',
        'cmd'  => 'ping -c 20 www.google.com',
    ],
];;
