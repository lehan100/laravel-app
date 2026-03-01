<?php

namespace App\Helpers;

use Config;

class Form {

    public static function show($elements) {
        $xhtml = null;
        foreach ($elements as $element) {
            $xhtml .= self::formGroup($element);
        }
        return $xhtml;
    }

    public static function formGroup($element, $params = null) {
        $type = isset($element['type']) ? $element['type'] : "input";
        $xhtml = null;
        $label = isset($element['label']) ? $element['label'] : '';
        $control = isset($element['control']) ? $element['control'] : '';
        $form_input_class = isset($element['form_input_class']) ? $element['label'] : '';
        $elementInput = isset($element['element']) ? $element['element'] : '';
        $error = isset($element['error']) ? $element['error'] : '';
        switch ($type) {
            case 'input':
                $xhtml .= sprintf(
                        '<div class="form-group row align-items-start %s">
                        %s
                        <div class="col">
                            %s%s
                        </div>
                    </div>',
                        $control,
                        $label,
                        $elementInput,
                        $error
                );
                break;
        }

        return $xhtml;
    }

}
