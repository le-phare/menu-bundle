<?php

namespace Lephare\Bundle\MenuBundle\Configuration\NodeProcessor;

use Knp\Menu\FactoryInterface;
use Knp\Menu\ItemInterface;

class RemoveEmptyNodeProcessor extends AbstractNodeProcessor
{
    public function getName()
    {
        return 'remove_empty';
    }

    public function process($configuration, array &$processors, FactoryInterface $factory, ItemInterface &$node = null)
    {
        if (null === $node) {
            return false;
        }

        $this->removeEmpty($node);
    }

    public function removeEmpty(ItemInterface $menu)
    {
        foreach ($menu as $rootMenu) {
            if ($rootMenu->hasChildren()) {
                $this->removeEmpty($rootMenu);
            } elseif (strpos($rootMenu->getUri(), '/') !== 0) {
                $rootMenu->setDisplay(false);
            }
        }
    }

    public function getPriority()
    {
        return 999;
    }
}
