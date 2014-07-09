<?php

namespace Lephare\Bundle\MenuBundle\Configuration\NodeProcessor;

use Knp\Menu\FactoryInterface;
use Knp\Menu\ItemInterface;
use Lephare\Bundle\MenuBundle\Configuration\ConfigurationPriorityList;

class RegisterNodeProcessor extends AbstractNodeProcessor implements NodeProcessorInterface
{
    public function getName()
    {
        return 'register';
    }

    public function process($configuration, ConfigurationPriorityList $processors, FactoryInterface $factory, ItemInterface &$node = null)
    {
        $processors = NodeProcessor::getInstance()->getProcessors();

        foreach ($configuration as $processor) {
            if ($processor instanceof NodeProcessorInterface) {
                $processors->insert($processor->getName(), $processor, $processor->getPriority());
            }
        }

        return $node;
    }

    public function getPriority()
    {
        return 0;
    }

    public function validate($config)
    {
        return is_array($config) && !!count($config);
    }
}
