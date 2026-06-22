<?php

require_once "models/Menu.php";

class MenuController {
     private $menu;

    public function __construct()
    {
        $this->menu = new Menu();
    }

    public function getMenu(){
        return $this->menu->getAll();
    }
}