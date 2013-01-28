<?php

namespace lwListtool\Domain;

class DomainEventDispatch 
{
    public function __construct()
    {
        
    }
    
    public static function getinstance()
    {
        return new DomainEventDispatch();
    }
    
    public function execute($event)
    {
        $DomainEventHandlerClass = "\\lwListtool\\Domain\\".$event->getDomainName()."\\EventHandler";
        return $DomainEventHandlerClass::getInstance()->execute($event);
    }
}