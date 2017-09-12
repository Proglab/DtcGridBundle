<?php

namespace Dtc\GridBundle\DependencyInjection\Compiler;

use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Compiler\CompilerPassInterface;

class GridSourceCompilerPass implements CompilerPassInterface
{
    public function process(ContainerBuilder $container)
    {
        $sourceManager = $container->getDefinition('dtc_grid.manager.source');

        // Add each worker to workerManager, make sure each worker has instance to work
        foreach ($container->findTaggedServiceIds('dtc_grid.source') as $id => $attributes) {
            $gridSourceDefinition = $container->getDefinition($id);
            $class = $gridSourceDefinition->getClass();

            $refClass = new \ReflectionClass($class);
            $interface = 'Dtc\GridBundle\Grid\Source\GridSourceInterface';

            if (!$refClass->implementsInterface($interface)) {
                throw new \InvalidArgumentException(sprintf('Service "%s" must implement interface "%s".', $id, $interface));
            }

            $gridSourceDefinition->addMethodCall('setId', array($id));
            $sourceManager->addMethodCall('add', [$id, $id]);
        }
    }
}
