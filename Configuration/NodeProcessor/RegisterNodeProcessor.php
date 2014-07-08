<?php

namespace Lephare\Bundle\MenuBundle\Configuration\NodeProcessor;

use Knp\Menu\ItemInterface;
use Knp\Menu\MenuFactory;

class RegisterNodeProcessor extends AbstractNodeProcessor implements NodeProcessorInterface
{
    public function getName()
    {
        return 'register';
    }

    public function process(array $configuration, MenuFactory $factory = null, ItemInterface &$node = null)
    {
        $processors = NodeProcessor::getInstance()->getProcessors();

        foreach ($configuration as $processor) {
            if ($processor instanceof NodeProcessorInterface) {
                $processors->insert($processor, $processor->getPriority());
            }
        }

        return $node;
    }

    public function getPriority()
    {
        return -1;
    }

    public function validate($config)
    {
        return is_array($config) && !!count($config);
    }
}
