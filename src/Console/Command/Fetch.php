<?php

namespace Nails\ReleaseNotes\Console\Command;

use Nails\Console\Command\Base;
use Nails\Factory;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

/**
 * Class Fetch
 *
 * @package Nails\ReleaseNotes\Console\Command
 */
class Fetch extends Base
{
    /**
     * Configures the command
     */
    protected function configure()
    {
        $this
            ->setName('releasenotes:fetch')
            ->setDescription('Fetches new tags from GitHub');
    }

    // --------------------------------------------------------------------------

    /**
     * Executes the command
     *
     * @param InputInterface  $oInput
     * @param OutputInterface $oOutput
     *
     * @return int|void
     */
    protected function execute(InputInterface $oInput, OutputInterface $oOutput)
    {
        parent::execute($oInput, $oOutput);

        //  @todo (Pablo 2021-08-11) - fetch and store new tags

        return static::EXIT_CODE_SUCCESS;
    }
}
