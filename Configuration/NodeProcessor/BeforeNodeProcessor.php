<?php

namespace Lephare\Bundle\MenuBundle\Configuration\NodeProcessor;

use Knp\Menu\FactoryInterface;
use Knp\Menu\ItemInterface;
use Lephare\Bundle\MenuBundle\Configuration\ConfigurationPriorityList;

class BeforeNodeProcessor extends AbstractNodeProcessor implements NodeProcessorInterface
{
    public function getName()
    {
        return 'before';
    }

    public function getAliases()
    {
        return [];
    }

    public function process($configuration, ConfigurationPriorityList $processors, FactoryInterface $factory, ItemInterface &$node = null)
    {
            var_dump($configuration);
        foreach ($configuration as $nodeName => $nodeData) {
            var_dump($node->getChildren());
            if ($child = $node->getChild($nodeName)) {
                var_dump($child);
            } elseif ($children = $node->getChildren()) {
                foreach ($children as $child) {
                    $this->process($configuration, $processors, $factory, $child);
                }
            }
        }
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
