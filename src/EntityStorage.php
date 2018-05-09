<?php

namespace Inquirer;

use Inquirer\Entity\Entity;

class EntityStorage extends Storage
{
    /**
     * @param Entity $entity
     * @throws Exception\StorageException
     */
    public function addEntity(Entity $entity)
    {
        $this->add($entity->getKey(), $entity->toArray());
    }

    /**
     * @param Entity $entity
     * @throws Exception\StorageException
     */
    public function replaceEntity(Entity $entity)
    {
        $this->replace($entity->getKey(), $entity->toArray());
    }
}
