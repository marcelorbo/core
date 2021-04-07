<?php

namespace Estartar\Core;

class Pagination
{
    private $start;
    private $length;
    private $records;
    private $url;        

    private $pages;
    private $html;

    public function __construct($start, $length, $records, $url, $template = null)
    {
        $this->start = $start;
        $this->length = $length;
        $this->records = $records;
        $this->url = CONFIG["BASEURL"]. $url;

        ob_start();
        include './views/Templates/'. (!empty($template) ? $template : 'Pagination') . '.htm';
        $this->html = ob_get_clean();
    }

    public function Render()
    {
        if($this->records <= $this->length) {
            return "";
            exit;
        }

        $this->pages = ceil($this->records / $this->length);

        /* ----------------- */
        /* previous buttom
        /* ----------------- */        
        if($this->start == 1) {
            $this->html = str_replace("{{previous-disabled}}", "disabled", $this->html);
        }

        /* ----------------- */        
        /* next buttom
        /* ----------------- */        
        if($this->start == $this->pages) {
            $this->html = str_replace("{{next-disabled}}", "disabled", $this->html);
        }        

        /* ----------------- */
        /* {{page-itens}}
        /* ----------------- */      
        $itens = "";          
        for ($i = 1; $i <= $this->pages; $i++) { 
            $itens .= ($i != $this->start) ? "<li class=\"page-item\">" : "<li class=\"page-item active\">";
            $itens .= ($i != $this->start) ? "<a class=\"page-link post\" href=\"{$this->url}/page/{$i}\">{$i} <span class=\"sr-only\">(current)</span></a>" : "<a class=\"page-link\">{$i} <span class=\"sr-only\">(current)</span></a>";
            $itens .= "</li>";
        }
        $this->html = str_replace("{{page-itens}}", $itens, $this->html);

        return $this->html;
    }


}