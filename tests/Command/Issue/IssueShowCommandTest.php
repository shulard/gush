<?php

/*
 * This file is part of Gush package.
 *
 * (c) Luis Cordova <cordoval@gmail.com>
 *
 * This source file is subject to the MIT license that is bundled
 * with this source code in the file LICENSE.
 */

namespace Gush\Tests\Command\Issue;

use Gush\Command\Issue\IssueShowCommand;
use Gush\Exception\UserException;
use Gush\Tests\Command\CommandTestCase;
use Symfony\Component\Console\Helper\HelperSet;

class IssueShowCommandTest extends CommandTestCase
{
    /**
     * @test
     */
    public function it_shows_issue()
    {
        $tester = $this->getCommandTester(new IssueShowCommand());
        $tester->execute(['issue' => 60]);

        $this->assertCommandOutputMatches(
            [
                'Issue #60 - Write a behat test to launch strategy by weaverryan [open]',
                'Org/Repo: gushphp/gush',
                'Link: https://github.com/gushphp/gush/issues/60',
                'Labels: actionable, easy pick',
                'Milestone: v1.0',
                'Assignee: cordoval',
                'Help me conquer the world. Teach them to use Gush.',
            ],
            $tester->getDisplay()
        );
    }

    /**
     * @test
     */
    public function it_shows_issue_with_number_from_branch()
    {
        $command = new IssueShowCommand();
        $tester = $this->getCommandTester(
            $command,
            null,
            null,
            function (HelperSet $helperSet) {
                $helperSet->set($this->getLocalGitHelper()->reveal());
            }
        );

        $tester->execute();

        $this->assertCommandOutputMatches(
            [
                'Issue #60 - Write a behat test to launch strategy by weaverryan [open]',
                'Org/Repo: gushphp/gush',
                'Link: https://github.com/gushphp/gush/issues/60',
                'Labels: actionable, easy pick',
                'Milestone: v1.0',
                'Assignee: cordoval',
                'Help me conquer the world. Teach them to use Gush.',
            ],
            $tester->getDisplay()
        );
    }

    /**
     * @test
     */
    public function it_errors_when_the_number_cannot_be_auto_determined()
    {
        //@FIXME
        $this->markTestSkipped('We need to fix the test or remove it because logic sticks to the git helper');
        $command = new IssueShowCommand();
        $tester = $this->getCommandTester(
            $command,
            null,
            null,
            function (HelperSet $helperSet) {
                $helperSet->set($this->getLocalGitHelper()->reveal());
            }
        );

        $this->setExpectedException(
            UserException::class,
            'Unable to extract issue-number from the current branch name.'
        );

        $tester->execute();
    }

    private function getLocalGitHelper()
    {
        $helper = $this->getGitHelper(true);
        $helper->getIssueNumber()->willReturn(60);

        return $helper;
    }
}
