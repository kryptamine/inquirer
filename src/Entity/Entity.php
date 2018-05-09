<?php

namespace Inquirer\Entity;

interface Entity
{
    public function getKey();
    public function toArray();
}
