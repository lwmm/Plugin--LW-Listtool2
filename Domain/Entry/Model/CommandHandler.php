<?php

namespace lwListtool\Domain\Entry\Model;

class CommandHandler
{
    private $db;
    
    public function __construct($db)
    {
        $this->db = $db;
    }

    public function addEntity($listId, $array)
    {
        $this->db->setStatement("INSERT INTO t:lw_master ( lw_object, category_id, name, description, published, opt1bool, opt1number, opt2number, opt1text, opt2text, opt3text, lw_first_date, lw_last_date ) VALUES ( 'lw_listtool2', :listid, :name, :description, :published, :opt1bool, :opt1number, :opt2number, :opt1text, :opt2text, :opt3text, :firstdate, :lastdate ) ");
        $this->db->bindParameter("listid", 'i', $listId);
        $this->db->bindParameter("name", 's', $array['name']);
        $this->db->bindParameter("description", 's', $array['description']);
        $this->db->bindParameter("published", 's', $array['published']);
        $this->db->bindParameter("opt1bool", 's', $array['opt1bool']);
        $this->db->bindParameter("opt1number", 's', $array['opt1number']);
        $this->db->bindParameter("opt2number", 's', $array['opt2number']);
        $this->db->bindParameter("opt1text", 's', $array['opt1text']);
        $this->db->bindParameter("opt2text", 's', $array['opt2text']);
        $this->db->bindParameter("opt3text", 's', $array['opt3text']);
        $this->db->bindParameter("firstdate", 's', date("YmdHis"));
        $this->db->bindParameter("lastdate", 's', date("YmdHis"));
        return $this->db->pdbinsert($this->db->gt('lw_master'));
    }
    
    public function saveEntity($id, $array)
    {
        $this->db->setStatement("UPDATE t:lw_master SET name = :name, description = :description, published = :published, opt1number = :opt1number, opt2number = :opt2number, opt1text = :opt1text, opt2text = :opt2text, opt3text = :opt3text, lw_last_date = :lastdate WHERE id = :id ");
        $this->db->bindParameter("id", 'i', $id);
        $this->db->bindParameter("name", 's', $array['name']);
        $this->db->bindParameter("description", 's', $array['description']);
        $this->db->bindParameter("published", 's', $array['published']);
        $this->db->bindParameter("opt1number", 's', $array['opt1number']);
        $this->db->bindParameter("opt2number", 's', $array['opt2number']);
        $this->db->bindParameter("opt1text", 's', $array['opt1text']);
        $this->db->bindParameter("opt2text", 's', $array['opt2text']);
        $this->db->bindParameter("opt3text", 's', $array['opt3text']);
        $this->db->bindParameter("lastdate", 's', date("YmdHis"));
        return $this->db->pdbquery();
    }
    
    public function deleteEntity($id, $listId)
    {
        $this->db->setStatement("DELETE FROM t:lw_master WHERE id = :id AND category_id = :listid ");
        $this->db->bindParameter("id", 'i', $id);
        $this->db->bindParameter("listid", 'i', $listId);
        return $this->db->pdbquery();
    }
}