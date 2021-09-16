<?php declare(strict_types=1);

namespace Dallask\TotalChecker\Task;

use GrumPHP\Collection\ProcessArgumentsCollection;
use GrumPHP\Fixer\Provider\FixableProcessProvider;
use GrumPHP\Runner\FixableTaskResult;
use GrumPHP\Runner\TaskResult;
use GrumPHP\Runner\TaskResultInterface;
use GrumPHP\Task\AbstractExternalTask;
use GrumPHP\Task\Context\ContextInterface;
use GrumPHP\Task\Context\GitPreCommitContext;
use GrumPHP\Task\Context\RunContext;
use Symfony\Component\OptionsResolver\OptionsResolver;

class Prettier extends AbstractExternalTask
{
    public static function getConfigurableOptions(): OptionsResolver
    {
      $resolver = new OptionsResolver();
        $resolver->setDefaults([
            // Task config options
            'bin' => null,
            'triggered_by' => ['css', 'less', 'scss', 'sass', 'pcss'],
            'allowed_paths' => null,

            // prettier config options
            'config' => null,
        ]);

        // Task config options
        $resolver->addAllowedTypes('bin', ['null', 'string', 'array']);
        $resolver->addAllowedTypes('allowed_paths', ['null', 'array']);
        $resolver->addAllowedTypes('triggered_by', ['array']);

        // prettier config options
        $resolver->addAllowedTypes('config', ['null', 'string']);

        return $resolver;
    }

    public function canRunInContext(ContextInterface $context): bool
    {
        return ($context instanceof GitPreCommitContext || $context instanceof RunContext);
    }

    public function run(ContextInterface $context): TaskResultInterface
    {
      $config = $this->getConfig()->getOptions();

        $files = $context
            ->getFiles()
            ->paths($config['allowed_paths'] ?? [])
            ->extensions($config['triggered_by']);

        if (0 === \count($files)) {
            return TaskResult::createSkipped($this, $context);
        }

        $arguments = isset($config['bin'])
            ? array_reduce(
                is_array($config['bin']) ? $config['bin'] : [$config['bin']],
                static function ($carry, $item): ProcessArgumentsCollection {
                    if ($carry instanceof ProcessArgumentsCollection) {
                        $carry->add($item);
                        return $carry;
                    }

                    return ProcessArgumentsCollection::forExecutable($item);
                }
            )
            : $this->processBuilder->createArgumentsForCommand('prettier');



        $arguments->addOptionalArgument('--config=%s', $config['config']);
        $arguments->add('--check');


        $arguments->addFiles($files);

        $process = $this->processBuilder->buildProcess($arguments);
        $process->run();

        if (!$process->isSuccessful()) {
            $arguments->add('--write');
            $fixerCommand = $this->processBuilder
                ->buildProcess($arguments)
                ->getCommandLine();

            $message = sprintf(
                '%sYou can fix errors by running the following command:%s',
                $this->formatter->format($process) . PHP_EOL . PHP_EOL,
                PHP_EOL . $fixerCommand
            );

            return new FixableTaskResult(
                TaskResult::createFailed($this, $context, $message),
                FixableProcessProvider::provide($fixerCommand)
            );
        }

        return TaskResult::createPassed($this, $context);
    }
}
