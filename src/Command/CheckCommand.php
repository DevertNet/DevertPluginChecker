<?php declare(strict_types=1);

namespace Devert\PluginChecker\Command;

use Shopware\Core\Framework\Adapter\Console\ShopwareStyle;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Question\Question;
use Shopware\Core\Framework\Context;
use Shopware\Core\Framework\Adapter\Cache\CacheClearer;
use Shopware\Core\Framework\Plugin\PluginCollection;
use Shopware\Core\Framework\Plugin\PluginEntity;
use Shopware\Core\Framework\Plugin\PluginLifecycleService;
use Shopware\Core\Framework\DataAbstractionLayer\EntityRepositoryInterface;
use Shopware\Core\Framework\DataAbstractionLayer\Search\Criteria;

class CheckCommand extends Command
{
    protected static $defaultName = 'pluginchecker:check';

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

    public function __construct(
        PluginLifecycleService $pluginLifecycleService,
        EntityRepositoryInterface $pluginRepo,
        CacheClearer $cacheClearer
    ) {
        parent::__construct();

        $this->pluginLifecycleService = $pluginLifecycleService;
        $this->pluginRepo = $pluginRepo;
        $this->cacheClearer = $cacheClearer;
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $io = new ShopwareStyle($input, $output);

        $this->writePluginList($input, $output);

        $passwordQuestion = new Question('Deactive the following plugins? (y/n; default: y)');
        //$passwordQuestion->setHidden(true);
        $passwordQuestion->setMaxAttempts(3);

        $password = $io->askQuestion($passwordQuestion);

        $output->writeln($password);

        $output->writeln('It works!');
    }

    public function writePluginList(InputInterface $input, OutputInterface $output)
    {
        $context = Context::createDefaultContext();

        $criteria = new Criteria();
        /** @var PluginCollection $plugins */
        $plugins = $this->pluginRepo->search($criteria, $context)->getEntities();

        $pluginTable = [];
        $active = $installed = $upgradeable = 0;

        foreach ($plugins as $plugin) {
            $pluginActive = $plugin->getActive();
            $pluginInstalled = $plugin->getInstalledAt();
            $pluginUpgradeable = $plugin->getUpgradeVersion();

            $out = 'Delete ' . $plugin->getName() . ' (Active: '. ($pluginActive ? 'Yes' : 'No') .')';
            
            $output->writeln((string) '<fg=red>'. $out .'</>');
        }
    }
}