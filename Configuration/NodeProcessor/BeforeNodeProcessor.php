<?php

namespace Lephare\Bundle\MenuBundle\Configuration\NodeProcessor;

use Knp\Menu\FactoryInterface;
use Knp\Menu\ItemInterface;

class BeforeNodeProcessor extends AbstractNodeProcessor
{
    public function getName()
    {
        return 'before';
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
                    $this->insertBefore($name, $node, $submenuChild);
                }
            }
            unset($submenu);
        }
    }

    protected function insertBefore($name, ItemInterface $menu, ItemInterface $node)
    {
        if ($menu->hasChildren()) {
            if (null !== ($child = $menu->getChild($name))) {
                $childName = $child->getName();
                $order = array_keys($menu->getChildren());

                $position = array_search($childName, $order);
                $menu->addChild($node);
                $node->moveToPosition($position);
            } else {
                foreach ($menu->getChildren() as $child) {
                    $this->insertBefore($name, $child, $node);
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
