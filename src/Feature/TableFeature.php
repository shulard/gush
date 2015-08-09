<?php

/*
 * This file is part of Gush package.
 *
 * (c) Luis Cordova <cordoval@gmail.com>
 *
 * This source file is subject to the MIT license that is bundled
 * with this source code in the file LICENSE.
 */

namespace Gush\Feature;

/**
 * The TableSubscriber will act on classes implementing
 * this interface.
 */
interface TableFeature
{
    /**
     * Return the default table layout to use.
     *
     * @return string
     */
    public function getTableDefaultLayout();
}
