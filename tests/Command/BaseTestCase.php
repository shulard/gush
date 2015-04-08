<?php

/*
 * This file is part of Gush package.
 *
 * (c) 2013-2015 Luis Cordova <cordoval@gmail.com>
 *
 * This source file is subject to the MIT license that is bundled
 * with this source code in the file LICENSE.
 */

namespace Gush\Tests\Command;

use Gush\Adapter\DefaultConfigurator;
use Gush\Config;
use Gush\Event\CommandEvent;
use Gush\Event\GushEvents;
use Gush\Factory\AdapterFactory;
use Gush\Helper\OutputAwareInterface;
use Gush\Tester\Adapter\TestAdapter;
use Gush\Tester\Adapter\TestIssueTracker;
use Gush\Tests\TestableApplication;
use Guzzle\Http\Client;
use Prophecy\Prophecy\ObjectProphecy;
use Prophecy\Prophet;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Event\ConsoleEvent;
use Symfony\Component\Console\Input\InputAwareInterface;
use Symfony\Component\Console\Tester\CommandTester;

class BaseTestCase extends \PHPUnit_Framework_TestCase
{
    /**
     * @var TestAdapter
     */
    protected $adapter;

    /**
     * @var Config|ObjectProphecy
     */
    protected $config;

    /**
     * @var Prophet
     */
    protected $prophet;

    protected function setUp()
    {
        $this->prophet = new Prophet();
        $this->config = $this->prophet->prophesize('Gush\Config');
        $this->adapter = $this->buildAdapter();
    }

    public function tearDown()
    {
        $this->prophet->checkPredictions();
    }

    protected function assertCommandOutputEquals($expected, $output)
    {
        $this->assertEquals($expected, implode("\n", array_map('trim', explode("\n", trim($output)))));
    }

    /**
     * @param Command $command
     *
     * @return CommandTester
     */
    protected function getCommandTester(Command $command)
    {
        $adapterFactory = new AdapterFactory();
        $adapterFactory->registerAdapter(
            'github',
            function () {
                return new TestAdapter();
            },
            function ($helperSet) {
                return new DefaultConfigurator(
                    $helperSet->get('question'),
                    'GitHub',
                    'https://api.github.com/',
                    'https://github.com'
                );
            }
        );

        $adapterFactory->registerAdapter(
            'github_enterprise',
            function () {
                return new TestAdapter();
            },
            function ($helperSet) {
                return new DefaultConfigurator(
                    $helperSet->get('question'),
                    'GitHub Enterprise',
                    '',
                    ''
                );
            }
        );

        $adapterFactory->registerIssueTracker(
            'github',
            function () {
                return new TestIssueTracker();
            },
            function ($helperSet) {
                return new DefaultConfigurator(
                    $helperSet->get('question'),
                    'GitHub',
                    'https://api.github.com/',
                    'https://github.com'
                );
            }
        );

        $adapterFactory->registerIssueTracker(
            'jira',
            function () {
                return new TestIssueTracker();
            },
            function ($helperSet) {
                return new DefaultConfigurator(
                    $helperSet->get('question'),
                    'Jira',
                    '',
                    ''
                );
            }
        );

        $application = new TestableApplication($adapterFactory);
        $application->setAutoExit(false);
        $application->setConfig($this->config->reveal());
        $application->setAdapter($this->adapter);
        $application->setIssueTracker($this->adapter);
        $application->setVersionEyeClient($this->buildVersionEyeClient());

        $command->setApplication($application);

        $application->getDispatcher()->dispatch(
            GushEvents::DECORATE_DEFINITION,
            new CommandEvent($command)
        );

        $application->getDispatcher()->addListener(
            GushEvents::INITIALIZE,
            function (ConsoleEvent $event) {
                $command = $event->getCommand();
                $input = $event->getInput();
                $output = $event->getOutput();

                foreach ($command->getHelperSet() as $helper) {
                    if ($helper instanceof InputAwareInterface) {
                        $helper->setInput($input);
                    }

                    if ($helper instanceof OutputAwareInterface) {
                        $helper->setOutput($output);
                    }
                }
            }
        );

        return new CommandTester($command);
    }

    /**
     * @return TestAdapter
     */
    protected function buildAdapter()
    {
        return new TestAdapter();
    }

    protected function buildVersionEyeClient()
    {
        $client = new Client();
        $client
            ->setBaseUrl('https://www.versioneye.com/')
            ->setDefaultOption('query', ['api_key' => '123'])
        ;

        return $client;
    }

    protected function expectsConfig($username = 'cordoval')
    {
        $this->config->get('adapter')->willReturn('github_enterprise');
        $this->config->get('[adapters][github_enterprise][authentication]')->willReturn(['username' => $username]);
    }
}
