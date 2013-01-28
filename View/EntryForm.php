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
        }
        $this->entryType = $type;
    }
    
    public function render()
    {
        $this->view->mediaUrl = $this->systemConfiguration['url']['media'];
        $this->view->actionUrl = \lw_page::getInstance()->getUrl(array("cmd"=>"addEntry"));
        $this->view->isWriteAllowed = true;
        $this->view->entry = $this->entity;
        $form = $this->view->render();
        $popupView = new \lwListtool\View\Popup();
        $popupView->setForm($form);
        return $popupView->render();
    }
}