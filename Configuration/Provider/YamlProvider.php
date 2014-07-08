<?php

namespace Lephare\Bundle\MenuBundle\Configuration\Provider;

use Symfony\Component\Yaml\Parser;

class YamlProvider implements ProviderInterface
{
    public function handle($data)
    {
        $parser = new Parser;

        try {
            return $parser->parse($data);
        } catch (\Exception $e) {
            return false;
        }
    }
}
