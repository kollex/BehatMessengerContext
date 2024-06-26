<?php

declare(strict_types=1);

namespace DependencyInjection;

use BehatMessengerContext\Context\MessengerContext;
use BehatMessengerContext\DependencyInjection\BehatMessengerContextExtension;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\Attributes\UsesClass;
use PHPUnit\Framework\TestCase;
use Symfony\Component\Config\FileLocator;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Extension\Extension;
use Symfony\Component\DependencyInjection\Loader\XmlFileLoader;

#[CoversClass(BehatMessengerContextExtension::class)]
#[UsesClass(Extension::class)]
#[UsesClass(ContainerBuilder::class)]
#[UsesClass(XmlFileLoader::class)]
#[UsesClass(FileLocator::class)]
final class BehatMessengerContextExtensionTest extends TestCase
{
    public function testHasServices(): void
    {
        $extension = new BehatMessengerContextExtension();
        $container = new ContainerBuilder();

        $this->assertInstanceOf(Extension::class, $extension);

        $extension->load([], $container);

        $this->assertTrue($container->has(MessengerContext::class));
    }
}
