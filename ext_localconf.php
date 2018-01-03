<?php

if(class_exists('\KayStrobach\Dyncss\Configuration\BeRegistry')) {
	\KayStrobach\Dyncss\Configuration\BeRegistry::get()->registerFileHandler('scss', 'Baschte\DyncssScssAutoprefixer\Parser\ScssParser');
}

if (TYPO3_MODE === 'BE') {
	$GLOBALS['TYPO3_CONF_VARS']['SC_OPTIONS']['reports']['tx_reports']['status']['providers']['SCSS Autoprefixer'][] =
		\Baschte\DyncssScssAutoprefixer\Reports\StatusReport::class;
}
