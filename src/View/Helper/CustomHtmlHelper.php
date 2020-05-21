<?php

namespace App\View\Helper;

use Cake\View\Helper\HtmlHelper;

class CustomHtmlHelper extends HtmlHelper {

    public function component($path, $type = 'css', array $options = array()) {
        $path = '/vendor/' . $path;
        return parent::{$type}($path, $options);
    }

}
