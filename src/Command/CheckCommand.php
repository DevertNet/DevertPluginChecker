<?php declare(strict_types=1);

namespace Devert\PluginChecker\Command;

use Shopware\Core\Framework\Adapter\Console\ShopwareStyle;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

class CheckCommand extends AbstractCommand
{
    protected static $defaultName = 'pluginchecker:check';

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $io = new ShopwareStyle($input, $output);

        $this->checkMissing($input, $output, $io);
        $this->checkActiveState($input, $output, $io);
        $this->checkVersion($input, $output, $io);
    }

    public function checkActiveState(InputInterface $input, OutputInterface $output, ShopwareStyle $io)
    {
        $output->writeln((string) '<fg=yellow>Plugin Active State Mismatch (SW -> Checklist)</>');

        $plugins = $this->getActiveMismatchPlugins();
        $this->outputActiveMismatchPlugins($plugins, $output);

        $io->newLine();
    }

    public function checkMissing(InputInterface $input, OutputInterface $output, ShopwareStyle $io)
    {
        $output->writeln((string) '<fg=yellow>Installed in SW, but not in the check list</>');

        $plugins = $this->getMissingPlugins();
        $this->outputMissingPlugins($plugins, $output);

        $io->newLine();
    }

    public function checkVersion(InputInterface $input, OutputInterface $output, ShopwareStyle $io)
    {
        $output->writeln((string) '<fg=yellow>Plugin Version Mismatch (SW vs Checklist)</>');

        $plugins = $this->getVersionMismatchPlugins();
        $this->outputVersionMismatchPlugins($plugins, $output);

        $io->newLine();
    }
}