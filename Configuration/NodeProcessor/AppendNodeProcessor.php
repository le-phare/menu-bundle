<?php

namespace Lephare\Bundle\MenuBundle\Configuration\NodeProcessor;

use Knp\Menu\FactoryInterface;
use Knp\Menu\ItemInterface;

class AppendNodeProcessor extends AbstractNodeProcessor
{
    public function getName()
    {
        return 'append';
    }

    public function getAliases()
    {
        return [];
    }

    public function process($configuration, array &$processors, FactoryInterface $factory, ItemInterface &$node = null)
    {
        if (null === $factory) {
            return false;
        }

        $childrenProcessor = $processors['children'];
        foreach ($configuration as $name => $items) {
            $submenu = $factory->createItem('node', []);
            $childrenProcessor->process($items, $processors, $factory, $submenu);
            if ($submenu->hasChildren()) {
                foreach ($submenu->getChildren() as $submenuChild) {
                    $submenuChild->setParent(null);
                    $this->append($name, $node, $submenuChild);
                }
            }
            unset($submenu);
        }
    }

    protected function append($name, ItemInterface $menu, ItemInterface $node)
    {
        if ($menu->hasChildren()) {
            if (null !== ($child = $menu->getChild($name))) {
                $child->addChild($node);
                $node->moveToLastPosition();
            } else {
                foreach ($menu->getChildren() as $child) {
                    $this->append($name, $child, $node);
                }
            }
        }

        return false;
    }

    public function getPriority()
    {
        return 40;
    }

    public function validate($config)
    {
        return !!is_array($config);
    }
}
