<?php

namespace lwListtool\Domain\Entry\Object;

class entry extends \LWddd\Entity
{
    public function __construct($id=false)
    {
        parent::__construct($id);
    }
    
    public function renderView($view)
    {
        $view->entity = $this;
    }
    
    public function isLink()
    {
        if ($this->getValueByKey('opt1bool') == 1 && $this->isLoaded()) {
            return true;
        }
        return false;
    }
    
    public function isFile()
    {
        if ($this->getValueByKey('opt1bool') != 1 && $this->isLoaded()) {
            return true;
        }
        return false;
    }
    
    public function hasFile()
    {
        return false;
    }
    
    public function hasLastDate()
    {
        return false;
    }
    
    public function hasFirstDate()
    {
        return false;
    }
    
    public function hasUsername()
    {
        return false;
    }
    
    public function hasThumbnail()
    {
        return false;
    }
    
    public function getFirstDate()
    {
        return \lw_object::formatDate($this->getValueByKey('lw_first_date'));
    }
}