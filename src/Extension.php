<?php

declare(strict_types=1);

namespace Dallask\GrumPHPPrettierTask;

use GrumPHP\Extension\ExtensionInterface;
use Dallask\GrumPHPPrettierTask\Task\Prettier;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Reference;

/**
 * Doc section.
 */
class Extension implements ExtensionInterface {

  /**
   * Doc section.
   */
  public function load(ContainerBuilder $container): void {
    $container
      ->register('task.prettier', Prettier::class)
      ->addArgument(new Reference('process_builder'))
      ->addArgument(new Reference('formatter.raw_process'))
      ->addTag('grumphp.task', ['task' => 'prettier']);
  }

}
