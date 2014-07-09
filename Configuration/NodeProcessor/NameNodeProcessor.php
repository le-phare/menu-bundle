<?php

namespace Lephare\Bundle\MenuBundle\Configuration\NodeProcessor;

use Knp\Menu\FactoryInterface;
use Knp\Menu\ItemInterface;
use Lephare\Bundle\MenuBundle\Configuration\ConfigurationPriorityList;

class NameNodeProcessor extends AbstractNodeProcessor implements NodeProcessorInterface
{
    public function getName()
    {
        return 'name';
    }

    public function process($configuration, ConfigurationPriorityList $processors, FactoryInterface $factory, ItemInterface &$node = null)
    {
        if (null === $node) {
            return false;
        }

        $node->setName($configuration);
    }

    public function getPriority()
    {
        return 10;
    }
}
