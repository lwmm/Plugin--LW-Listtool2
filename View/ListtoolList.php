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

namespace lwListtool\View;

class ListtoolList extends \LWmvc\View
{
    public function __construct()
    {
        parent::__construct('edit');
        $this->dic = new \lwListtool\Services\dic();
        $this->systemConfiguration = $this->dic->getConfiguration();
        $this->auth = $this->dic->getLwAuth();
        $this->inAuth = $this->dic->getLwInAuth();
    }

    public function setListRights($rights)
    {
        $this->listRights = $rights;
    }
    
    public function setConfiguration($configuration)
    {
        $this->configuration = $configuration;
    }
    
    public function init()
    {
        if (filter_var($this->configuration->getValueByKey('template'), FILTER_VALIDATE_INT)) {
            $template = $base . $this->repository->loadTemplateById($this->configuration->getValueByKey('template'));
        }
        else {
            $template = \lw_io::loadFile(dirname(__FILE__).'/templates/'.$this->configuration->getValueByKey('template'));
        }        
        $this->view = new \lw_te($template);
    }    
    
    public function render()
    {
        $this->view->reg("listtitle", $this->configuration->getValueByKey('name'));
        if ($this->listRights->isReadAllowed()) {
            $this->view->setIfVar('ltRead');
        }
        if ($this->listRights->isWriteAllowed()) {
            $this->view->setIfVar('ltWrite');
        }
        
        $blocktemplate = $this->view->getBlock("entry");
        foreach($this->view->aggregate as $entry)
        {
            $this->view->setIfVar('entries');
            $btpl = new \lw_te($blocktemplate);
            if ($entry->isLink()) {
                $btpl->setIfVar('link');
                $btpl->reg("opt3text", $entry->getValueByKey("opt3text"));
            }
            if ($entry->isFile() && $entry->hasFile()) {
                $btpl->setIfVar('file');
                $btpl->reg("downloadurl", \lw_page::getInstance()->getUrl(array("cmd"=>"downloadEntry", "id"=>$entry->getValueByKey("id"))));
            }
            $btpl->reg("deleteurl", \lw_page::getInstance()->getUrl(array("cmd"=>"deleteEntry", "id"=>$entry->getValueByKey("id"))));
            $btpl->reg("id", $entry->getValueByKey("id"));
            $btpl->reg("name", $entry->getValueByKey("name"));
            $btpl->reg("lw_first_date", $entry->getFirstDate());
            $btpl->reg("published", $entry->getValueByKey("published"));
            if ($this->listRights->isReadAllowed()) {
                $btpl->setIfVar('ltRead');
            }
            if ($this->listRights->isWriteAllowed()) {
                $btpl->setIfVar('ltWrite');
            }
            if ($this->configuration->getValueByKey('borrow') == 1) {
                if ($entry->isBorrowed()) {
                    if ($this->auth->isLoggedIn() || $entry->isBorrower($this->inAuth->getUserdata("id"))) {
                        $btpl->setIfVar('showEditOptions');
                        $btpl->setIfVar('showReleaseLink');
                        $btpl->reg("releaseurl", \lw_page::getInstance()->getUrl(array("cmd"=>"releaseEntry", "id"=>$entry->getValueByKey("id"))));
                    }
                    else  {
                        $btpl->setIfVar('borrowed');
                        $btpl->reg('borrower', $entry->getBorrowerName().' <!-- borrower_id: '.$entry->getBorrowerId().' --> ');
                    }
                }
                else {
                    $btpl->setIfVar('borrow');
                    $btpl->reg("borrowurl", \lw_page::getInstance()->getUrl(array("cmd"=>"borrowEntry", "id"=>$entry->getValueByKey("id"))));
                }
            }
            else {
                $btpl->setIfVar('showEditOptions');
            }
            $bout.= $btpl->parse();
        }
        
        if ($this->configuration->getValueByKey('sorting') == "opt1number" && $this->listRights->isWriteAllowed()) {
            $this->view->setIfVar("manualsorting");
        }   
        
        $this->view->putBlock("entry", $bout);
        $listtoolbase = new \lwListtool\View\ListtoolBase();
        return $listtoolbase->render()."\n".$this->view->parse();
    }
}