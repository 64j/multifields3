<?php
/**
 * custom tv multifields3
 *
 * @author 64j
 */

if (!defined('MODX_BASE_PATH')) {
    die('HACK???');
}

echo '
<script>mf3Config["tv' . $row['id'] . '"]=' . Multifields3::getInstance()->getConfig($row ?? []) . ';</script>
<textarea name="tv' . $row['id'] . '" class="mf3-data d-none" rows="4">' . $row['value'] . '</textarea>
';
