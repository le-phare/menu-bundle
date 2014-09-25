<?php

namespace Lephare\Bundle\MenuBundle\Configuration\NodeProcessor;

use Knp\Menu\FactoryInterface;
use Knp\Menu\ItemInterface;
use Knp\Menu\MenuItem;

class ChildrenNodeProcessor extends AbstractNodeProcessor
{
    public function getName()
    {
        return 'children';
    }

    public function getAliases()
    {
        return [ 'menu' ];
    }

    public function process($configuration, array &$processors, FactoryInterface $factory, ItemInterface &$node = null)
    {
        if (null === $factory) {
            return false;
        }

        $nodeProcessor = new NodeProcessor;
        $nodeProcessor->setContainer($this->container);

        foreach ($configuration as $name => $child) {
            if (is_callable($child)) {
                call_user_func_array($child, [ $node ]);
            } elseif ($child instanceof ItemInterface) {
                $node->addChild($child);
            } else {
                $menuItem = $factory->createItem('node');
                $child['name'] = !isset($child['name']) ? $name : $child['name'];

                $nodeProcessor->process($child, $processors, $factory, $menuItem);

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
