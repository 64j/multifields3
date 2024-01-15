<?php

/**
 * Plugin multifields3
 *
 * @author 64j
 */

if (!defined('MODX_BASE_PATH')) {
    die('What are you doing? Get out of here!');
}

/** @var DocumentParser $modx */
$e = &$modx->event;

require_once __DIR__ . '/Multifields3.php';

$mf3 = new Multifields3();

switch ($e->name) {
    case 'OnManagerMainFrameHeaderHTMLBlock':
        if (in_array($modx->manager->action, [3, 4, 17, 27, 72, 112])) {
            $e->addOutput($mf3->getStartScripts());
        }
        break;

    case 'OnBeforeManagerPageInit':
        $mf3->managerInit();
        break;
}
