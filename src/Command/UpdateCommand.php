<?php declare(strict_types=1);

namespace Devert\PluginChecker\Command;

use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Shopware\Core\Framework\Context;

class UpdateCommand extends AbstractCommand
{
    protected static $defaultName = 'pluginchecker:update';

    protected function configure(): void
    {
        $this
            ->setDescription('Update (local) all plugins.');
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $output->writeln((string) '<fg=green>Update plugins:</>');

        $context = Context::createDefaultContext();
        $count = 0;

        $plugins = $this->getPluginList();
        foreach($plugins as $plugin)
        {
            if ($plugin->getInstalledAt() && $plugin->getActive())
            {
                $output->writeln($plugin->getName());
                $this->pluginLifecycleService->updatePlugin($plugin, $context);

                $this->log('update', $plugin->getName());

                $count++;
            }
        }

        if($count===0)
        {
            $output->writeln((string) '<fg=red>No installed & active plugins found.</>');
            $this->log('update', 'No installed & active plugins found');
        }
    }
}