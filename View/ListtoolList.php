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
    
    public function setListId($id)
    {
        $this->listId = $id;
    }
    
    public function init()
    {
        if (filter_var($this->configuration->getValueByKey('template'), FILTER_VALIDATE_INT)) {
            die("page templates are not implemented yet"); //$template = $base . $this->repository->loadTemplateById($this->configuration->getValueByKey('template'));
        }
        else {
            $template = \lw_io::loadFile(dirname(__FILE__).'/listTemplates/'.$this->configuration->getValueByKey('template'));
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
            
            if ($this->configuration->getValueByKey('showName') == 1) {
                $btpl->setIfVar("showname");
                $btpl->reg("name", $entry->getValueByKey("name"));
            }
            
            if ($this->configuration->getValueByKey('showDate') == 1) {
                $btpl->setIfVar("showdate");
                $btpl->reg("lw_first_date", $entry->getFirstDate());
                if ($this->configuration->getValueByKey('showTime') == 1) {
                    $btpl->setIfVar("showtime");
                    $btpl->reg("lw_first_time", $entry->getFirstTime());
                }
            }
            
            if ($this->configuration->getValueByKey('showDescription') == 1) {
                $btpl->setIfVar("showdescription");
                $btpl->reg("description", html_entity_decode($entry->getValueByKey('description')));
            }
            
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
            
            if ($this->configuration->getValueByKey('showId') == 1) {
                $btpl->setIfVar("showid");
            }   
            
            if ($this->configuration->getValueByKey('showUser') == 1) {
                $btpl->setIfVar("showuser");
                $btpl->reg("last_username", "username");
            }   
            
            if ($this->configuration->getValueByKey('linktype') == 1) {
                $btpl->setIfVar("columnlink");
            }   
            else {
                $btpl->setIfVar("namelink");
            }
            
            if ($this->configuration->getValueByKey('publishedoption') == 0) {
                $btpl->setIfVar("columnpublished");
            }   
            else {
                if ($entry->getValueByKey('published') == 0) {
                    $btpl->setIfVar("rowcolorpublished");
                }
            }
            
            if ($this->configuration->getValueByKey('language') == "de") {
                $this->setGermanTexts($btpl);
            }
            else {
                $this->setEnglishTexts($btpl);
            }
            
            $bout.= $btpl->parse();
        }
        
        if ($this->configuration->getValueByKey('showName') == 1) {
            $this->view->setIfVar("showname");
        }
        
        if ($this->configuration->getValueByKey('showDate') == 1) {
            $this->view->setIfVar("showdate");
        }
        
        if ($this->configuration->getValueByKey('showDescription') == 1) {
            $this->view->setIfVar("showdescription");
        }
        
        if ($this->configuration->getValueByKey('showId') == 1) {
            $this->view->setIfVar("showid");
        }   
        
        if ($this->configuration->getValueByKey('showUser') == 1) {
            $this->view->setIfVar("showuser");
        }   
        
        if ($this->configuration->getValueByKey('publishedoption') == 0) {
            $this->view->setIfVar("columnpublished");
        }   
        
        if ($this->configuration->getValueByKey('sorting') == "opt1number" && $this->listRights->isWriteAllowed()) {
            $this->view->setIfVar("manualsorting");
        }   
        
        if ($this->configuration->getValueByKey('showcss') == 1) {
            $this->view->setIfVar("showcss");
        }   
        
        if ($this->configuration->getValueByKey('linktype') == 1) {
            $this->view->setIfVar("columnlink");
        }   
        else {
            $this->view->setIfVar("namelink");
        }
        if ($this->configuration->getValueByKey('language') == "de") {
            $this->setGermanTexts($this->view);
        }
        else {
            $this->setEnglishTexts($this->view);
        }
        
        $this->view->reg("listId", $this->listId);
        $this->view->putBlock("entry", $bout);
        $listtoolbase = new \lwListtool\View\ListtoolBase();
        return $listtoolbase->render()."\n".$this->view->parse();
    }
    
    protected function setEnglishTexts($tpl)
    {
        $tpl->reg("lang_newfile", "add new file");
        $tpl->reg("lang_newlink", "add new link");
        $tpl->reg("lang_sortlist", "sort list");
        if ($this->configuration->getValueByKey('title_name')) {
            $tpl->reg("lang_name", $this->configuration->getValueByKey('title_name'));
        }
        else {
            $tpl->reg("lang_name", "Name");
        }
        $tpl->reg("lang_date", "Date");
        $tpl->reg("lang_published", "Published");
        if ($this->configuration->getValueByKey('title_description')) {
            $tpl->reg("lang_description", $this->configuration->getValueByKey('title_description'));
        }
        else {
            $tpl->reg("lang_description", "Description");
        }
        $tpl->reg("lang_user", "User");
        $tpl->reg("lang_link", "Link");
        $tpl->reg("lang_download", "Download");
        $tpl->reg("lang_edit", "edit");
        $tpl->reg("lang_delete", "delete");
        $tpl->reg("lang_release", "release");
        $tpl->reg("lang_borrow", "borrow");
        $tpl->reg("lang_reallydelete", "really delete?");
        $tpl->reg("lang_borrowedby", "borrowed by");
        $tpl->reg("lang_noentries", "no entries available");
    }

    protected function setGermanTexts($tpl)
    {
        $tpl->reg("lang_newfile", "neue Datei anlegen");
        $tpl->reg("lang_newlink", "neuen Link anlegen");
        $tpl->reg("lang_sortlist", "Liste sortieren");
        if ($this->configuration->getValueByKey('title_name')) {
            $tpl->reg("lang_name", $this->configuration->getValueByKey('title_name'));
        }
        else {
            $tpl->reg("lang_name", "Name");
        }
        $tpl->reg("lang_date", "Datum");
        $tpl->reg("lang_published", "ver&ouml;ffentlicht");
        if ($this->configuration->getValueByKey('title_description')) {
            $tpl->reg("lang_description", $this->configuration->getValueByKey('title_description'));
        }
        else {
            $tpl->reg("lang_description", "Beschreibung");
        }
        $tpl->reg("lang_user", "Benutzer");
        $tpl->reg("lang_link", "Verlinkung");
        $tpl->reg("lang_download", "Herunterladen");
        $tpl->reg("lang_edit", "bearbeiten");
        $tpl->reg("lang_delete", "l&oumlschen");
        $tpl->reg("lang_release", "zur&uuml;ckgeben");
        $tpl->reg("lang_borrow", "ausleihen");
        $tpl->reg("lang_reallydelete", "wirklich l&ouml;schen?");
        $tpl->reg("lang_borrowedby", "ausgeliehen von");
        $tpl->reg("lang_noentries", "Es liegen keine Eintr&auml;ge vor.");
    }
}