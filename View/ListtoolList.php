<?php

namespace lwListtool\View;

class ListtoolList extends \LWmvc\View
{
    public function __construct($type)
    {
        parent::__construct('edit');
        $this->dic = new \lwListtool\Services\dic();
        $this->systemConfiguration = $this->dic->getConfiguration();
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
        $this->view->setIfVar('ltRead');
        $this->view->setIfVar('ltWrite');
        $listtoolbase = new \lwListtool\View\ListtoolBase();
        
        return $listtoolbase->render()."\n".$this->view->parse();
    }
}