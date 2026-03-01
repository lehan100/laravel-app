<?php

namespace App\Helpers;

class Seo {

    protected $head;

    public function __construct($head) {
        $this->head = $head;
    }

    public function metaTags($param) {
        $contents = $param->contents;
        $title = (isset($contents['title']) && $contents['title'] != "") ? $contents['title'] : $param->name;
        $keywords = (isset($contents['keyword']) && $contents['keyword'] != "") ? $contents['keyword'] : $title;
        $description = $this->getDescription($param);
        if ($description == "") {
            $description = $title;
        }
        $this->setMeta('title', $title);
        $this->setMeta('keywords', $keywords);
        $this->setMeta('description', $description);

        $this->setMetaProperty('og:title', $title);
        $this->setMetaProperty('og:keywords', $keywords);
        $this->setMetaProperty('og:description', $description);
        return $this;
    }

    public function cutString($string = "", $length = 160) {
        $end = (strlen($string) > $length) ? "..." : '';
        return trim(strip_tags(substr($string, 0, $length))) . $end;
    }

    public function getDescription($param) {
        $contents = $param->contents;
        $description = $param->name;
        if (isset($contents['description']) && $contents['description'] != "") {
            $description = $this->cutString($contents['description'], 320);
        } else if ($contents->content != "") {
            $description = $this->cutString($contents->content, 320);
        }
        return $description;
    }

    public function setMeta($name = "", $content = "") {
        $this->head->headMeta($content, $name);
        return $this;
    }

    public function setMetaProperty($property = "", $content = "") {
        $this->head->headMetaProperty($content, $property);
        return $this;
    }

}
