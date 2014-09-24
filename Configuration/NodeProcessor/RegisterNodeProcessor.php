<?php

namespace Lephare\Bundle\MenuBundle\Configuration\NodeProcessor;

use Knp\Menu\FactoryInterface;
use Knp\Menu\ItemInterface;

class RegisterNodeProcessor extends AbstractNodeProcessor implements NodeProcessorInterface
{
    public function getName()
    {
        return 'register';
    }

    public function process($configuration, array &$processors, FactoryInterface $factory, ItemInterface &$node = null)
    {
        foreach ($configuration as $processor) {
            if ($processor instanceof NodeProcessorInterface) {
                $processors[$processor->getName()] = $processor;
            }
        }

        uasort($processors, function ($a, $b) {
            if ($a->getPriority() == $b->getPriority()) {
                return 0;
            }
            return ($a->getPriority() < $b->getPriority()) ? -1 : 1;
        });

        return $node;
    }

    public function getPriority()
    {
        return 0;
    }

    public function validate($config)
    {
        return is_array($config) && !!count($config);
    }
}
