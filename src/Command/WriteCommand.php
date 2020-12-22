<?php declare(strict_types=1);

namespace Devert\PluginChecker\Command;

use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

class WriteCommand extends AbstractCommand
{
    protected static $defaultName = 'pluginchecker:write';

    protected function configure(): void
    {
        $this
            ->setDescription('Write checklist to file.');
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $this->writeCheckList();
        $output->writeln((string) '<fg=green>Checklist successful writen to</>');
        $output->writeln((string) '  <fg=yellow> ' . $this->getFilePath() . '</>');

        $this->log('write', 'Checklist successful writen');
    }
}