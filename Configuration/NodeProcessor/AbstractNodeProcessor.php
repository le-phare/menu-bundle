<?php

namespace Lephare\Bundle\MenuBundle\Configuration\NodeProcessor;

abstract class AbstractNodeProcessor implements NodeProcessorInterface
{
    public function getAliases()
    {
        return [];
    }

    public function getPriority()
    {
        return 0;
    }

    public function isRequired()
    {
        return false;
    }

    public function validate($config)
    {
        return true;
    }
}
