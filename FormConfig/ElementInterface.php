<?php

namespace EMS\FormBundle\FormConfig;

interface ElementInterface
{
    public function getName(): string;

    public function getClassName(): string;
}
