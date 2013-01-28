<?php

namespace lwListtool\View;

class ConfigurationForm extends \LWmvc\View
{
    public function __construct($type)
    {
        parent::__construct('edit');
        $this->dic = new \lwListtool\Services\dic();
        $this->view = new \lw_view(dirname(__FILE__).'/templates/Form.tpl.phtml');
        $this->systemConfiguration = $this->dic->getConfiguration();
    }

    public function render()
    {
        $this->view->actionUrl = $this->systemConfiguration['url']['client']."admin.php?obj=content&cmd=open&oid=".$this->entity->getId()."&pcmd=save";
        $this->view->backUrl = $this->systemConfiguration['url']['client']."admin.php?obj=content";
        $this->entity->renderView($this->view);
        return $this->view->render();    
    }
}