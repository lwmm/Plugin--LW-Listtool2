<?php

/**************************************************************************
*  Copyright notice
*
*  Copyright 2013 Logic Works GmbH
*
*  Licensed under the Apache License, Version 2.0 (the "License");
*  you may not use this file except in compliance with the License.
*  You may obtain a copy of the License at
*
*  http://www.apache.org/licenses/LICENSE-2.0
*  
*  Unless required by applicable law or agreed to in writing, software
*  distributed under the License is distributed on an "AS IS" BASIS,
*  WITHOUT WARRANTIES OR CONDITIONS OF ANY KIND, either express or implied.
*  See the License for the specific language governing permissions and
*  limitations under the License.
*  
***************************************************************************/

namespace lwListtool\Controller;

class FrontendController extends \LWmvc\Controller
{
    public function __construct()
    {
        parent::__construct("\\lwListtool\\", "showListAction");
        $this->response = $this->dic->getLwResponse();
    }
    
    public function setConfigurationId($id)
    {
        $this->configurationId = intval($id);
    }

    public function init()
    {
        $response = $this->executeDomainEvent('Configuration', 'getConfigurationEntityById', array("id"=>$this->configurationId));
        $this->listConfig = $response->getDataByKey('ConfigurationEntity');
        
        $response = $this->executeDomainEvent('ListRights', 'getListRightsObject', array("listId"=>$this->configurationId, "listConfig"=>$this->listConfig));
        $this->listRights = $response->getDataByKey('rightsObject');
    }
    
    protected function showListAction($error = false)
    {
        if ($this->listRights->isReadAllowed()) {
            $this->response->useJQuery();
            $this->response->useJQueryUI();

            $view = new \lwListtool\View\ListtoolList();
            $view->setConfiguration($this->listConfig);
            $view->setListRights($this->listRights);
            $view->setListId($this->configurationId);
            $view->init();
        
            $response = $this->executeDomainEvent('Entry', 'getListEntriesAggregate', array("configuration"=>$this->listConfig, "listId"=>$this->configurationId, "listRights"=>$this->listRights));
            $view->setAggregate($response->getDataByKey('listEntriesAggregate'));

            $response = $this->executeDomainEvent('Entry', 'getIsDeletableSpecification');
            $view->setIsDeletableSpecification($response->getDataByKey('isDeletableSpecification'));
            return $this->returnRenderedView($view);    
        }
        else {
            $response = \LWddd\Response::getInstance();
            $response->setOutputByKey('output', "<!-- Listtool not allowed -->");
            return $response;           
        }
     }
     
     protected function addEntryAction()
     {
        if ($this->listRights->isWriteAllowed()) {

            $response = $this->executeDomainEvent('Entry', 'add', array("listId"=>$this->configurationId, "configuration" => $this->listConfig), array('postArray'=>$this->request->getPostArray(), 'opt1file'=>$this->request->getFileData('opt1file'), 'opt2file'=>$this->request->getFileData('opt2file')));
            if ($response->getParameterByKey("error")) {
                if ($this->request->getAlnum("type") == "file") {
                    return $this->showAddFileFormAction($response->getDataByKey("error"));
                } 
                else {
                    return $this->showAddLinkFormAction($response->getDataByKey("error"));
                }
            }
            return $this->buildReloadResponse(array("cmd"=>"showList", "reloadParent"=>1));
        }
     }

     protected function showEditEntryFormAction($error=false)
     {
         if ($this->listRights->isWriteAllowed()) {
            $formView = new \lwListtool\View\EntryForm('edit');
            if ($error) {
                $response = $this->executeDomainEvent('Entry', 'getEntryEntityFromPostArray', array(), array("postArray"=>$this->request->getPostArray()));
            }
            else {
                $response = $this->executeDomainEvent('Entry', 'getEntryEntityById', array("id"=>$this->request->getInt("id"), "listId"=>$this->configurationId));
            }
            $entity = $response->getDataByKey('EntryEntity');
            $entity->setId($this->request->getInt("id"));
            $formView->setEntity($entity);
            $formView->setConfiguration($this->listConfig);
            if ($entity->isFile()) {
                $formView->setEntryType('file');
            }
            else {
                $formView->setEntryType('link');
            }
            $formView->setErrors($error);
            $response = $this->returnRenderedView($formView);
            $response->setParameterByKey('die', 1);
            return $response;
        }
     }
     
    protected function saveEntryAction()
    {
       if ($this->listRights->isWriteAllowed()) {
           $response = $this->executeDomainEvent('Entry', 'save', array("id"=>$this->request->getInt("id"), "listId"=>$this->configurationId, "configuration" => $this->listConfig), array('postArray'=>$this->request->getPostArray(), 'opt1file'=>$this->request->getFileData('opt1file'), 'opt2file'=>$this->request->getFileData('opt2file')));
           if ($response->getParameterByKey("error")) {
               return $this->showEditEntryFormAction($response->getDataByKey("error"));
           }
           return $this->buildReloadResponse(array("cmd"=>"showList", "reloadParent"=>1));
       }
    }
     
