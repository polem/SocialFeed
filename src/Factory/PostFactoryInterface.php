<?php

/*
 * This file is part of the Social Feed Util.
 *
 * (c) LaNetscouade <contact@lanetscouade.com>
 *
 * This source file is subject to the MIT license that is bundled
 * with this source code in the file LICENSE.
 */

namespace Lns\SocialFeed\Factory;

interface PostFactoryInterface
{
    /**
     * create.
     *
     * @param array $data
     */
    public function create(array $data);
}
