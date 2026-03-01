<?php

namespace App\Helpers\Product;

use App\Helpers\Product\Price as Price;

class OptionEntries {

    private $_ITEM_ATTRIBUTE_CLASS = 'attributes navbar justify-content-start';
    private $_CONFIG;

    public function __construct() {
        $this->_CONFIG = config('image.path.product_option');
    }

    public static function toStrings($options) {
        if (count($options) > 0) {
            $xhtml = '<div class="option-group-entries ">';
            $data = [];
            foreach ($options as $option) {
                if (count($option->attributes) > 0) {
                    $xhtml .= '<div class="option-item-entries row align-items-center mb-3">';
                    $xhtml .= sprintf('<div class="col-12 fw-bold pe-0 mb-2">%s <span class="required">*</span></div>', $option->title);
                    $xhtml .= sprintf('<div class="col">%s</div>', (new \App\Helpers\Product\OptionEntries())->getAttributes($option));
                    $xhtml .= '</div>';
                }
                foreach ($option->attributes as $attribute) {
                         $data[] = [
                                'id' => $attribute->id,
                                'title' => $attribute->title,
                                'price' => $attribute->price
                            ];
                    }
            }
            $xhtml .= '</div>';
            $xhtml .= sprintf('<script>var dataOptionEntries = %s</script>', json_encode($data));
            return $xhtml;
        }
        return null;
    }

    public function getAttributes($option) {
        if ($option) {
            $xhtml = "";
            if ($option->type == 0) {
                $xhtml .= $this->getAttributeTypeText($option->attributes);
            }
            if ($option->type == 1) {
                $xhtml .= $this->getAttributeTypeTextPicture($option->attributes);
            }
            if ($option->type == 2) {
                $xhtml .= $this->getAttributeTypeTextColor($option->attributes);
            }

            return $xhtml;
        }
    }

    public function getAttributeTypeText($attributes) {
        $xhtml = sprintf('<ul class="%s">', $this->_ITEM_ATTRIBUTE_CLASS);
        foreach ($attributes as $attribute) {
            $xdata = sprintf('data-option-id="%s"', $attribute->id);
            if ($attribute->price > 0) {
                $xdata .= sprintf('data-price="%s"', $attribute->price);
            }
            $xhtml .= sprintf('<li class="nav-item" %s><div class="title px-3">%s</div><span class="check"></span></li>', $xdata, $attribute->title);
        }
        $xhtml .= '</ul>';
        return $xhtml;
    }

    public function getAttributeTypeTextPicture($attributes) {
        $xhtml = sprintf('<ul class="%s">', $this->_ITEM_ATTRIBUTE_CLASS);
        foreach ($attributes as $attribute) {
            $xdata = sprintf('data-option-id="%s"', $attribute->id);
            if ($attribute->price > 0) {
                $xdata .= sprintf('data-price="%s"', $attribute->price);
            }
            $picture = $attribute->picture;
            $pictureUrl = ($picture != "") ? asset($this->_CONFIG['path'] . '/' . $picture) : "";
            $xpicture = sprintf('<img src="%s" width="46px" alt="%s">', $pictureUrl, $attribute->title);
            $xhtml .= sprintf('<li class="nav-item d-flex align-items-center" %s><div class="thumb">%s</div><div class="title px-3">%s</div><span class="check"></span></li>', $xdata, $xpicture, $attribute->title);
        }
        $xhtml .= '</ul>';
        return $xhtml;
    }

    public function getAttributeTypeTextColor($attributes) {
        $xhtml = sprintf('<ul class="%s">', $this->_ITEM_ATTRIBUTE_CLASS);
        foreach ($attributes as $attribute) {
            $xdata = sprintf('data-option-id="%s"', $attribute->id);
            if ($attribute->price > 0) {
               $xdata .= sprintf('data-price="%s"', $attribute->price);
            }
            $color = $attribute->color;
            $xhtml .= sprintf('<li class="nav-item d-flex align-items-center" %s><div class="color" style="background:%s"></div><div class="title px-3">%s</div><span class="check"></span></li>', $xdata, $color, $attribute->title);
        }
        $xhtml .= '</ul>';
        return $xhtml;
    }
}
