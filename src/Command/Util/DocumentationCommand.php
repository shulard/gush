<?php

/**
 * This file is part of Gush package.
 *
 * (c) 2013-2014 Luis Cordova <cordoval@gmail.com>
 *
 * This source file is subject to the MIT license that is bundled
 * with this source code in the file LICENSE.
 */

namespace Gush\Command\Util;

use Gush\Application;
use Gush\Command\BaseCommand;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\StringInput;
use Symfony\Component\Console\Output\BufferedOutput;
use Symfony\Component\Console\Output\OutputInterface;

class DocumentationCommand extends BaseCommand
{
    /**
     * {@inheritdoc}
     */
    protected function configure()
    {
        $this
            ->setName('util:documentation')
            ->setDescription('Generate documentation for commands in .md files.')
            ->setHelp(
                <<<EOF
The <info>%command.name%</info> command generates command docuementation for gush:

    <info>$ gush %command.name%</info>

EOF
            )
        ;
    }

    /**
     * {@inheritdoc}
     */
    protected function execute(InputInterface $input, OutputInterface $output)
    {
        /** @var Application $app */
        $app = $this->getApplication();
        /** @var BaseCommand $command */
        foreach ($app->getCommands() as $command) {
            $output = new BufferedOutput();
            $input = new StringInput(sprintf('%s --help --format md', $command->getName()));

            $app->run($input, $output);

            $header = <<<EOT
---
layout: docu-page
full_title: "Gush: Rapid workflow for project maintainers and contributors"
---
{% block content %}
EOT;

            $footer = <<<EOT
{% endblock %}
EOT;

            file_put_contents(
                'web/'.str_replace(':', '_', $command->getName()).'.md',
                sprintf("%s\n%s\n%s", $header, $output->fetch(), $footer)
            );
        }

        return self::COMMAND_SUCCESS;
    }
}
