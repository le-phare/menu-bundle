<?php

namespace Lephare\Bundle\MenuBundle\Configuration\NodeProcessor;

use Knp\Menu\FactoryInterface;
use Knp\Menu\ItemInterface;

class PrependNodeProcessor extends AbstractNodeProcessor
{
    public function getName()
    {
        return 'prepend';
    }

    public function getAliases()
    {
        return [];
    }

    public function process($configuration, array $processors, FactoryInterface $factory, ItemInterface &$node = null)
    {
        if (null === $factory) {
            return false;
        }

        $childrenProcessor = $processors['children'];
        foreach ($configuration as $name => $items) {
            $submenu = $factory->createItem('node', []);
            $childrenProcessor->process(array_reverse($items), $processors, $factory, $submenu);
            if ($submenu->hasChildren()) {
                foreach ($submenu->getChildren() as $submenuChild) {
                    $submenuChild->setParent(null);
                    $this->prepend($name, $node, $submenuChild);
                }
            }
            unset($submenu);
        }
    }

    protected function prepend($name, ItemInterface $menu, ItemInterface $node)
    {
        if ($menu->hasChildren()) {
            if (null !== ($child = $menu->getChild($name))) {
                $child->addChild($node);
                $node->moveToFirstPosition();
            } else {
                foreach ($menu->getChildren() as $child) {
                    $this->prepend($name, $child, $node);
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
