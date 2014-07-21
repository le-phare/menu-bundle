<?php

namespace Lephare\Bundle\MenuBundle\Configuration\Provider;

use Symfony\Component\Yaml\Parser;

class YamlProvider implements ProviderInterface
{
    public function handle($data)
    {
        $parser = new Parser;

        if (is_string($data)) {
            return $parser->parse($data);
        }

        return false;
    }
}
