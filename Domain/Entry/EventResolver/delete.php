<?php

namespace lwListtool\Domain\Entry\EventResolver;

class delete extends \LWddd\DomainEventResolver
{
    public function __construct($event)
    {
        parent::__construct($event);
        $this->baseNamespace = "\\lwListtool\\Domain\\Entry\\";
        $this->ObjectClass = $this->baseNamespace."Object\\entry";
    }
    
    public function getInstance($event)
    {
        return new delete($event);
    }
    
    public function resolve()
    {
        $ok = $this->getCommandHandler()->deleteEntity($this->event->getParameterByKey("id"), $this->event->getParameterByKey("listId"));
        if ($ok) {
            $this->event->getResponse()->setParameterByKey('deleted', true);
        }
        else {
            $this->event->getResponse()->setDataByKey('error', 'error deleting');
            $this->event->getResponse()->setParameterByKey('error', true);
        }                    
        return $this->event->getResponse();
    }
}