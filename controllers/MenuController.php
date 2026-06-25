<?php

require_once "models/Menu.php";

class MenuController {
    private $menu;

    public function __construct()
    {
        $this->menu = new Menu();
    }

    public function getMenu()
    {
        return $this->menu->getAll();
    }

    public function getMenuById($id)
    {
        return $this->menu->find($id);
    }

    public function createMenu($data)
    {
        return $this->menu->create($data);
    }

    public function updateMenu($id, $data)
    {
        return $this->menu->update($id, $data);
    }

    public function deleteMenu($id)
    {
        return $this->menu->delete($id);
    }
}