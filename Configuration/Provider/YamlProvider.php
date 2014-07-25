<?php

namespace Lephare\Bundle\MenuBundle\Configuration\Provider;

use Symfony\Component\Yaml\Yaml;

class YamlProvider implements ProviderInterface
{
    public function handle($data)
    {

        if (is_string($data)) {
            return Yaml::parse($data);
        }

        return false;
    }
}
