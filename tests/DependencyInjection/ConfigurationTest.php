<?php

declare(strict_types=1);

namespace DependencyInjection;

use BehatMessengerContext\DependencyInjection\Configuration;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\Attributes\UsesClass;
use PHPUnit\Framework\TestCase;
use Symfony\Component\Config\Definition\Builder\NodeBuilder;
use Symfony\Component\Config\Definition\Builder\TreeBuilder;
use Symfony\Component\Config\Definition\ConfigurationInterface;
use Symfony\Component\Config\Definition\Processor;

#[CoversClass(Configuration::class)]
#[UsesClass(TreeBuilder::class)]
#[UsesClass(NodeBuilder::class)]
final class ConfigurationTest extends TestCase
{
    public function testConfiguration(): void
    {
        $processor = new Processor();
        $configuration = new Configuration();

        $this->assertInstanceOf(ConfigurationInterface::class, $configuration);

        $configs = $processor->processConfiguration($configuration, []);

        $this->assertSame([], $configs);
    }
}
