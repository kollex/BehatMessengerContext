<?php

declare(strict_types=1);

namespace BehatMessengerContext;

use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\HttpKernel\Bundle\Bundle;

class BehatMessengerContextBundle extends Bundle
{
    public function __construct()
    {
    }

    public function build(ContainerBuilder $container): void
    {
        parent::build($container);
    }
}
