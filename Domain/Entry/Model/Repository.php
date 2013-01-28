<?php

namespace lwListtool\Domain\Entry\Model;

class Repository extends \LWddd\Repository
{
    public function __construct()
    {
        parent::__construct();
        $this->dic = new \lwListtool\Services\dic();
        $this->baseNamespace = "lwListtool\\Domain\\Entry\\";
    }

    public function getAllObjectsAggregate($listId, $sorting)
    {
        $items = $this->getQueryHandler()->loadAllEntriesByListId($listId, $sorting);
        return $this->buildAggregateFromQueryResult($items);
    }
    
    protected function buildObjectById($id)
    {
        return new \lwListtool\Domain\Entry\Object\entry($id);
    }
    
    public function saveObject($id, $listId, $dataValueObject)
    {
        $DataValueObjectFiltered = $this->getDataValueObjectFilter()->filter($dataValueObject);
        $entity = $this->buildEntityFromFilteredValues($id, $DataValueObjectFiltered);

        $class = $this->baseNamespace."Specification\\isValid";
        $isValidSpecification = $class::getInstance();
        $isValidSpecification->setConfiguration($this->dic->getConfigurationByListId($listId));
        
        if ($isValidSpecification->isSatisfiedBy($entity)) {
            if ($entity->getId() > 0 ) {
                $result = $this->getCommandHandler()->saveEntity($entity->getId(), $entity->getValues());
                $id = $entity->getId();
            }
            else {
                $result = $this->getCommandHandler()->addEntity($listId, $entity->getValues());
                $id = $result;
            }
            $this->postSaveWork($result, $id, $entity);
            return $id;            
        }
        else {
            echo "<pre>";print_r($this->getIsValidSpecification()->getErrors());exit();
            die("b");
            $exception = new \LWddd\validationErrorsException('Error');
            $exception->setErrors($this->getIsValidSpecification()->getErrors());
            throw $exception;
        }        
    }
}