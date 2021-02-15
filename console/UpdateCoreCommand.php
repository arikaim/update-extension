<?php
/**
 * Arikaim
 *
 * @link        http://www.arikaim.com
 * @copyright   Copyright (c)  Konstantin Atanasov <info@arikaim.com>
 * @license     http://www.arikaim.com/license
 * 
 */
namespace Arikaim\Extensions\Update;

use Arikaim\Core\Console\ConsoleCommand;
use Arikaim\Core\Console\ConsoleHelper;
use Arikaim\Core\System\Update;

/**
 * Update core command class 
 */
class UpdateCoreCommand extends ConsoleCommand
{  
    /**
     * Command config
     * 
     * @return void
     */
    protected function configure(): void
    {
        $this->setName('update:core')->setDescription('Arikaim CMS Core Packages Update');
    }

    /**
     * Command code
     *
     * @param InputInterface $input
     * @param OutputInterface $output
     * @return void
     */
    protected function executeCommand($input, $output)
    {
        $this->showTitle();
      
      
    }
}
