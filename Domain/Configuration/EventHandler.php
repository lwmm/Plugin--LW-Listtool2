<?php

namespace lwListtool\Domain\Configuration;

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

    protected function getIsDeletableSpecification()
    {
        $this->event->getResponse()->setDataByKey('isDeletableSpecification', \lwListtool\Domain\Configuration\Specification\isValid::getInstance());
        return $this->event->getResponse();
    }
    
    protected function getConfigurationEntityFromArray()
    {
        $dataValueObject = new \LWddd\ValueObject($this->event->getDataByKey('postArray'));
        $entity = \lwOrganizations\Domain\Organization\Model\Factory::getInstance()->buildNewObjectFromValueObject($dataValueObject);
        $this->event->getResponse()->setDataByKey('ConfigurationEntity', $entity);
        return $this->event->getResponse();
    }
    
    protected function getConfigurationEntityById()
    {
        $entity = $this->dic->getConfigurationRepository()->getObjectById($this->event->getParameterByKey('id'));
        $this->event->getResponse()->setDataByKey('ConfigurationEntity', $entity);
        return $this->event->getResponse();        
    }
    
    public function save()
    {
        try {
            $dataValueObject = new \LWddd\ValueObject($this->event->getDataByKey('postArray'));
             $result = $this->dic->getConfigurationRepository()->saveObject($this->event->getParameterByKey('id'), $dataValueObject);
            $this->event->getResponse()->setParameterByKey('saved', true);
        }
        catch (\LWddd\validationErrorsException $e) {
            $this->event->getResponse()->setDataByKey('error', $e->getErrors());
            $this->event->getResponse()->setParameterByKey('error', true);
        }        
        return $this->event->getResponse();
    }
    
    protected function deleteById()
    {
        try {
            $this->dic->getRepository()->deleteObjectById($this->event->getParameterByKey('id'));
            $this->event->getResponse()->setParameterByKey('deleted', true);
        }
        catch (\Exception $e) {
            $this->event->getResponse()->setDataByKey('errorMessage', $e->getMessage());
            $this->event->getResponse()->setParameterByKey('error', true);
        }        
        return $this->event->getResponse();
    }    
}