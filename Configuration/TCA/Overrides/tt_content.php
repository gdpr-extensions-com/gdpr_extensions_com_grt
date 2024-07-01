<?php
defined('TYPO3') || die();

$frontendLanguageFilePrefix = 'LLL:EXT:frontend/Resources/Private/Language/locallang_ttc.xlf:';
\TYPO3\CMS\Extbase\Utility\ExtensionUtility::registerPlugin(
    'GdprExtensionsComGrt',
    'googlereviewteaser',
    'Google Reviews Teaser'
);

$fields = [

    'gdpr_business_locations_teaser' => [
        'onChange' => 'reload',
        'config' => [
            'type' => 'select',
            'renderType' => 'selectSingle',
            'itemsProcFunc' => 'GdprExtensionsCom\GdprExtensionsComGrt\Utility\ProcesslistItems->getLocationsforRoodPid',
        ],
    ],


    'gdpr_business_locations_teaser_rev' => [
        'config' => [
            'type' => 'select',
            'renderType' => 'selectMultipleSideBySide',
            'itemsProcFunc' => 'GdprExtensionsCom\GdprExtensionsComGrt\Utility\ProcesslistreviewItems->getReviewsforRoodPid',
            'maxitems' => 1,
        ],
    ],

    'gdpr_background_color_teaser' => [
        'config' => [
            'type' => 'input',
            'renderType' => 'colorpicker',
        ],
    ],
    'gdpr_color_of_border_teaser' => [
        'config' => [
            'type' => 'input',
            'renderType' => 'colorpicker',
        ],
    ],

    'gdpr_color_of_text_teaser' => [
        'config' => [
            'type' => 'input',
            'renderType' => 'colorpicker',
        ],
    ],






];

\TYPO3\CMS\Core\Utility\ExtensionManagementUtility::addTCAcolumns('tt_content', $fields);

$GLOBALS['TCA']['tt_content']['types']['gdprextensionscomgrt_googlereviewteaser'] = [
    'showitem' => '
                --palette--;' . $frontendLanguageFilePrefix . 'palette.general;general,
                 gdpr_color_of_border_teaser; Border Color,
                 gdpr_color_of_text_teaser; Text Color,
                 gdpr_background_color_teaser; Background Color,
                 gdpr_business_locations_teaser_rev; Bussiness Locations review,


                --div--;' . $frontendLanguageFilePrefix . 'tabs.appearance,
                --palette--;' . $frontendLanguageFilePrefix . 'palette.frames;frames,
                --div--;LLL:EXT:core/Resources/Private/Language/Form/locallang_tabs.xlf:language,
                --palette--;;language,
                --div--;' . $frontendLanguageFilePrefix . 'tabs.access,
                hidden;' . $frontendLanguageFilePrefix . 'field.default.hidden,
                --palette--;' . $frontendLanguageFilePrefix . 'palette.access;access,
                --div--;LLL:EXT:core/Resources/Private/Language/Form/locallang_tabs.xlf:notes,
        ',
];
