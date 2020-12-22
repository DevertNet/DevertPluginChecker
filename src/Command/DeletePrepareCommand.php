<?php declare(strict_types=1);

namespace Devert\PluginChecker\Command;

use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Shopware\Core\Framework\Context;

class DeletePrepareCommand extends AbstractCommand
{
    protected static $defaultName = 'pluginchecker:delete:prepare';

    private $composerTemplate = <<<EOL
{
  "name": "swag/plugin-skeleton",
  "description": "Skeleton plugin",
  "type": "shopware-platform-plugin",
  "license": "MIT",
  "autoload": {
    "psr-4": {
      "#namespace#\\\\": "src/"
    }
  },
  "extra": {
    "shopware-plugin-class": "#namespace#\\\\#class#",
    "label": {
      "de-DE": "Skeleton plugin",
      "en-GB": "Skeleton plugin"
    }
  }
}

EOL;

    protected function configure(): void
    {
        $this
            ->setDescription('Create dummy plugin if original files already deleted.');
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $output->writeln((string) '<fg=green>Prepare plugins:</>');

        $context = Context::createDefaultContext();
        $count = 0;

        $plugins = $this->getPluginList();
        foreach($plugins as $plugin)
        {
            if($plugin->getName()!='SwagPayPal')
            {
                continue;
            }

            //create dummy plugin if files not exist
            $this->createDummyPlugin($plugin);
        }
    }

    public function createDummyPlugin($plugin)
    {
        $pluginDir = $this->projectDir . '/' . $plugin->getPath();
        if(!file_exists($pluginDir . '/composer.json')) // . '/' . $plugin->getName() . '.php'
        {
            var_dump($plugin->getBaseClass());
            var_dump($plugin->getPath());
            var_dump('nope!');

            //create plugin folder
            if(!file_exists($pluginDir))
            {
                mkdir($pluginDir);
            }

            //create src folder
            if(!file_exists($pluginDir . '/src'))
            {
                mkdir($pluginDir . '/src');
            }
            

            $namespace = substr($plugin->getBaseClass(), 0, strrpos( $plugin->getBaseClass(), '\\'));
            $class = substr($plugin->getBaseClass(), strrpos($plugin->getBaseClass(), '\\') + 1);

            $data = "<?php declare(strict_types=1);
            
            namespace " . $namespace . ";

            use Shopware\Core\Framework\Plugin;
            
            class " . $class . " extends Plugin
            {
            }";

            $baseClassFile = $pluginDir . '/src/' . $plugin->getName() . '.php';
            file_put_contents($baseClassFile, $data);


            $composerFile = $pluginDir . '/composer.json';
            $composerFileData = $this->composerTemplate;
            $composerFileData = str_replace('#namespace#', str_replace('\\', '\\\\', $namespace), $composerFileData);
            $composerFileData = str_replace('#class#', $class, $composerFileData);
            file_put_contents($composerFile, $composerFileData);
        }
    }
}