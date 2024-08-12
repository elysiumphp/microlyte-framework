<?php

declare(strict_types=1);

namespace Ultra\Console;

use Symfony\Component\Console\Application as SymfonyApplication;
use Symfony\Component\Console\Command\Command as SymfonyCommand;
use Symfony\Component\Console\Exception\CommandNotFoundException;
use Symfony\Component\Console\Input\ArrayInput;
use Symfony\Component\Console\Input\InputDefinition;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Input\StringInput;
use Symfony\Component\Console\Output\BufferedOutput;

class Application extends SymfonyApplication
{
    public const CONSOLE_LOGO = <<<'ASCII'
   _______      ___    ___ _________  ________  _______   _____ ______   _______      
|\  ___ \    |\  \  /  /|\___   ___\\   __  \|\  ___ \ |\   _ \  _   \|\  ___ \     
\ \   __/|   \ \  \/  / ||___ \  \_\ \  \|\  \ \   __/|\ \  \\\__\ \  \ \   __/|    
 \ \  \_|/__  \ \    / /     \ \  \ \ \   _  _\ \  \_|/_\ \  \\|__| \  \ \  \_|/__  
  \ \  \_|\ \  /     \/       \ \  \ \ \  \\  \\ \  \_|\ \ \  \    \ \  \ \  \_|\ \ 
   \ \_______\/  /\   \        \ \__\ \ \__\\ _\\ \_______\ \__\    \ \__\ \_______\
    \|_______/__/ /\ __\        \|__|  \|__|\|__|\|_______|\|__|     \|__|\|_______|
             |__|/ \|__|                                                            
                                                                                    
                                                                                    
ASCII;

    public const CONSOLE_NAME = 'EXTREME';

     /**
     * {@inheritdoc}
     *
     * @param 
     */
    public function __construct()
    {
        parent::__construct();

        if ($defaultName = Command::getDefaultName()) {
            $this->setDefaultCommand($defaultName);
        }
    }

    /**
     * {@inheritdoc}
     */
    public function getHelp(): string
    {
        return self::CONSOLE_LOGO.parent::getHelp();
    }

    /**
     * {@inheritdoc}
     */
    public function getName(): string
    {
        return self::CONSOLE_NAME;
    }

    /**
     * {@inheritdoc}
     */
    public function getVersion(): string
    {
        return '1.0.0';
    }
}