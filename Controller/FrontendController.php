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
    
    protected function showListAction($error = false)
    {
        $this->response->useJQuery();
        $this->response->useJQueryUI();
        
        $view = new \lwListtool\View\ListtoolList();

        $response = $this->executeDomainEvent('Configuration', 'getConfigurationEntityById', array("id"=>$this->configurationId));
        $configuration = $response->getDataByKey('ConfigurationEntity');
        $view->setConfiguration($configuration);
        $view->init();
        
        $response = $this->executeDomainEvent('Entry', 'getAllEntriesAggregate', array("configuration"=>$configuration, "listId"=>$this->configurationId));
        $view->setAggregate($response->getDataByKey('allEntriesAggregate'));

        $response = $this->executeDomainEvent('Entry', 'getIsDeletableSpecification');
        $view->setIsDeletableSpecification($response->getDataByKey('isDeletableSpecification'));
        
        return $this->returnRenderedView($view);    
     }
     
     protected function showAddFileFormAction()
     {
        $formView = new \lwListtool\View\EntryForm('add');

        $response = $this->executeDomainEvent('Entry', 'getEntryEntityFromArray', array(), array("postArray"=>$this->request->getPostArray()));
        $formView->setEntity($response->getDataByKey('EntryEntity'));
        $formView->setEntryType('file');
        $formView->setErrors($error);
        $response = $this->returnRenderedView($formView);
        $response->setParameterByKey('die', 1);
        return $response;
     }

     protected function addEntryAction()
     {
        $response = $this->executeDomainEvent('Entry', 'add', array("listId"=>$this->configurationId), array('postArray'=>$this->request->getPostArray(), 'opt1file'=>$this->request->getFileData('opt1file'), 'opt2file'=>$this->request->getFileData('opt2file')));
        if ($response->getParameterByKey("error")) {
            return $this->showAddFileFormAction($response->getDataByKey("error"));
        }
        return $this->buildReloadResponse(array("cmd"=>"showList", "response"=>1));
     }
     
     protected function showAddLinkFormAction()
     {
         die("addLink");
     }
     
}