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
        $this->db->bindParameter("name", 's', htmlentities($array['name'], ENT_QUOTES, "ISO-8859-1"));
        $this->db->bindParameter("description", 's', htmlentities($array['description'], ENT_QUOTES, "ISO-8859-1"));
        $this->db->bindParameter("published", 's', $array['published']);
        $this->db->bindParameter("opt1bool", 's', $array['op1bool']);
        $this->db->bindParameter("opt1number", 's', $array['opt1number']);
        $this->db->bindParameter("opt2number", 's', $array['opt2number']);
        $this->db->bindParameter("opt1text", 's', htmlentities($array['opt1text'], ENT_QUOTES, "ISO-8859-1"));
        $this->db->bindParameter("opt2text", 's', htmlentities($array['opt2text'], ENT_QUOTES, "ISO-8859-1"));
        $this->db->bindParameter("opt3text", 's', htmlentities($array['opt3text'], ENT_QUOTES, "ISO-8859-1"));
        $this->db->bindParameter("firstdate", 's', date("YmdHis"));
        $this->db->bindParameter("lastdate", 's', date("YmdHis"));
        die($this->db->prepare());
        
        return $this->db->pdbinsert($this->table);
    }
}