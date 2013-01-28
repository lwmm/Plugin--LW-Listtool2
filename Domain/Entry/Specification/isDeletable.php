<?php

namespace lwListtool\Domain\Entry\Specification;

class isDeletable 
{
    public function __construct()
    {
    }
    
    static public function getInstance()
    {
        return new isDeletable();
    }
    
    public function isSatisfiedBy(lwListtool\Domain\Entry\Object\entry $entity)
    {
        return true;
    }
}