    protected function showAddFileFormAction($error=false)
    {
       if ($this->listRights->isWriteAllowed()) {
           return $this->buildAddForm('file', $error);
       }
    }
     
    protected function showAddLinkFormAction($error=false)
    {
        if ($this->listRights->isWriteAllowed()) {
            return $this->buildAddForm('link', $error);
        }
    }
     
    protected function buildAddForm($type, $error=false)
    {
       if ($this->listRights->isWriteAllowed()) {
           $formView = new \lwListtool\View\EntryForm("add");
           $response = $this->executeDomainEvent('Entry', 'getEntryEntityFromPostArray', array(), array("postArray"=>$this->request->getPostArray()));
           $formView->setConfiguration($this->listConfig);
           $formView->setEntity($response->getDataByKey('EntryEntity'));
           $formView->setEntryType($type);
           $formView->setErrors($error);
           $response = $this->returnRenderedView($formView);
           $response->setParameterByKey('die', 1);
           return $response;
       }
    }
     
    protected function deleteEntryAction()
    {
       if ($this->listRights->isWriteAllowed()) {
           $response = $this->executeDomainEvent('Entry', 'delete', array("id"=>$this->request->getInt("id"), "listId"=>$this->configurationId));
           return $this->buildReloadResponse(array("cmd"=>"showList"));
       }
    }
     
    protected function borrowEntryAction()
    {
       if ($this->listRights->isWriteAllowed()) {
           $response = $this->executeDomainEvent('Entry', 'borrow', array("id"=>$this->request->getInt("id"), "listId"=>$this->configurationId, "borrowerId"=>$this->dic->getLwInAuth()->getUserdata("id")));
           return $this->buildReloadResponse(array("cmd"=>"showList"));
       }
    }
     
    protected function releaseEntryAction()
    {
       if ($this->listRights->isWriteAllowed()) {
           $response = $this->executeDomainEvent('Entry', 'release', array("id"=>$this->request->getInt("id"), "listId"=>$this->configurationId, "borrowerId"=>$this->dic->getLwInAuth()->getUserdata("id")));
           return $this->buildReloadResponse(array("cmd"=>"showList"));
       }
    }
     
    protected function showThumbnailAction()
    {
       if ($this->listRights->isReadAllowed()) {
           $response = $this->executeDomainEvent('Entry', 'getEntryEntityById', array("id"=>$this->request->getInt("id"), "listId"=>$this->configurationId));
           $file = $response->getDataByKey('EntryEntity')->getThumbnailPath();
           if (is_file($file)) {
               header("Content-type: ".\lw_io::getMimeType(\lw_io::getFileExtension($file)));
               readfile($file);
               exit();
           }
           die("not existing");
       }
       die("not allowed");
    }
     
    public function downloadEntryAction()
    {
        if ($this->listRights->isReadAllowed()) {
            $response = $this->executeDomainEvent('Entry', 'getEntryEntityById', array("id"=>$this->request->getInt("id"), "listId"=>$this->configurationId));
            $entity = $response->getDataByKey('EntryEntity');
            $file = $entity->getFilePath();
            if (is_file($file)) {
                $extension = \lw_io::getFileExtension($data['opt2file']);
                $mimeType = \lw_io::getMimeType($extension);
                if (strlen($mimeType) < 1) {
                    $mimeType = "application/octet-stream";
                }
                header("Pragma: public");
                header("Expires: 0");
                header("Cache-Control: must-revalidate, post-check=0, pre-check=0");
                header("Content-Type: " . $mimeType);
                header("Content-disposition: attachment; filename=\"".$entity->getValueByKey('opt2file')."\"");
                readfile($file);
                exit();
            }
            die("not existing");
        }
        die("not allowed");
    }
    
    public function sortEntriesAction()
    {
        if ($this->listRights->isWriteAllowed()) {
            $this->response->useJQuery();
            $this->response->useJQueryUI();

            $view = new \lwListtool\View\Sortlist();
            $view->setConfiguration($this->listConfig);
            $view->setListRights($this->listRights);
            $view->init();
        
            $response = $this->executeDomainEvent('Entry', 'getListEntriesAggregate', array("configuration"=>$this->listConfig, "listId"=>$this->configurationId, "listRights"=>$this->listRights));
            $view->setAggregate($response->getDataByKey('listEntriesAggregate'));

            $response = $this->returnRenderedView($view);
            $response->setParameterByKey('die', 1);
            return $response;
        }
        else {
           die("not allowed");
        }
    }
    
    public function saveSortingAction()
    {
        if ($this->listRights->isWriteAllowed()) {
            $response = $this->executeDomainEvent('Entry', 'sort', array("listId"=>$this->configurationId), array("postArray" => $this->request->getPostArray()));
            return $this->buildReloadResponse(array("cmd"=>"showList", "reloadParent"=>1));
        }
    }
}