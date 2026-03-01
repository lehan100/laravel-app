<?php

namespace App\Helpers;

class Head {

    protected $headMeta = [];
    protected $headMetaProperty = [];
    protected $headLink = [];

    public function headMeta($content, $name) {
        if ($name != "") {
            $this->headMeta[$name] = $content;
        }
    }

    public function headMetaProperty($content, $name) {
        if ($name != "") {
            $this->headMetaProperty[$name] = $content;
        }
    }

    public function headLink($param) {
        if (count($param) > 0) {
            $this->headLink[] = $param;
        }
    }

    public function getHeadMeta() {
        
        $metaString = "";
        if (count($this->headMeta) > 0) {
            foreach ($this->headMeta as $n => $c) {
                if ($n == 'title') {
                    $metaString .= sprintf('<title>%s</title>', $c);
                } else {
                    $metaString .= sprintf('<meta name="%s" content="%s"/>', $n, $c);
                }
            }
        }
        if (count($this->headMetaProperty) > 0) {
            foreach ($this->headMetaProperty as $n => $c) {
                $metaString .= sprintf('<meta property="%s" content="%s"/>', $n, $c);
            }
        }
        return $metaString;
    }

    public function getHeadLink() {
        $html = "";
        if (count($this->headLink) > 0) {

            foreach ($this->headLink as $val) {
                $xhtml = '';
                foreach ($val as $key => $item) {
                    $xhtml .= sprintf('%s="%s" ', $key, $item);
                }
                $html .= sprintf("<link %s/>", $xhtml);
            }
        }
        return $html;
    }

}
