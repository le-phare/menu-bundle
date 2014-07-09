<?php

namespace Lephare\Bundle\MenuBundle\Configuration\Provider;

class FileProvider implements ProviderInterface
{
    public function handle($data)
    {
        if (!is_string($data) || !file_exists($data)) {
            return false;
        }

        return file_get_contents($data);
    }
}
