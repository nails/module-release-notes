<?php

namespace Nails\ReleaseNotes\Cron\Task;

use Nails\Cron\Task\Base;
use Nails\Environment;

/**
 * Class Fetch
 *
 * @package Nails\ReleaseNotes\Cron\Task\Alert
 */
class Fetch extends Base
{
    /**
     * The cron expression of when to run
     *
     * @var string
     */
    const CRON_EXPRESSION = '2 * * * *';

    /**
     * The console command to execute
     *
     * @var string
     */
    const CONSOLE_COMMAND = 'releasenotes:fetch';

    /**
     * Which environments to run the task on, leave empty to run on every environment
     *
     * @var string[]
     */
    const ENVIRONMENT = [
        Environment::ENV_PROD,
    ];
}
