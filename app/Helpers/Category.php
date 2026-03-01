<?php

namespace App\Helpers;

use Illuminate\Support\Arr;

class Category
{

    const page = array(
        0 => 'Trang chủ',
        2 => 'Sản phẩm',
        1 => 'Một bài viết',
        3 => 'Nhiều bài viết',
        4 => 'Liên hệ'
    );

    protected $menu = [];
    protected $data;
    protected $str;

    public static function getPage($page = 0)
    {
        if (isset(self::page[$page])) {
            return self::page[$page];
        }
        return false;
    }

    public static function getRoute($page = 0, $id = 0)
    {
        $route = null;
        if (Arr::exists(self::page, $page) && $id > 0) {
            switch ($page) {
                case '0':
                    $route = 'home';
                    break;
                case '1':
                    $route = 'news/detail/' . $id;
                    break;
                case '2':
                    $route = 'product/list/' . $id;
                    break;
                case '3':
                    $route = 'news/list/' . $id;
                    break;
                case '4':
                    $route = 'contact/index/' . $id;
                    break;
            }
        }
        return $route;
    }

    public static function getDataPage()
    {
        return self::page;
    }

    public static function generateDataSelector($data)
    {
        $result = null;
        if (count($data) > 0) {
            foreach ($data as $val) {
                $result[$val['id']] = $val['name'];
            }
        }
        return $result;
    }

    public static function generateSelector($data, $dataCategory = null)
    {
        $str = "";
        if (count($data) > 0) {
            $str .= "<div id='cat_ids' class='border p-3 overflow-auto' style='max-height:200px'>";
            foreach ($data as $val) {
                $str .= "<p class='mb-1'>";
                $findCategory = [];
                if ($dataCategory != null && count($dataCategory) > 0) {
                    $findCategory = $dataCategory->filter(function ($d) use ($val) {
                        return $d['id'] == $val['id'];
                    });
                }
                if (count($findCategory) > 0 && $val['has_sub'] == 1) {
                    $str .= sprintf('<input type="checkbox" disabled checked="true" value="%s" name="cat_id[]"> &nbsp;', $val['id']);
                } else if (count($findCategory) > 0) {
                    $str .= sprintf('<input type="checkbox" checked="true" value="%s" name="cat_id[]"> &nbsp;', $val['id']);
                } else if ($val['has_sub'] == 1) {
                    $str .= sprintf('<input type="checkbox" disabled value="%s" name="cat_id[]"> &nbsp;', $val['id']);
                } else {
                    $str .= sprintf('<input type="checkbox" value="%s" name="cat_id[]"> &nbsp;', $val['id']);
                }

                $str .= $val['name'];
                $str .= "</p>";
            }
            $str .= "</div>";
        }
        return $str;
    }

    public function generateDataId($data, $id = 0, $include_curents = true)
    {
        //        $data[] = $id;
        $tmpArr = array();
        if ($include_curents) {
            $tmpArr[] = (int)$id;
        }
        $this->data = $data;
        $categoryParents = $data->filter(function ($d) use ($id) {
            return $d['parent_id'] == $id;
        });

        $this->Recursive($categoryParents);
        foreach ($this->menu as $sub) {
            $tmpArr[] = $sub['id'];
        }
        //        $result = implode(',', $tmpArr);
        return $tmpArr;
    }

    public function generateAlias($data, $id = 0)
    {
        $tmpArr = array();
        $this->data = $data;
        $item = Arr::first($this->data, function ($d) use ($id) {
            return $d['id'] == $id;
        });
        $result = "";
        if ($item) {
            $tmpArr[] = $item['alias'];
            $this->RecursiveAlias($item);
            foreach ($this->menu as $sub) {
                if ($sub) {
                    $tmpArr[] = $sub['alias'];
                }
            }
            $tmpArr = array_reverse($tmpArr, true);
            $result = implode('/', $tmpArr);
        }
        return $result;
    }

