<?php

namespace KaiokenFramework\Page;

interface IAction
{
    public function setParameter($param, $value);
    public function serialize();
}