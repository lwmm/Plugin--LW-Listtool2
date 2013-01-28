<?php

namespace lwListtool\Domain\Configuration\Model;

class CommandHandler
{
    private $db;
    private $pluginRepository;
    
    public function __construct($db)
    {
        $this->db = $db;
    }

    public function setPluginRepositroy($repository)
    {
        $this->pluginRepository = $repository;
    }

    public function savePluginData($id, $parameter, $content)
    {
        return $this->pluginRepository->savePluginData('lw_listtool2', $id, $parameter, $content);
    }
}