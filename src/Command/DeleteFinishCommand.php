<?php declare(strict_types=1);

namespace Devert\PluginChecker\Command;

use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Shopware\Core\Framework\Context;

/*
    For Debugging:
    rm -rf /home/leon/htdocs/shopware6/custom/plugins/SwagPayPal-1.8.4 && cp -R /home/leon/Downloads/SwagPayPal-1.8.4 /home/leon/htdocs/shopware6/custom/plugins/SwagPayPal-1.8.4 && ./bin/console plugin:install SwagPayPal && rm -rf /home/leon/htdocs/shopware6/custom/plugins/SwagPayPal-1.8.4 && ./bin/console pluginchecker:delete:prepare && ./bin/console pluginchecker:delete:finish
*/

class DeleteFinishCommand extends AbstractCommand
{
    protected static $defaultName = 'pluginchecker:delete:finish';

    protected function configure(): void
    {
        $this
            ->setDescription('Delete plugins (Not asking!).');
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $output->writeln((string) '<fg=green>Delete plugins:</>');

        $context = Context::createDefaultContext();
        $count = 0;

        $plugins = $this->getPluginList();
        foreach($plugins as $plugin)
        {
            if($plugin->getName()!='SwagPayPal')
            {
                continue;
            }

            //deactive plugin
            if ($plugin->getInstalledAt() && $plugin->getActive())
            {
                $msg = 'Deactivate ' . $plugin->getName();
                $output->writeln($msg);
                $this->log('delete', $msg);

                $this->pluginLifecycleService->deactivatePlugin($plugin, $context);
            }

            //uninstall plugin
            if ($plugin->getInstalledAt())
            {
                $msg = 'Uninstall ' . $plugin->getName();
                $output->writeln($msg);
                $this->log('delete', $msg);

                $this->pluginLifecycleService->uninstallPlugin($plugin, $context);
            }

            //delete plugin files
            $msg = 'Delete ' . $plugin->getName();
            $output->writeln($msg);
            $this->log('delete', $msg);
            $pluginDir = $this->projectDir . '/' . $plugin->getPath();
            $this->deleteDir($pluginDir);
        }
    }

    public function deleteDir($dirPath)
    {
        if (! is_dir($dirPath)) {
            throw new InvalidArgumentException("$dirPath must be a directory");
        }
        if (substr($dirPath, strlen($dirPath) - 1, 1) != '/') {
            $dirPath .= '/';
        }
        $files = glob($dirPath . '*', GLOB_MARK);
        foreach ($files as $file) {
            if (is_dir($file)) {
                $this->deleteDir($file);
            } else {
                unlink($file);
                $this->log('delete', 'Delete file ' . $file);
            }
        }
        rmdir($dirPath);
        $this->log('delete', 'Delete file ' . $file);
    }
}