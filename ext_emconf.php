<?php

$EM_CONF[$_EXTKEY] = [
    'title' => 'Dyncss - SCSS Autoprefixer',
    'description' => 'Autoprefixer for Dyncss Scss',
    'category' => 'frontend',
    'constraints' => [
        'depends' => [
            'typo3'  => '6.2.0-9.9.9',
            'dyncss'  => '0.8.3-2.0.99',
            'dyncss_scss'  => '1.0.0-2.0.99',
        ],
        'conflicts' => [
        ],
    ],
    'state' => 'stable',
    'uploadfolder' => 0,
    'createDirs' => '',
    'clearCacheOnLoad' => 1,
		'author' => 'Sebastian Richter',
		'author_email' => 'info@baschte.de',
    'version' => '0.2.3',
];

?>
