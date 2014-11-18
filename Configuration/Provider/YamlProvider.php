<?php

namespace Lephare\Bundle\MenuBundle\Configuration\Provider;

use Symfony\Component\Yaml\Yaml;

class YamlProvider implements ProviderInterface
{
    public function handle($data)
    {
        if (is_string($data)) {
            if (is_file($data)) {
                return Yaml::parse($data);
            } else {
                throw new \Exception("File '$data' not found.");
            }
        }

        return false;
    }
}
