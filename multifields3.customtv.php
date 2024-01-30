<?php
/**
 * custom tv multifields3
 *
 * @author 64j
 */

if (!defined('MODX_BASE_PATH')) {
    die('HACK???');
}

if (!empty($row['elements'])) {
    $config = trim($row['elements'])[0] == '{' ? $row['elements'] : 'null';
} elseif (file_exists($path = MODX_BASE_PATH . 'assets/plugins/multifields3/config/' . $row['name'] . '.json')) {
    $config = file_get_contents($path) ?: 'null';
} else {
    $config = 'null';
}

echo '<script>window["mf3Config"]["tv' . $row['id'] . '"]=' . $config . ';</script>';
echo '<textarea name="tv' . $row['id'] . '" class="mf3-data d-none" rows="4">' . $row['value'] . '</textarea>';
