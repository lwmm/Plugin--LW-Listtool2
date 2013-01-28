<?php

namespace lwListtool\Domain\Entry;

class EventHandler extends \LWddd\EventHandler
{
    public function __construct()
    {
        parent::__construct();
        $this->baseNamespace = "\\lwListtool\\Domain\\Entry\\";
    }
    
    public function getInstance()
    {
        return new EventHandler();
    }
    
    protected function getNewDomainObject($id=false)
    {
        return new \lwListtool\Domain\Entry\Object\entry($id);
    }

    protected function buildValueObjectFromPostArrays()
    {
        $array = $this->event->getDataByKey('postArray');
        $array['opt1file'] = $this->event->getDataByKey('opt1file');
        $array['opt2file'] = $this->event->getDataByKey('opt2file');
        return new \LWddd\ValueObject($array);
    }    
    
    public function getEntryEntityFromPostArray()
    {
        $entity = $this->buildEntityFromArray($this->event->getDataByKey('postArray'));
        $this->event->getResponse()->setDataByKey('EntryEntity', $entity);
        return $this->event->getResponse();
    }
    
    public function getListEntriesAggregate()
    {
        $items = $this->getQueryHandler()->loadAllEntriesByListId($this->event->getParameterByKey("listId"), $this->event->getParameterByKey("sorting"));
        $aggregate = $this->buildAggregateFromQueryResult($items);        
        $this->event->getResponse()->setDataByKey('listEntriesAggregate', $aggregate);
        return $this->event->getResponse();
    }
    
    public function add()
    {
        $DataValueObjectFiltered = $this->getDataValueObjectFilter()->filter($this->buildValueObjectFromPostArrays());
        $entity = $this->buildEntityFromValueObject($DataValueObjectFiltered);

        $isValidSpecification = $this->getIsValidSpecification();
        $isValidSpecification->setConfiguration($this->event->getParameterByKey("configuration"));

        if ($isValidSpecification->isSatisfiedBy($entity)) {
            $id = $this->saveEntity($entity);
            $this->event->getResponse()->setParameterByKey('saved', true);
        }
        else {
            echo "<h2>Errors: </h2><pre>";print_r($this->getIsValidSpecification()->getErrors());exit();
            $this->event->getResponse()->setDataByKey('error', $this->getIsValidSpecification()->getErrors());
            $this->event->getResponse()->setParameterByKey('error', true);
        }                    
        return  $this->event->getResponse();
    }
}