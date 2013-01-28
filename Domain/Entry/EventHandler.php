<?php

namespace lwListtool\Domain\Entry;

class EventHandler 
{
    public function __construct()
    {
        $this->dic = new \lwListtool\Services\dic();
    }
    
    public function getInstance()
    {
        return new EventHandler();
    }

    public function execute($event)
    {
        $this->event = $event;
        $method = $this->event->getEventName();
        return $this->$method();
    }

    public function getEntryEntityFromArray()
    {
        $dataValueObject = new \LWddd\ValueObject($this->event->getDataByKey('postArray'));
        $entity = \lwListtool\Domain\Entry\Model\Factory::getInstance()->buildNewObjectFromValueObject($dataValueObject);
        $this->event->getResponse()->setDataByKey('EntryEntity', $entity);
        return $this->event->getResponse();
        
    }
    
    public function getAllEntriesAggregate()
    {
        $aggregate = $this->dic->getEntryRepository()->getAllObjectsAggregate($this->event->getParameterByKey("listId"), $this->event->getParameterByKey("sorting"));
        $this->event->getResponse()->setDataByKey('allOrganizationsAggregate', $aggregate);
        return $this->event->getResponse();
        
    }
    
    public function getIsDeletableSpecification()
    {
        $this->event->getResponse()->setDataByKey('isDeletableSpecification', \lwListtool\Domain\Entry\Specification\isDeletable::getInstance());
        return $this->event->getResponse();
    }
    
    public function add()
    {
        try {
            $array = $this->event->getDataByKey('postArray');
            $array['opt1file'] = $this->event->getDataByKey('opt1file');
            $array['opt2file'] = $this->event->getDataByKey('opt2file');
            $dataValueObject = new \LWddd\ValueObject($array);
            $result = $this->dic->getEntryRepository()->saveObject(false, $this->event->getParameterByKey("listId"), $dataValueObject);
            $this->event->getResponse()->setParameterByKey('saved', true);
        }
        catch (\LWddd\validationErrorsException $e) {
            $this->event->getResponse()->setDataByKey('error', $e->getErrors());
            $this->event->getResponse()->setParameterByKey('error', true);
        }
        return  $this->event->getResponse();
    }
}