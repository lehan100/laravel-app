<?php

namespace App\Blocks;

use App\Repositories\Category\CategoryEloquentRepository;

class NavigationMenu {

    protected $menu;
    protected $data;
    protected $configPath;
    protected $mainModel;
    protected $numberRow;

    public function __construct() {
        $this->mainModel = new CategoryEloquentRepository();
        $this->configPath = config('image.path.category');
        $this->numberRow = 3;
    }

    public function getData() {
        $listItems = $this->mainModel->listItems(['position' => 'position_menu'], ['task' => "frontend-list-items-positions"]);
        $this->data = $listItems;
    }

    public function generate($idClass = 'mm-list') {
        $this->getData();
        $categoryRoot = $this->data->filter(function ($d) {
            return $d['parent_id'] == 0;
        });
        $this->menu .= sprintf('<ul id="%s">', $idClass);
        foreach ($categoryRoot as $val) {
            $this->menu .= '<li>';
            $this->menu .= sprintf('<a class="menu-clone" href="%s"><span>%s</span></a>', url($val->url['path']), $val['name']);
            $categoryParents = $this->data->filter(function ($d) use ($val) {
                return $d['parent_id'] == $val->id;
            });
            if (count($categoryParents) > 0) {
                $this->Recursive($categoryParents);
            }
            $this->menu .= '</li>';
        }
        $this->menu .= '</ul>';
        return $this->menu;
    }

    public function Recursive($dataParent) {
        if (count($dataParent) > 0) {
            $col = ceil(count($dataParent) / $this->numberRow);
            $this->menu .= sprintf('<div class="sub-menu col-%s">', $col);
            $this->menu .= '<ul>';
            foreach ($dataParent as $val) {
                $picture = $val->picture;
                $pictureUrl = ( $picture != "") ? asset($this->configPath['path'] . '/' . $picture) : "";
                $this->menu .= sprintf('<li class="mm-img"><a href="%s"><img src="%s" alt="%s" /><span>%s</span></a></li>', url($val->url['path']), $pictureUrl, $val['name'], $val['name']);
            }
            $this->menu .= '</ul>';
            $this->menu .= '</div>';
        }
    }
}
