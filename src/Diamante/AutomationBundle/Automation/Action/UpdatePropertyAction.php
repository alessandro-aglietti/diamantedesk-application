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

namespace Diamante\AutomationBundle\Automation\Action;

use Diamante\AutomationBundle\Rule\Action\AbstractModifyAction;
use Diamante\DeskBundle\Infrastructure\Persistence\DoctrineGenericRepository;
use Diamante\DeskBundle\Model\Shared\Updatable;
use Diamante\AutomationBundle\Configuration\AutomationConfigurationProvider;

/**
 * Class UpdatePropertyAction
 *
 * @package Diamante\AutomationBundle\Automation\Action
 */
class UpdatePropertyAction extends AbstractModifyAction
{
    /**
     * @var AutomationConfigurationProvider
     */
    private $configurationProvider;

    public function execute()
    {
        $context = $this->getContext();
        $target = $context->getFact()->getTarget();
        $targetType = $context->getFact()->getTargetType();
        $properties = $context->getParameters()->all();
        $targetClass = $this->configurationProvider->getEntityConfiguration($targetType)->get('class');

        $entity = $this->update($target, $targetClass, $properties);
        $this->disableListeners();
        $this->registry->getManager()->persist($entity);
    }

    /**
     * @param AutomationConfigurationProvider $configurationProvider
     */
    public function setConfigurationProvider(AutomationConfigurationProvider $configurationProvider)
    {
        $this->configurationProvider = $configurationProvider;
    }

    /**
     * @param array $target
     * @param       $targetClass
     * @param       $properties
     *
     * @return $this|Updatable
     */
    protected function update(array $target, $targetClass, $properties)
    {
        $targetEntity = new \ReflectionClass($targetClass);

        if ($targetEntity->hasMethod('updateProperties')) {
            /** @var DoctrineGenericRepository $repository */
            $repository = $this->registry->getManager()->getRepository($targetClass);
            /** @var Updatable $entity */
            $entity = $repository->get($target['id']);
            $entity->updateProperties($properties);

            return $entity;
        }

        return $this;
    }
}