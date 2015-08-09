<?php

/*
 * This file is part of Gush package.
 *
 * (c) Luis Cordova <cordoval@gmail.com>
 *
 * This source file is subject to the MIT license that is bundled
 * with this source code in the file LICENSE.
 */

namespace Gush\Tests\Subscriber;

use Gush\Feature\GitRepoFeature;
use Symfony\Component\Console\Command\Command;

class TestGitRepoCommand extends Command implements GitRepoFeature
{
}
