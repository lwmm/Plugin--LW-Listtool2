<?php

namespace lwListtool\Domain\Configuration\Model;

class Repository extends \LWddd\Repository
{
    public function __construct()
    {
        parent::__construct();
        $this->dic = new \lwListtool\Services\dic();
        $this->baseNamespace = "lwListtool\\Domain\\Configuration\\";
    }
    
    protected function getQueryHandler()
    {
        if (!$this->queryHandler) {
            $class = $this->baseNamespace.'Model\\QueryHandler';
            $this->queryHandler = new $class($this->dic->getDbObject());
            $this->queryHandler->setPluginRepositroy($this->dic->getPluginRepository());
        }
        return $this->queryHandler;
    }    
    
    protected function getCommandHandler()
    {
        if (!$this->commandHandler) {
            $class = $this->baseNamespace.'Model\\CommandHandler';
            $this->commandHandler = new $class($this->dic->getDbObject());
            $this->commandHandler->setPluginRepositroy($this->dic->getPluginRepository());
        }
        return $this->commandHandler;
    }    
    
    protected function buildObjectById($id)
    {
        return new \lwListtool\Domain\Configuration\Object\configuration($id);
    }
    
    public function saveObject($id, $dataValueObject)
    {
        $parameter['name'] = $dataValueObject->getValueByKey("name");
        $parameter['listtooltype'] = $dataValueObject->getValueByKey("listtooltype");
        $parameter['template'] = $dataValueObject->getValueByKey("template");
        $parameter['sorting'] = $dataValueObject->getValueByKey("sorting");
        $parameter['suffix_type'] = $dataValueObject->getValueByKey("suffix_type");
        $parameter['suffix'] = $dataValueObject->getValueByKey("suffix");
        $parameter['secured'] = $dataValueObject->getValueByKey("secured");
        $parameter['language'] = $dataValueObject->getValueByKey("language");
        $content = false;
        return $this->getCommandHandler()->savePluginData($id, $parameter, $content);
    }
}