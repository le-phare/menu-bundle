<?php

namespace Lephare\Bundle\MenuBundle\Configuration\NodeProcessor;

use Knp\Menu\FactoryInterface;
use Knp\Menu\ItemInterface;
use Lephare\Bundle\MenuBundle\Configuration\ConfigurationPriorityList;
use Lephare\Bundle\MenuBundle\Configuration\NodeProcessor\NodeProcessorInterface;
use Symfony\Component\DependencyInjection\ContainerAware;
use Symfony\Component\Finder\Finder;

class NodeProcessor extends ContainerAware
{
    protected static $instance = false;
    protected static $excluded = [
        'AbstractNodeProcessor.php',
        'NodeProcessor.php',
        'NodeProcessorInterface.php',
    ];

    public function getProcessors()
    {
        $finder = new Finder;
        $processors = new ConfigurationPriorityList;
        $processors->rewind();

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
                $processors->insert($processor->getName(), $processor, $processor->getPriority());
            }
        }

        return $processors;
    }

    public static function getInstance()
    {
        if (false === self::$instance) {
            self::$instance = new self;
        }

        return self::$instance;
    }

    public function process(array $configuration, ConfigurationPriorityList $processors = null, FactoryInterface $factory = null, ItemInterface $node = null)
    {
        if (null === $processors) {
            $processors = $this->getProcessors();
        }

        if (null === $factory) {
            $factory = $this->container->get('knp_menu.factory');
            $this->enhancedConfiguration($configuration);
            $configuration = reset($configuration);
        }

        foreach ($processors as $processor) {
            $used = false;
            foreach ($configuration as $key => $value) {
                if ($key === $processor->getName() || in_array($key, $processor->getAliases())) {
                    $used = true;

                    if (false === $processor->validate($value)) {
                        throw new \InvalidArgumentException("Invalid configuration for node \"{$processor->getName()}\".");
                    }

                    $processor->process($value, $processors, $factory, $node);
                }
            }
            if (!$used && $processor->isRequired()) {
                throw new \InvalidArgumentException("Node \"{$processor->getName()}\" must be defined.");
            }
        }

        return $node;
    }

    protected function enhancedConfiguration(array &$configuration)
    {
        foreach ($configuration as &$item) {
            if (is_array($item)) {
                $this->enhancedConfiguration($item);
            } else {
                if ('@' === $item[0]) {
                    $item = $this->container->get(substr($item, 1));
                } elseif (!!preg_match_all('/%[A-Za-z0-9_\-\.]+%/', $item, $matches)) {
                    $replace = [];
                    foreach ($matches[0] as $match) {
                        $replace[] = $this->container->getParameter(trim($match, '%'));
                    }
                    $item = str_replace($matches[0], $replace, $item);
                }
            }
        }
    }
}
