<?php

namespace lwListtool\Domain\Entry\EventResolver;

class getIsDeletableSpecification extends \LWddd\DomainEventResolver
{
    public function __construct($event)
    {
        parent::__construct($event);
        $this->baseNamespace = "\\lwListtool\\Domain\\Entry\\";
    }
    
    public function getInstance($event)
    {
        return new getListEntriesAggregate($event);
    }
    
    public function resolve()
    {
        $class = $this->baseNamespace.'Specification\isDeletable';
        $this->event->getResponse()->setDataByKey('isDeletableSpecification', $class::getInstance());
        return $this->event->getResponse();
    }
}