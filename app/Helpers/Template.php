<?php

namespace App\Helpers;
use Illuminate\Support\Facades\Auth;
class Template {

    public static function getUserGroup($group = 0) {
        $templates = config('configs.user_group');
        if ($group > 0) {
            return $templates[$group];
        }
        return "";
    }

    public static function showStatus($controllerName, $status = 0, $id = 0) {
        $templates = config('configs.active_status');
        $statusTemplate = $templates[$status];
        $link = "'" . route($controllerName . '/status', ['id' => $id, 'status' => $status]) . "'";
        return sprintf('<button type="button" onclick="changeStatus(this,%s)" class="%s"><i class="%s mr-1"></i>%s</button>', $link, $statusTemplate['class'], $statusTemplate['icon'], $statusTemplate['name']);
    }

    public static function showStatusContact($controllerName, $status = 0, $id = 0) {
        $templates = config('configs.contact_status');
        $statusTemplate = $templates[$status];
        $link = "'" . route($controllerName . '/status', ['id' => $id, 'status' => $status]) . "'";
        return sprintf('<button type="button" onclick="changeStatus(this,%s)" class="%s"><i class="%s mr-1"></i>%s</button>', $link, $statusTemplate['class'], $statusTemplate['icon'], $statusTemplate['name']);
    }

    public static function showButtomAction($controllerName, $id = 0) {
        $templateButton = config('configs.button_action');
        $buttonInArea = config('configs.button_configs.button_action');
        $buttonMessage = config('configs.dialog_messages');
        $controllerNameArea = (array_key_exists($controllerName, $buttonInArea)) ? $controllerName : 'default';
        $listButton = $buttonInArea[$controllerNameArea];
        $xhtml = '';
        foreach ($listButton as $button) {
            $template = $templateButton[$button];
            if (auth()->user()->can($controllerName . $template['route'])) {
                $link = route($controllerName . $template['route'], ['id' => $id]);
                if (isset($buttonMessage[$button])) {
                    $messageItem = $buttonMessage[$button];
                    $link = sprintf("javascript:onActionForm('%s',true,'%s','%s','%s','%s')", $link, $messageItem['title'], $messageItem['message'], $messageItem['class'], $messageItem['icon']);
                }
                $xhtml .= sprintf('<a href="%s" class="%s"><i class="%s mr-2"></i>%s</a>', $link, $template['class'], $template['icon'], $template['name']);
            }
        }
        return $xhtml;
    }

    public static function showButtomMain($controllerName, $buttomGroup = 'default', $id = 0) {
        $templateButton = config('configs.main_button');
        $buttonInArea = config('configs.button_configs.main_button');
        $buttonMessage = config('configs.dialog_messages');
        $controllerNameArea = (array_key_exists($controllerName, $buttonInArea)) ? $controllerName : 'default';
        $listButton = $buttonInArea[$controllerNameArea][$buttomGroup];
        $xhtml = '';
        foreach ($listButton as $button) {
            $template = $templateButton[$button];
            if (auth()->user()->can($controllerName . $template['route'])) {
                if (isset($template['type'])) {
                    if (isset($buttonMessage[$button])) {
                        $messageItem = $buttonMessage[$button];
                        $link = sprintf("javascript:onSubmitForm('appForm','%s',true,'%s','%s','%s','%s')", route($controllerName . $template['route'], ['type' => $template['type']]), $messageItem['title'], $messageItem['message'], $messageItem['class'], $messageItem['icon']);
                    } else {
                        $link = sprintf("javascript:onSubmitForm('appForm','%s')", route($controllerName . $template['route'], ['type' => $template['type']]));
                    }
                } elseif ($button == 'sort') {
                    $link = sprintf("javascript:onSubmitForm('appForm','%s')", route($controllerName . $template['route']));
                } elseif ($button == 'save' || $button == 'save_index') {
                    $link = sprintf("javascript:onSubmitActon('appForm')");
                } elseif ($button == 'saveandrollback') {
                    $link = sprintf("javascript:onSubmitActonRollback('appForm')");
                } elseif ($button == 'deleteatform') {
                    $link = route($controllerName . $template['route'], ['id' => $id]);
                } else {
                    $link = route($controllerName . $template['route']);
                }
                if ($button == 'deleteatform' && $id <= 0) {
                    $xhtml .= "";
                } else {
                    $xhtml .= sprintf('<a href="%s" class="%s"><i class="%s mr-2"></i>%s</a>', $link, $template['class'], $template['icon'], $template['name']);
                }
            }
        }
        return $xhtml;
    }

    public static function showImageAminLists($src_image, $width, $height) {
        return sprintf("<img src='%s' width='%s', height='%s'/>", $src_image, $width, $height);
    }

    public static function showAttributeSelector($param, $name, $alias, $data = null) {

        $xhtml = sprintf('<select class="form-control select2_multiple" name="%s" multiple="multiple">', $name);
        //$xhtml = sprintf('<select class="form-control" name="%s">', $name);
//        if ($data != null && count($data) > 0) {
//            $find = \Illuminate\Support\Arr::first($data, function ($value, $key) use ($alias) {
//                        return $value['alias'] == $alias;
//                    });
//        }
        foreach ($param as $val) {
            $xselected = "";
            if ($data) {
                $find = $data->filter(function ($d) use ($val, $alias) {
                    return $d['alias'] == $alias && $d['attribute_set_ids'] == $val->id;
                });
                if (count($find) > 0) {
                    $xselected = "selected='true'";
                }
            }
            $xhtml .= sprintf("<option value='%s' %s>%s</option>", $val->id, $xselected, $val->name);
        }
        $xhtml .= "</select>";
        return $xhtml;
    }
}
