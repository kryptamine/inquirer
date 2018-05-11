<?php

namespace Inquirer\Factory;

use Inquirer\Entity;

class Dialog
{
    /**
     * @return Entity\Dialog[]
     */
    public function getList()
    {
        return [
            new Entity\Dialog('php', 'PHP Quiz'),
            new Entity\Dialog('js', 'JavaScript Quiz'),
            new Entity\Dialog('test', 'Test Quiz'),
        ];
    }
}
