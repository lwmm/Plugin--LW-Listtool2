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
        return false;
    }
    
    public function isFile()
    {
        return true;
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
}