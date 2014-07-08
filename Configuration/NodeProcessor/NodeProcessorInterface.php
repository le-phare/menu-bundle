<?php

namespace Lephare\Bundle\MenuBundle\Configuration\NodeProcessor;

use Knp\Menu\ItemInterface;
use Knp\Menu\MenuFactory;

interface NodeProcessorInterface
{
    public function getAliases();

    public function getName();

    public function getPriority();

    public function isRequired();

    public function process(array $configuration, MenuFactory $factory = null, ItemInterface &$node = null);

    public function validate($config);
}
