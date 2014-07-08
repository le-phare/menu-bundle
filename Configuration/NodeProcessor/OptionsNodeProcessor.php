<?php

namespace Lephare\Bundle\MenuBundle\Configuration\NodeProcessor;

use Knp\Menu\ItemInterface;
use Knp\Menu\MenuFactory;

class OptionsNodeProcessor extends AbstractNodeProcessor implements NodeProcessorInterface
{
    public function getName()
    {
        return 'options';
    }

    public function process(array $configuration, MenuFactory $factory = null, ItemInterface &$node = null)
    {
        var_dump($configuration);
    }

    public function getPriority()
    {
        return 9;
    }
}
