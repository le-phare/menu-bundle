<?php

namespace Lephare\Bundle\MenuBundle\Configuration\NodeProcessor;

use Knp\Menu\FactoryInterface;
use Knp\Menu\ItemInterface;

class RoleNodeProcessor extends AbstractNodeProcessor
{
    public function getName()
    {
        return 'role';
    }

    public function process($configuration, array &$processors, FactoryInterface $factory, ItemInterface &$node = null)
    {
        if (null === $node) {
            return false;
        }

        $node->setDisplay($this->container->get('security.context')->isGranted($configuration));
    }

    public function getPriority()
    {
        return 11;
    }
}
