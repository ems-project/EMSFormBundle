<?php

declare(strict_types=1);

namespace EMS\FormBundle\Tests\Integration\App;

use Doctrine\Bundle\DoctrineBundle\DoctrineBundle;
use EMS\ClientHelperBundle\EMSClientHelperBundle;
use EMS\CommonBundle\EMSCommonBundle;
use EMS\FormBundle\EMSFormBundle;
use Symfony\Bundle\FrameworkBundle\FrameworkBundle;
use Symfony\Bundle\TwigBundle\TwigBundle;
use Symfony\Component\Config\Loader\LoaderInterface;
use Symfony\Component\HttpKernel\Kernel as BaseKernel;

final class Kernel extends BaseKernel
{
    public static function getPath(): string
    {
        return __DIR__.'/var';
    }

    public function getCacheDir(): string
    {
        return self::getPath().'/cache/'.$this->environment;
    }

    public function getLogDir(): string
    {
        return self::getPath().'/log';
    }

    public function registerBundles(): array
    {
        return [
            new FrameworkBundle(),
            new TwigBundle(),
            new DoctrineBundle(),
            new EMSCommonBundle(),
            new EMSClientHelperBundle(),
            new EMSFormBundle(),
        ];
    }

    public function registerContainerConfiguration(LoaderInterface $loader): void
    {
        $loader->load(__DIR__.'/config/config.yml');
    }
}
