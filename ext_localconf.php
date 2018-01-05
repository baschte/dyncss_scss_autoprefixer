<?php

if (TYPO3_MODE === 'BE') {
    $GLOBALS['TYPO3_CONF_VARS']['SC_OPTIONS']['reports']['tx_reports']['status']['providers']['SCSS Autoprefixer'][] =
        \Baschte\DyncssScssAutoprefixer\Reports\StatusReport::class;
}

/** @var \TYPO3\CMS\Extbase\SignalSlot\Dispatcher $signalSlotDispatcher */
$signalSlotDispatcher = \TYPO3\CMS\Core\Utility\GeneralUtility::makeInstance(\TYPO3\CMS\Extbase\SignalSlot\Dispatcher::class);
$signalSlotDispatcher->connect(
    \KayStrobach\Dyncss\Parser\AbstractParser::class,  // Signal class name
    'afterFileParsed',                                  // Signal name
    \Baschte\DyncssScssAutoprefixer\Slots\FileParsedSlot::class,        // Slot class name
    'afterFileParsed'                               // Slot name
);
