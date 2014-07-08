<?php

namespace Lephare\Bundle\MenuBundle\Configuration\NodeProcessor;

use Knp\Menu\ItemInterface;
use Knp\Menu\MenuFactory;

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

    public function process(array $configuration, MenuFactory $factory = null, ItemInterface &$node = null)
    {
        // var_dump($configuration);
    }

    public function getPriority()
    {
        return 10;
    }

    public function validate($config)
    {
        return is_array($config) && !!count($config);
    }
}
