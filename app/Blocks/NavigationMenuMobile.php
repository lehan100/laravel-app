<?php

namespace App\Blocks;

use App\Repositories\Category\CategoryEloquentRepository;

class NavigationMenuMobile
{

    protected $menu;
    protected $data;
    protected $configPath;
    protected $mainModel;
    protected $numberRow;

    public function __construct()
    {
        $this->mainModel = new CategoryEloquentRepository();
        $this->configPath = config('image.path.category');
        $this->numberRow = 3;
    }

    public function getData()
    {
        $listItems = $this->mainModel->listItems(['position' => 'position_menu'], ['task' => "frontend-list-items-positions"]);
        $this->data = $listItems;
    }

    public function generate($idClass = 'main-menu')
    {
        $this->getData();
        $categoryRoot = $this->data->filter(function ($d) {
            return $d['parent_id'] == 0;
        });
        $this->menu .= sprintf('<ul class="%s">', $idClass);
        $start = true;
        foreach ($categoryRoot as $val) {
            if ($start == true) {
                $this->menu .= '<li class="active">';
                $start = false;
            } else {
                $this->menu .= '<li>';
            }

            $this->menu .= sprintf('<div class="dropdown"><a href="javascript:;">%s</a></div>', $val['name']);
            $categoryParents = $this->data->filter(function ($d) use ($val) {
                return $d['parent_id'] == $val->id;
            });
            if (count($categoryParents) > 0) {
                $this->Recursive($categoryParents, url($val->url['path']), $val['name']);
            }
            $this->menu .= '</li>';
        }
        $this->menu .= '</ul>';
        return $this->menu;
    }

    public function Recursive($dataParent, $url = '', $name = '')
    {
        if (count($dataParent) > 0) {
            $this->menu .= '<div class="submenu">';
            $this->menu .= sprintf('<p class="submenu-title">%s<a href="%s">Xem tất cả</a></p>', $name ,$url);
            foreach ($dataParent as $val) {
                $picture = $val->picture;
                $pictureUrl = ($picture != "") ? asset($this->configPath['path'] . '/' . $picture) : "";
                $this->menu .= sprintf(' <a href="%s"><figure><img class="lazy-menu" alt="%s" width="48" height="48px" src="%s"></figure>%s</a>', url($val->url['path']),$val['name'], $pictureUrl,  $val['name']);
            }
            $this->menu .= '</div>';
        }
    }
}
