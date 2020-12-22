<?php declare(strict_types=1);

namespace Devert\PluginChecker\Command;

use Shopware\Core\Framework\Adapter\Console\ShopwareStyle;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Shopware\Core\Framework\Context;
use Shopware\Core\Framework\Adapter\Cache\CacheClearer;
use Shopware\Core\Framework\Plugin\PluginCollection;
use Shopware\Core\Framework\Plugin\PluginEntity;
use Shopware\Core\Framework\Plugin\PluginLifecycleService;
use Shopware\Core\Framework\DataAbstractionLayer\EntityRepositoryInterface;
use Shopware\Core\Framework\DataAbstractionLayer\Search\Criteria;

abstract class AbstractCommand extends Command
{
    /**
     * @var PluginLifecycleService
     */
    protected $pluginLifecycleService;

    /**
     * @var CacheClearer
     */
    protected $cacheClearer;

    /**
     * @var EntityRepositoryInterface
     */
    private $pluginRepo;

    /**
     * @var string
     */
    private $projectDir;

    /**
     * @var string
     */
    private $pluginDir = 'var/pluginchecker.json';

    /**
     * @var array
     */
    private $checkList = [];

    public function __construct(
        PluginLifecycleService $pluginLifecycleService,
        EntityRepositoryInterface $pluginRepo,
        CacheClearer $cacheClearer,
        string $projectDir
    ) {
        parent::__construct();

        $this->pluginLifecycleService = $pluginLifecycleService;
        $this->pluginRepo = $pluginRepo;
        $this->cacheClearer = $cacheClearer;
        $this->projectDir = $projectDir;

        $this->initPath();
    }

    public function initPath()
    {
        $path = $this->getFilePath();

        //create plugin check list file if not exists
        if(!file_exists($path))
        {
            file_put_contents($path, '{}');
        }
    }

    public function getFilePath()
    {
        return $this->projectDir . '/' . $this->pluginDir;
    }

    public function readCheckList()
    {
        if(!$this->checkList)
        {
            $data = file_get_contents($this->getFilePath());
            $data = json_decode($data, true);

            if(!$data)
            {
                $data = array();
            }

            $this->checkList = $data;
        }

        return $this->checkList;
    }

    public function writeCheckList()
    {
        $out = [];
        foreach ($this->getPluginList() as $plugin)
        {
            $out[] = [
                'name' => $plugin->getName(),
                //'installed' => $plugin->getInstalledAt() === null ? false : true,
                'active' => $plugin->getActive(),
                'version' => $plugin->getVersion(),
            ];
        }

        file_put_contents($this->getFilePath(), json_encode($out, JSON_PRETTY_PRINT));
    }

    public function getPluginList()
    {
        $context = Context::createDefaultContext();
        $criteria = new Criteria();

        /** @var PluginCollection $plugins */
        return $this->pluginRepo->search($criteria, $context)->getEntities();
    }

    public function getMissingPlugins()
    {
        $list = $this->readCheckList();
        $plugins = $this->getPluginList();

        $filtered_plugins = [];
        foreach($plugins as $plugin)
        {
            //search if plugin name is in check list
            $key = array_search($plugin->getName(), array_column($list, 'name'));

            if($plugin->getActive() && $key===false)
            {
                //plugin not found in list
                $filtered_plugins[] = [
                    'plugin' => $plugin
                ];
            }
        }

        return $filtered_plugins;
    }

    public function outputMissingPlugins(array $filtered_plugins, OutputInterface $output)
    {
        if($filtered_plugins)
        {
            foreach($filtered_plugins as $item)
            {
                $output->writeln((string) '  <fg=red>' . $item['plugin']->getName() .  ' (Active: ' . $item['plugin']->getActive() . ')</>');
            }
        }else{
            $output->writeln((string) '  <fg=green>No mismatch found!</>');
        }
    }

    public function getVersionMismatchPlugins()
    {
        $list = $this->readCheckList();
        $plugins = $this->getPluginList();

        $filtered_plugins = [];
        foreach($list as $item)
        {
            //search plugin in PluginCollection
            $plugin = $plugins->filterByProperty('name', $item['name'])->first();

            //check if the version against the checklist
            if($plugin!==null && $plugin->getVersion()!==$item['version'])
            {
                $filtered_plugins[] = [
                    'plugin' => $plugin,
                    'checkPlugin' => $item,
                ];
            }
        }

        return $filtered_plugins;
    }

    public function outputVersionMismatchPlugins(array $filtered_plugins, OutputInterface $output)
    {
        if($filtered_plugins)
        {
            foreach($filtered_plugins as $item)
            {
                $output->writeln((string) '  <fg=red>' . $item['plugin']->getName() .  ' (' . $item['plugin']->getVersion() . ' vs ' . $item['checkPlugin']['version'] . ')</>');
            }
        }else{
            $output->writeln((string) '  <fg=green>No mismatch found!</>');
        }
    }

    public function getActiveMismatchPlugins()
    {
        $list = $this->readCheckList();
        $plugins = $this->getPluginList();

        $filtered_plugins = [];
        foreach($list as $item)
        {
            //search plugin in PluginCollection
            $plugin = $plugins->filterByProperty('name', $item['name'])->first();

            //check if the active state against the checklist
            if($plugin!==null && $plugin->getActive()!==$item['active'])
            {
                $filtered_plugins[] = [
                    'plugin' => $plugin,
                    'checkPlugin' => $item,
                ];
            }
        }

        return $filtered_plugins;
    }

    public function outputActiveMismatchPlugins(array $filtered_plugins, OutputInterface $output)
    {
        if($filtered_plugins)
        {
            foreach($filtered_plugins as $item)
            {
                $output->writeln((string) '  <fg=red>' . $item['plugin']->getName() .  ' (' . ($item['plugin']->getActive() ? 1 : 0) . ' -> ' . ($item['checkPlugin']['active'] ? 1 : 0) . ')</>');
            }
        }else{
            $output->writeln((string) '  <fg=green>No mismatch found!</>');
        }
    }

    public function log(string $command, $message)
    {
        $logFile = $this->projectDir . '/var/log/pluginchecker_' . $command . '.log';

        $data = [
            date("Y-m-d H:i:s"),
            '[' . $command . ']',
            $message,
            "\n"
        ]; 

        file_put_contents($logFile, implode(' ', $data), FILE_APPEND);
    }
}