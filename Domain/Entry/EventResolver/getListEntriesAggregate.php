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
        $conf = $this->event->getParameterByKey("configuration");
        $items = $this->getQueryHandler()->loadAllEntriesByListId($this->event->getParameterByKey("listId"), $conf->getValueByKey("sorting"));
        $aggregate = $this->buildAggregateFromQueryResult($items, true);        
        $this->event->getResponse()->setDataByKey('listEntriesAggregate', $aggregate);
        return $this->event->getResponse();        
    }
    
}