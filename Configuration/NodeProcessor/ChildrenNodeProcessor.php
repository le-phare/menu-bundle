<?php

namespace Lephare\Bundle\MenuBundle\Configuration\NodeProcessor;

use Knp\Menu\FactoryInterface;
use Knp\Menu\ItemInterface;
use Knp\Menu\MenuItem;
use Lephare\Bundle\MenuBundle\Configuration\ConfigurationPriorityList;

class ChildrenNodeProcessor extends AbstractNodeProcessor implements NodeProcessorInterface
{
    public function getName()
    {
        return 'children';
    }

    public function getAliases()
    {
        return [ 'menu' ];
    }

    public function process($configuration, ConfigurationPriorityList $processors, FactoryInterface $factory, ItemInterface &$node = null)
    {
        if (null === $factory) {
            return false;
        }

        foreach ($configuration as $child) {
            if (is_callable($child)) {
                call_user_func_array($child, [ $node ]);
            } else {
                $menuItem = $factory->createItem('menu');

                NodeProcessor::getInstance()->process($child, $processors, $factory, $menuItem);

                if (null === $node) {
                    $node = $menuItem;
                } else {
                    $node->addChild($menuItem);
                }
            }
        }
    }

    public function getPriority()
    {
        return 30;
    }

    public function validate($config)
    {
        return is_callable($config) || (is_array($config) && !!count($config));
    }
}