    public function generateDataBreadcrumb($data, $id = 0)
    {
        $this->data = $data;
        $item =  $this->data->firstWhere('id',$id);
        // $item = Arr::first($this->data, function ($d) use ($id) {
        //     return $d['id'] == $id;
        // });
        if ($item) {
            $this->menu[] = $item;
            $this->RecursiveAlias($item);
        }
        return  array_reverse($this->menu, true);
    }
    public function getDataIDBreadcrumb($data)
    {
        $result = [];
        if (count($data) > 0) {
            foreach ($data as $item) {
                $result[] = $item['id'];
            }
        }
        return $result;
    }
    public function Hierarchy($data, $strong = false)
    {
        if (count($data) > 0) {
            $this->data = $data;
            $categoryRoot = $data->filter(function ($d) {
                return $d['parent_id'] == 0;
            });
            //echo "<pre>";print_r($categoryRoot);die();
            foreach ($categoryRoot as $val) {
                $val->name = ($strong) ? "<strong>+ " . $val->name . "</strong>" : "+ " . $val->name;
                $val->has_sub = 0;
                $categoryParents = $data->filter(function ($d) use ($val) {
                    return $d['parent_id'] == $val->id;
                });
                $this->menu[] = $val->toArray();
                if (count($categoryParents) > 0) {
                    $val->has_sub = 1;
                    $this->Recursive($categoryParents);
                }
            }
        }

        return $this->menu;
    }

    public function generateNavigation($data)
    {
        $this->data = $data;
        $categoryRoot = $data->filter(function ($d) {
            return $d['parent_id'] == 0;
        });
        $link = route("category/formajax");
        $this->str .= "<ul>";
        $this->str .= sprintf("<li id='0' data-jstree='%s'>Root", '{"opened" : true }');
        $this->str .= "<ul>";
        foreach ($categoryRoot as $val) {
            $link = route("category/formajax", ['id' => $val['id']]);
            if ($val['status'] == 0) {
                $this->str .= sprintf("<li id='%s' class='disabled %s'><a id='%s' parent_id='%s' href='%s'>%s</a>", $val['id'], 'page-' . $val['page'], $val['id'], $val['parent_id'], $link, $val['name']);
            } else {
                $this->str .= sprintf('<li id="%s" class="%s"><a id="%s" parent_id="%s" href="%s">%s</a>', $val['id'], 'page-' . $val['page'], $val['id'], $val['parent_id'], $link, $val['name']);
            }
            $categoryParents = $data->filter(function ($d) use ($val) {
                return $d['parent_id'] == $val->id;
            });
            if (count($categoryParents) > 0) {
                $this->RecursiveNavigation($categoryParents);
            }
        }
        $this->str .= "</li>";
        $this->str .= "</ul>";
        $this->str .= "</li>";
        $this->str .= "</ul>";
        return $this->str;
    }

    public function RecursiveNavigation($dataParent)
    {
        if (count($dataParent) > 0) {
            $this->str .= "<ul>";
            foreach ($dataParent as $val) {
                $link = route("category/formajax", ['id' => $val['id']]);
                if ($val['status'] == 0) {
                    $this->str .= sprintf("<li id='%s' class='disabled %s'><a id='%s' parent_id='%s' href='%s'>%s</a>", $val['id'], 'page-' . $val['page'], $val['id'], $val['parent_id'], $link, $val['name']);
                } else {
                    $this->str .= sprintf('<li id="%s" class="%s"><a id="%s" parent_id="%s" href="%s">%s</a>', $val['id'], 'page-' . $val['page'], $val['id'], $val['parent_id'], $link, $val['name']);
                }
                $categoryParents = $this->data->filter(function ($d) use ($val) {
                    return $d['parent_id'] == $val->id;
                });
                if (count($categoryParents) > 0) {
                    $this->RecursiveNavigation($categoryParents);
                }
            }
            $this->str .= "</li>";
            $this->str .= "</ul>";
        }
    }
    public function generateNavigationMenu($data, $idMenu = "menu", $id_category = 0)
    {
        $this->data = $data;
        $categoryRoot = $data->filter(function ($d) {
            return $d['parent_id'] == 0;
        });
        $this->str .= sprintf('<ul id="%s">', $idMenu);
        foreach ($categoryRoot as $val) {
            $link = url($val->url->path);

            $categoryParents = $data->filter(function ($d) use ($val) {
                return $d['parent_id'] == $val->id;
            });
            if (count($categoryParents) > 0) {
                $this->str .= sprintf("<li><a href='javascript:;'>%s</a>",  $val['name']);
                $this->RecursiveNavigationMenu($categoryParents, $id_category);
            } else {
                $this->str .= sprintf("<li><a href='%s'>%s</a>", $link, $val['name']);
            }
            $this->str .= "</li>";
        }

        $this->str .= "</ul>";
        return $this->str;
    }

