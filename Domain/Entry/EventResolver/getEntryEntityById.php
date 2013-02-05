<?php

namespace lwListtool\Domain\Entry\EventResolver;

class getEntryEntityById extends \LWddd\DomainEventResolver
{
    public function __construct($event)
    {
        parent::__construct($event);
        $this->dic = new \lwListtool\Services\dic();
        $this->baseNamespace = "\\lwListtool\\Domain\\Entry\\";
        $this->ObjectClass = $this->baseNamespace."Object\\entry";
    }
    
    public function getInstance($event)
    {
        return new getEntryEntityById($event);
    }
    
    public function resolve()
    {
        $item = $this->getQueryHandler()->loadEntryById($this->event->getParameterByKey("id"), $this->event->getParameterByKey("listId"));
        $entity = $this->buildEntityFromArray($item, true);
        $this->event->getResponse()->setDataByKey('EntryEntity', $entity);
        return $this->event->getResponse();       
    }
}