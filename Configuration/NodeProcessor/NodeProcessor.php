<?php

namespace Lephare\Bundle\MenuBundle\Configuration\NodeProcessor;

use Knp\Menu\MenuFactory;
use Lephare\Bundle\MenuBundle\Configuration\ConfigurationQueue;
use Lephare\Bundle\MenuBundle\Configuration\NodeProcessor\NodeProcessorInterface;
use Symfony\Component\DependencyInjection\ContainerAware;
use Symfony\Component\Finder\Finder;

class NodeProcessor extends ContainerAware
{
    protected $processors;

    protected static $instance = false;
    protected static $excluded = [
        'AbstractNodeProcessor.php',
        'NodeProcessor.php',
        'NodeProcessorInterface.php',
    ];

    public function __construct()
    {
        $finder = new Finder;
        $this->processors = new ConfigurationQueue;

        $files = $finder->files()
            ->in(__DIR__)
            ->filter(function (\SplFileInfo $file) {
                if (in_array($file->getFilename(), self::$excluded)) {
                    return false;
                }
            })
        ;

        foreach ($files as $file) {
            $class = __NAMESPACE__ . '\\' . $file->getBasename('.php');
            if (($processor = new $class) instanceof NodeProcessorInterface) {
                $this->processors->insert($processor, $processor->getPriority());
            }
        }
    }

    public static function getInstance()
    {
        if (false === self::$instance) {
            self::$instance = new self;
        }

        return self::$instance;
    }

    public function getProcessors()
    {
        return $this->processors;
    }

    public function process(array $configuration, MenuFactory $factory = null, ItemInterface $node = null)
    {
        if (null === $factory) {
            $factory = $this->container->get('knp_menu.factory');
            $configuration = current($this->enhancedConfiguration($configuration));
        }

        var_dump($configuration);
        foreach ($this->processors as $processor) {
            $used = false;
            foreach ($configuration as $key => $value) {
                if ($key === $processor->getName() || in_array($key, $processor->getAliases())) {
                    $used = true;

                    if (false === $processor->validate($value)) {
                        throw new \InvalidArgumentException("Invalid configuration for node \"{$processor->getName()}\".");
                    }

                    $processor->process($value, $factory, $node);

                    // var_dump($node);
                }
            }
            // var_dump($processor->getName());
            if (!$used && $processor->isRequired()) {
                throw new \InvalidArgumentException("Node \"{$processor->getName()}\" must be defined.");
            }
        }

        return $node;
    }

    protected function enhancedConfiguration(array $configuration)
    {
        // foreach ($configuration as &$item) {
        //     if (is_array($item)) {
        //         $item = $this->enhancedConfiguration($item);
        //     } else {
        //         if ('@' === $item[0]) {
        //             $item = $this->container->get(substr($item, 1, strlen($item)));
        //         } elseif (!!preg_match('/^%\w+%$/', $item, $matches)) {
        //             var_dump($matches);
        //             $item = $matches[1];
        //         } else {
        //             $item = $item;
        //         }
        //     }
        // }

        return $configuration;
    }
}
