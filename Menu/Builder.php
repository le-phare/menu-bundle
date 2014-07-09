<?php

namespace Lephare\Bundle\MenuBundle\Menu;

use Lephare\Bundle\MenuBundle\Configuration\NodeProcessor\NodeProcessor;
use Lephare\Bundle\MenuBundle\Configuration\Provider\Provider;
use Symfony\Component\DependencyInjection\ContainerAware;

class Builder
{
    protected $configuration;
    protected $processor;

    public function __construct(NodeProcessor $processor, $configuration)
    {
        $provider = new Provider;

        $this->configuration = $provider->handle($configuration);
        $this->processor = $processor;
    }


    public function build()
    {
        return $this->processor->process($this->configuration);
    }
}
