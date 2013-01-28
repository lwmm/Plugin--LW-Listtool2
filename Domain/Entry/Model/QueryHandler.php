<?php

namespace lwListtool\Domain\Entry\Model;

class QueryHandler 
{
    public function __construct(\lw_db $db)
    {
        $this->db = $db;
        $this->table = 'lw_master';
        $this->type = "lw_listtool2";
    }
    
    public function loadAllEntriesByListId($listId, $sorting)
    {
        if (!$sorting) {
            $sorting = "name";
        }
        $this->db->setStatement("SELECT * FROM t:".$this->table." WHERE lw_object = :type AND category_id = :category ORDER BY :orderby ");
        $this->db->bindParameter("type", "s", $this->type);
        $this->db->bindParameter("category", "i", $listId);
        $this->db->bindParameter("orderby", "s", $sorting);
        return $this->db->pselect();
    }
}