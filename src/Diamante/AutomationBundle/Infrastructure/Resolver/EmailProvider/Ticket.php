<?php
/*
 * Copyright (c) 2015 Eltrino LLC (http://eltrino.com)
 *
 * Licensed under the Open Software License (OSL 3.0).
 * you may not use this file except in compliance with the License.
 * You may obtain a copy of the License at
 *
 *    http://opensource.org/licenses/osl-3.0.php
 *
 * If you did not receive a copy of the license and are unable to
 * obtain it through the world-wide-web, please send an email
 * to license@eltrino.com so we can send you a copy immediately.
 */

namespace Diamante\AutomationBundle\Infrastructure\Resolver\EmailProvider;

use Diamante\UserBundle\Api\UserService;

/**
 * Class Ticket
 *
 * @package Diamante\AutomationBundle\Infrastructure\Resolver\EmailProvider
 */
class Ticket implements EntityProvider
{
    /**
     * @var UserService
     */
    protected $userService;

    /**
     * Ticket constructor.
     *
     * @param UserService $userService
     */
    public function __construct(UserService $userService)
    {
        $this->userService = $userService;
    }

    /**
     * @param array $target
     *
     * @return string
     */
    public function getReporter(array $target)
    {
        $user = $this->userService->getByUser($target['reporter']);

        return $user->getEmail();
    }

    /**
     * @param array $target
     *
     * @return array
     */
    public function getWatchers(array $target)
    {
        $list = [];
        $watchers = $target['watcherList'];

        foreach ($watchers->getValues() as $watcher) {
            $user = $this->userService->getByUser($watcher->getUserType());
            $list[] = $user->getEmail();
        }
        return $list;
    }

    /**
     * @param array $target
     *
     * @return string
     */
    public function getAssignee(array $target)
    {
        if (empty($target['assignee'])) {
            return null;
        }

        $user = $this->userService->getByUser($target['assignee']);

        return $user->getEmail();
    }

    /**
     * @return string
     */
    public function getName()
    {
        return 'ticket';
    }
}