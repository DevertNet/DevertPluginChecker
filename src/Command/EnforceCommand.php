<?php declare(strict_types=1);

namespace Devert\PluginChecker\Command;

use Shopware\Core\Framework\Adapter\Console\ShopwareStyle;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Question\ConfirmationQuestion;

class EnforceCommand extends AbstractCommand
{
    protected static $defaultName = 'pluginchecker:enforce';

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $io = new ShopwareStyle($input, $output);

        $this->deactivateMissing($input, $output, $io);
        $this->enforceActiveState($input, $output, $io);
        $this->checkVersion($input, $output, $io);
    }

    public function deactivateMissing(InputInterface $input, OutputInterface $output, ShopwareStyle $io)
    {
        //output missing plugins
        $output->writeln((string) '<fg=yellow>Installed in SW, but not in the check list</>');
        $plugins = $this->getMissingPlugins();
        $this->outputMissingPlugins($plugins, $output);

        if($plugins)
        {
            //ask to deactivate them
            $helper = $this->getHelper('question');
            $question = new ConfirmationQuestion('<fg=green>Should the plugins be deactivated?</> (yes/no) [no]', false);
            if ($helper->ask($input, $output, $question)) {
                $output->writeln('Deactivate dat shit!');
            }
        }

        $io->newLine();
    }

    public function enforceActiveState(InputInterface $input, OutputInterface $output, ShopwareStyle $io)
    {
        $output->writeln((string) '<fg=yellow>Plugin Active State Mismatch (SW -> Checklist)</>');

        $plugins = $this->getActiveMismatchPlugins();
        $this->outputActiveMismatchPlugins($plugins, $output);

        if($plugins)
        {
            //ask to deactivate them
            $helper = $this->getHelper('question');
            $question = new ConfirmationQuestion('<fg=green>Deactivate/Activate plugins</> (yes/no) [yes]', true);
            if ($helper->ask($input, $output, $question)) {
                $output->writeln('Rataatatatatatata!');
            }
        }

        $io->newLine();
    }

    public function checkVersion(InputInterface $input, OutputInterface $output, ShopwareStyle $io)
    {
        $plugins = $this->getVersionMismatchPlugins();

        if($plugins)
        {
            $output->writeln((string) '<fg=yellow>Plugin Version Mismatch</>');
            $this->outputVersionMismatchPlugins($plugins, $output);

            $io->newLine();
        }
    }
}