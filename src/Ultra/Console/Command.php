<?php

declare(strict_types=1);

namespace Ultra\Console;

use Symfony\Component\Console\Command\Command as SymfonyCommand;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;
use Throwable;

class Command extends SymfonyCommand
{
    /**
     * {@inheritdoc}
     */
    protected static $defaultName = 'app:default';

    /**
     * {@inheritdoc}
     */
    protected function configure(): void
    {
        $this->setDescription('Wrapper of the default "list" command');
        $this->setHidden(true);
    }


}