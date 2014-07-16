<?php

namespace Lephare\Bundle\MenuBundle\Configuration\NodeProcessor;

use Symfony\Component\DependencyInjection\ContainerAwareInterface;
use Symfony\Component\DependencyInjection\ContainerInterface;

abstract class AbstractNodeProcessor implements NodeProcessorInterface, ContainerAwareInterface
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

    /**
     * @{inheritDoc}
     */
    protected $container;

    /**
     * @{inheritDoc}
     */
    public function setContainer(ContainerInterface $container = null)
    {
        $this->container = $container;
    }
}
