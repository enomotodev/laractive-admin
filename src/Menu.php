<?php

namespace Enomotodev\LaractiveAdmin;

class Menu
{
    /**
     * @var array
     */
    private $pages = [];

    /**
     * @return array
     */
    public function getPages()
    {
        return $this->pages;
    }

    /**
     * @param  array  $page
     * @return void
     */
    public function setPage($page)
    {
        $this->pages[] = $page;
    }
}
