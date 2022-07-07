<?php

declare(strict_types=1);

namespace EMS\FormBundle\Tests\Integration\App;

use PHPUnit\Runner\AfterLastTestHook;
use Symfony\Component\Filesystem\Filesystem;

final class ClearLogCacheListener implements AfterLastTestHook
{
    public function executeAfterLastTest(): void
    {
        $fs = new Filesystem();
        $kernel = new Kernel('test', true);

        $fs->remove($kernel->getCacheDir());
        $fs->remove($kernel->getLogDir());
    }
}
