<?php

$finder = Symfony\CS\Finder\DefaultFinder::create()
        ->exclude('logs')
        ->exclude('cache')
        ->in('app');

return Symfony\CS\Config\Config::create()
                ->level(Symfony\CS\FixerInterface::PSR2_LEVEL)
                ->finder($finder);
