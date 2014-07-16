<?php

namespace Lephare\Bundle\MenuBundle\Configuration\NodeProcessor;

use Knp\Menu\FactoryInterface;
use Knp\Menu\ItemInterface;
use Lephare\Bundle\MenuBundle\Configuration\ConfigurationPriorityList;
use Lephare\Bundle\MenuBundle\Configuration\NodeProcessor\NodeProcessorInterface;
use Symfony\Component\DependencyInjection\ContainerAwareInterface;
use Symfony\Component\DependencyInjection\ContainerInterface;
use Symfony\Component\Finder\Finder;

class NodeProcessor implements ContainerAwareInterface
{
    protected static $excluded = [
        'AbstractNodeProcessor.php',
        'NodeProcessor.php',
        'NodeProcessorInterface.php',
    ];

    /**
     * @{inheritDoc}
     */
    protected $container;

    /**
     * @{inheritDoc}
     */
    public function setContainer(ContainerInterface $container = null)
    {
        $this->container = $container;
    }

    public function getProcessors()
    {
        $finder = new Finder;
        $processors = [];

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
                if ($processor instanceof ContainerAwareInterface) {
                    $processor->setContainer($this->container);
                }
                $processors[$processor->getName()] = $processor;
            }
        }

        uasort($processors, function ($a, $b) {
            if ($a->getPriority() == $b->getPriority()) {
                return 0;
            }
            return ($a->getPriority() < $b->getPriority()) ? -1 : 1;
        });

        return $processors;
    }

    public function process(array $configuration, array $processors = [], FactoryInterface $factory = null, ItemInterface $node = null)
    {
        if (!$processors) {
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
