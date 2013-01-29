<?php

namespace lwListtool\View;

class EntryForm extends \LWmvc\View
{
    public function __construct($type)
    {
        parent::__construct('edit');
        $this->dic = new \lwListtool\Services\dic();
        $this->systemConfiguration = $this->dic->getConfiguration();
        $this->view = new \lw_view(dirname(__FILE__).'/templates/EntryForm.tpl.phtml');
    }

    public function setEntryType($type)
    {
        if ($type != "file") {
            $type = "link";
            $this->view->typeSwitch = "1";
        }
        else {
            $this->view->typeSwitch = "0";
        }
        $this->entryType = $type;
    }
    
    public function render()
    {
        $this->view->mediaUrl = $this->systemConfiguration['url']['media'];
        if ($this->entity->getId()<1) {
            $this->view->actionUrl = \lw_page::getInstance()->getUrl(array("cmd"=>"addEntry", "type" => $this->entryType));
            if ($this->entryType == "file") {
                $this->view->addFile = true;
            }
            else {
                $this->view->addLink = true;
            }
        }
        else {
            $this->view->actionUrl = \lw_page::getInstance()->getUrl(array("cmd"=>"saveEntry", "id"=>$this->entity->getId()));
        }
        $this->view->isWriteAllowed = true;
        $this->view->entry = $this->entity;
        $form = $this->view->render();
        $popupView = new \lwListtool\View\Popup();
        $popupView->setForm($form);
        return $popupView->render();
    }
}