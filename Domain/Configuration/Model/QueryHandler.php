<?php

namespace lwListtool\Domain\Configuration\Model;

class QueryHandler 
{
    public function __construct(\lw_db $db)
    {
        $this->db = $db;
        $this->table = 'lw_master';
        $this->type = "lw_organisation";
        $this->dic = new \lwListtool\Services\dic();
    }
    
    public function setPluginRepositroy($repository)
    {
        $this->pluginRepository = $repository;
    }
    
    public function loadObjectById($id)
    {
        $data = $this->pluginRepository->loadPluginData('lw_listtool2', $id);
        return $data['parameter'];
    }
}