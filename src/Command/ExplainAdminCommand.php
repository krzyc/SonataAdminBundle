<?php

declare(strict_types=1);

/*
 * This file is part of the Sonata Project package.
 *
 * (c) Thomas Rabaix <thomas.rabaix@sonata-project.org>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Sonata\AdminBundle\Command;

use Sonata\AdminBundle\Admin\Pool;
use Symfony\Component\Console\Attribute\AsCommand;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

/**
 * @author Thomas Rabaix <thomas.rabaix@sonata-project.org>
 */
#[AsCommand(name: 'sonata:admin:explain', description: 'Explain an admin service')]
final class ExplainAdminCommand extends Command
{
    // TODO: Remove static properties when support for Symfony < 5.4 is dropped.
    protected static $defaultName = 'sonata:admin:explain';
    protected static $defaultDescription = 'Explain an admin service';

    /**
     * @internal This class should only be used through the console
     */
    public function __construct(
        private Pool $pool
    ) {
        parent::__construct();
    }

    public function configure(): void
    {
        \assert(null !== static::$defaultDescription);

        $this
            // TODO: Remove setDescription when support for Symfony < 5.4 is dropped.
            ->setDescription(static::$defaultDescription)
            ->addArgument('admin', InputArgument::REQUIRED, 'The admin service id');
    }

    public function execute(InputInterface $input, OutputInterface $output): int
    {
        $admin = $this->pool->getInstance($input->getArgument('admin'));

        $output->writeln('<comment>AdminBundle Information</comment>');
        $output->writeln(sprintf('<info>% -20s</info> : %s', 'id', $admin->getCode()));
        $output->writeln(sprintf('<info>% -20s</info> : %s', 'Admin', $admin::class));
        $output->writeln(sprintf('<info>% -20s</info> : %s', 'Model', $admin->getClass()));
        $output->writeln(sprintf('<info>% -20s</info> : %s', 'Controller', $admin->getBaseControllerName()));
        $output->writeln(sprintf('<info>% -20s</info> : %s', 'Model Manager', $admin->getModelManager()::class));
        $output->writeln(sprintf('<info>% -20s</info> : %s', 'Form Builder', $admin->getFormBuilder()::class));
        $output->writeln(sprintf('<info>% -20s</info> : %s', 'Datagrid Builder', $admin->getDatagridBuilder()::class));
        $output->writeln(sprintf('<info>% -20s</info> : %s', 'List Builder', $admin->getListBuilder()::class));

        if ($admin->isChild()) {
            $output->writeln(sprintf('<info>% -15s</info> : %s', 'Parent', $admin->getParent()->getCode()));
        }

        $output->writeln('');
        $output->writeln('<info>Routes</info>');
        foreach ($admin->getRoutes()->getElements() as $route) {
            $output->writeln(sprintf('  - % -25s %s', $route->getDefault('_sonata_name'), $route->getPath()));
        }

        $output->writeln('');
        $output->writeln('<info>Datagrid Columns</info>');
        foreach ($admin->getListFieldDescriptions() as $name => $fieldDescription) {
            $output->writeln(sprintf(
                '  - % -25s  % -15s % -15s',
                $name,
                $fieldDescription->getType() ?? '',
                $fieldDescription->getTemplate() ?? ''
            ));
        }

        $output->writeln('');
        $output->writeln('<info>Datagrid Filters</info>');
        foreach ($admin->getFilterFieldDescriptions() as $name => $fieldDescription) {
            $output->writeln(sprintf(
                '  - % -25s  % -15s % -15s',
                $name,
                $fieldDescription->getType() ?? '',
                $fieldDescription->getTemplate() ?? ''
            ));
        }

        $output->writeln('');
        $output->writeln('<info>Form theme(s)</info>');
        foreach ($admin->getFormTheme() as $template) {
            $output->writeln(sprintf('  - %s', $template));
        }

        $output->writeln('');
        $output->writeln('<info>Form Fields</info>');
        foreach ($admin->getFormFieldDescriptions() as $name => $fieldDescription) {
            $output->writeln(sprintf(
                '  - % -25s  % -15s % -15s',
                $name,
                $fieldDescription->getType() ?? '',
                $fieldDescription->getTemplate() ?? ''
            ));
        }

        $output->writeln('');
        $output->writeln('<info>done!</info>');

        return 0;
    }
}
