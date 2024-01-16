<?php
/**
 * custom tv multifields3
 *
 * @author 64j
 */

if (!defined('MODX_BASE_PATH')) {
    die('HACK???');
}

if (file_exists($path = MODX_BASE_PATH . 'assets/plugins/multifields3/config/' . $row['name'] . '.json')) {
    echo '<script>if(!window["mf3Config"]) { window["mf3Config"] = [] }; window["mf3Config"]["' . $row['name'] . '"]=' . (file_get_contents($path) ?: 'null') . ';</script>';
}

echo '<textarea name="tv' . $row['id'] . '" class="mf3-data" data-id="mf2" data-tv-name="' . $row['name'] .
    '" style="display: none">' . $row['value'] . '</textarea>';