    public function RecursiveNavigationMenu($dataParent, $id_category = 0)
    {
        if (count($dataParent) > 0) {
            $this->str .= "<ul>";
            foreach ($dataParent as $val) {
                $link = url($val->url->path);
                if ($val->id == $id_category) {
                    $this->str .= sprintf("<li class='active'><a href='%s'><i class='bi bi-caret-right-fill me-2'></i>%s</a>", $link, $val['name']);
                } else {
                    $this->str .= sprintf("<li><a href='%s'><i class='bi bi-caret-right-fill me-2'></i>%s</a>", $link, $val['name']);
                }

                $categoryParents = $this->data->filter(function ($d) use ($val) {
                    return $d['parent_id'] == $val->id;
                });
                if (count($categoryParents) > 0) {
                    $this->RecursiveNavigation($categoryParents, $id_category);
                }
            }
            $this->str .= "</li>";
            $this->str .= "</ul>";
        }
    }
    public function Recursive($dataParent, $temp = "----⊹")
    {
        if (count($dataParent) > 0) {
            foreach ($dataParent as $val) {
                $val->name = $temp . " " . $val->name;
                $val->has_sub = 0;
                $categoryParents = $this->data->filter(function ($d) use ($val) {
                    return $d['parent_id'] == $val->id;
                });
                if (count($categoryParents) > 0) {
                    $val->has_sub = 1;
                    $this->menu[] = $val->toArray();
                    $this->Recursive($categoryParents, $temp . $temp);
                }
                $this->menu[] = $val->toArray();
            }
        }
        return;
    }

    public function RecursiveAlias($dataParent)
    {
        if ($dataParent) {
            // $item = Arr::first($this->data, function ($d) use ($dataParent) {
            //     return $d['id'] == $dataParent['parent_id'];
            // });
             $item =  $this->data->firstWhere('id',$dataParent['parent_id']);
            if ($item) {
                $this->menu[] = $item;
                $this->RecursiveAlias($item);
            }
        }
        return;
    }

    public function generateNavigationSelect($data, $checked = [])
    {
        $this->data = $data;
        $categoryRoot = $data->filter(function ($d) {
            return $d['parent_id'] == 0;
        });
        $this->str .= "<ul>";
        $this->str .= sprintf("<li id='0' data-jstree='%s'>Root", '{"opened" : true }');
        $this->str .= "<ul>";
        foreach ($categoryRoot as $val) {
            $checkedNode = 'checked-false';
            if ($checked != null && in_array($val['id'], $checked)) {
                $checkedNode = 'jstree-checked';
            }
            if ($val['status'] == 0) {
                $this->str .= sprintf("<li id='%s' class='disabled %s'><a id='%s' parent_id='%s'>%s</a>", $val['id'], $checkedNode, $val['id'], $val['parent_id'], $val['name']);
            } else {
                $this->str .= sprintf('<li id="%s" class="%s"><a id="%s" parent_id="%s">%s</a>', $val['id'], $checkedNode, $val['id'], $val['parent_id'], $val['name']);
            }
            $categoryParents = $data->filter(function ($d) use ($val) {
                return $d['parent_id'] == $val->id;
            });
            if (count($categoryParents) > 0) {
                $this->RecursiveNavigationSelect($categoryParents, $checked);
            }
        }
        $this->str .= "</li>";
        $this->str .= "</ul>";
        $this->str .= "</li>";
        $this->str .= "</ul>";
        return $this->str;
    }

    public function RecursiveNavigationSelect($dataParent, $checked = [])
    {
        if (count($dataParent) > 0) {
            $this->str .= "<ul>";
            foreach ($dataParent as $val) {
                $checkedNode = 'checked-false';
                if ($checked != null && in_array($val['id'], $checked)) {
                    $checkedNode = 'jstree-checked';
                }
                if ($val['status'] == 0) {
                    $this->str .= sprintf("<li id='%s' class='disabled %s'><a id='%s' parent_id='%s'>%s</a>", $val['id'], $checkedNode, $val['id'], $val['parent_id'], $val['name']);
                } else {
                    $this->str .= sprintf('<li id="%s" class="%s"><a id="%s" parent_id="%s">%s</a>', $val['id'], $checkedNode, $val['id'], $val['parent_id'], $val['name']);
                }
                $categoryParents = $this->data->filter(function ($d) use ($val) {
                    return $d['parent_id'] == $val->id;
                });
                if (count($categoryParents) > 0) {
                    $this->RecursiveNavigation($categoryParents);
                }
            }
            $this->str .= "</li>";
            $this->str .= "</ul>";
        }
    }
}
