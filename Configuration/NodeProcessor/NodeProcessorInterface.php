<?php

namespace Lephare\Bundle\MenuBundle\Configuration\NodeProcessor;

use Knp\Menu\FactoryInterface;
use Knp\Menu\ItemInterface;

interface NodeProcessorInterface
{
    public function getAliases();

    public function getName();

    public function getPriority();

    public function isRequired();

    public function process($configuration, array &$processors, FactoryInterface $factory, ItemInterface &$node = null);

    public function validate($config);
}
