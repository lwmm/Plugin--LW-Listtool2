<?php

namespace lwListtool\Domain\Entry\EventResolver;

class getListEntriesAggregate extends \LWddd\DomainEventResolver
{
    public function __construct($event)
    {
        parent::__construct($event);
        $this->baseNamespace = "\\lwListtool\\Domain\\Entry\\";
        $this->ObjectClass = $this->baseNamespace."Object\\entry";
    }
    
    public function getInstance($event)
    {
        return new getListEntriesAggregate($event);
    }
    
    public function resolve()
    {
        $items = $this->getQueryHandler()->loadAllEntriesByListId($this->event->getParameterByKey("listId"), $this->event->getParameterByKey("sorting"));
        $aggregate = $this->buildAggregateFromQueryResult($items, true);        
        $this->event->getResponse()->setDataByKey('listEntriesAggregate', $aggregate);
        return $this->event->getResponse();        
    }
    
}