<?php

/**
 * Plugin multifields3
 *
 * @author 64j
 */

if (!defined('MODX_BASE_PATH')) {
    die('What are you doing? Get out of here!');
}

require_once __DIR__ . '/Multifields3.php';

switch (evo()->event->name) {
    case 'OnManagerMainFrameHeaderHTMLBlock':
        if (in_array(evo()->getManagerApi()->action, [3, 4, 17, 27, 72, 112])) {
            evo()->event->addOutput(Multifields3::getInstance()->getStartScripts());
        }

        if (in_array(evo()->getManagerApi()->action, [300, 301])) {
            evo()->event->addOutput(Multifields3::getInstance()->jsonEditorScripts());
        }
        break;

    case 'OnBeforeManagerPageInit':
        Multifields3::getInstance()->managerInit();
        break;
}
