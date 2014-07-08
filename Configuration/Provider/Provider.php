<?php

namespace Lephare\Bundle\MenuBundle\Configuration\Provider;

use Symfony\Component\Finder\Finder;

class Provider implements ProviderInterface
{
    protected $providers;

    protected static $excluded = [
        'Provider.php',
        'ProviderInterface.php',
    ];

    public function __construct()
    {
        $finder = new Finder;

        $files = $finder->files()
            ->in(__DIR__)
            ->filter(function (\SplFileInfo $file) {
                if (in_array($file->getFilename(), self::$excluded)) {
                    return false;
                }
            })
        ;

        foreach ($files as $file) {
            $class = __NAMESPACE__ . '\\' . $file->getBasename('.php');
            if (($provider = new $class) instanceof ProviderInterface) {
                $this->providers[] = $provider;
            }
        }
    }

    public function handle($data)
    {
        $tmp = $data;

        foreach ($this->providers as $provider) {
            if (false !== ($tmp = $provider->handle($data))) {
                $data = $this->handle($tmp);
            }
        }

        return $data;
    }
}
