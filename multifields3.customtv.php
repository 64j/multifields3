<?php
/**
 * custom tv multifields3
 *
 * @author 64j
 */

if (!defined('MODX_BASE_PATH')) {
    die('HACK???');
}

echo '<textarea name="tv' . $row['id'] . '" class="mf3-data" data-id="mf2" data-tv-name="' . $row['name'] .
    '" style="display: none">' . $row['value'] . '</textarea>';
