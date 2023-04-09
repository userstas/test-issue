<?php

namespace App\Repository;

use App\Models\Event;
use App\Models\MenuItem;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\Model;

class MenuItemRepository
{

    protected $model;

    public function __construct(MenuItem $model)
    {
        $this->model = $model;
    }

    public function getMenuItems()
    {
        $menuItems =  $this->model->get();

        if ($menuItems->isNotEmpty()) {
            $menuItems->transform(function ($item, $key) use ($menuItems) {
                if(empty($item->parent_id)){
                    $item->children = $this->getChildren($item, $menuItems);
                }
                return $item;
            });
        }

        $menuItems = $menuItems->reject(function ($item){
            return !empty($item->parent_id);
        });


        return $menuItems;
    }

    private function getChildren($item, $menuItems){
        $items = [];
        foreach($menuItems as $menuItem){
            if($item->id == $menuItem->parent_id){
                $menuItem->children = $this->getChildren($menuItem, $menuItems);
                $items[] = $menuItem;
            }
        }
        return $items;
    }
}
