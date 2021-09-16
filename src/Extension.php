<?php declare(strict_types=1);

namespace Dallask\TotalChecker;

use GrumPHP\Extension\ExtensionInterface;
use Dallask\TotalChecker\Task\Prettier;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Reference;

class Extension implements ExtensionInterface
{
    public function load(ContainerBuilder $container): void
    {
        $container
            ->register('task.prettier', Prettier::class)
            ->addArgument(new Reference('process_builder'))
            ->addArgument(new Reference('formatter.raw_process'))
            ->addTag('grumphp.task', ['task' => 'prettier']);
    }
}
