<?php
defined('TYPO3') || die();

(static function() {
    \TYPO3\CMS\Extbase\Utility\ExtensionUtility::configurePlugin(
        'GdprExtensionsComGrt',
        'googlereviewteaser',
        [
            \GdprExtensionsCom\GdprExtensionsComGrt\Controller\GdprGoogleReviewTeaserController::class => 'index , showReviews'
        ],
        // non-cacheable actions
        [
            \GdprExtensionsCom\GdprExtensionsComGrt\Controller\GdprGoogleReviewTeaserController::class => 'showReviews',
            \GdprExtensionsCom\GdprExtensionsComGrt\Controller\GdprManagerController::class => 'create, update, delete'
        ],
        \TYPO3\CMS\Extbase\Utility\ExtensionUtility::PLUGIN_TYPE_CONTENT_ELEMENT
    );

    // register plugin for cookie widget
    \TYPO3\CMS\Extbase\Utility\ExtensionUtility::configurePlugin(
        'GdprExtensionsComGrt',
        'gdprcookiewidget',
        [
            \GdprExtensionsCom\GdprExtensionsComGrt\Controller\GdprCookieWidgetController::class => 'index'
        ],
        // non-cacheable actions
        [],
    );



    // wizards
    \TYPO3\CMS\Core\Utility\ExtensionManagementUtility::addPageTSConfig(
        'mod {
            wizards.newContentElement.wizardItems.plugins {
                elements {
                    gdprcookiewidget {
                        iconIdentifier = gdpr_extensions_com_grt-plugin-googlereviewteaser
                        title = cookie
                        description = LLL:EXT:gdpr_extensions_com_grt/Resources/Private/Language/locallang_db.xlf:tx_gdpr_extensions_com_grt_googlereviewteaser.description
                        tt_content_defValues {
                            CType = list
                            list_type = gdprextensionscomgrt_gdprcookiewidget
                        }
                    }
                }
                show = *
            }
       }'
    );
    // wizards
    \TYPO3\CMS\Core\Utility\ExtensionManagementUtility::addPageTSConfig(
        'mod.wizards.newContentElement.wizardItems {
               gdpr.header = LLL:EXT:gdpr_extensions_com_grt/Resources/Private/Language/locallang_db.xlf:tx_GdprExtensionsComGrt_domain_model_googlereviewteaser
        }'
    );
    // wizards
    \TYPO3\CMS\Core\Utility\ExtensionManagementUtility::addPageTSConfig(
        'mod {
            wizards.newContentElement.wizardItems.gdpr {
                elements {
                    googlereviewteaser {
                        iconIdentifier = gdpr_extensions_com_grt-plugin-googlereviewteaser
                        title = LLL:EXT:gdpr_extensions_com_grt/Resources/Private/Language/locallang_db.xlf:tx_gdpr_extensions_com_grt_googlereviewteaser.name
                        description = LLL:EXT:gdpr_extensions_com_grt/Resources/Private/Language/locallang_db.xlf:tx_gdpr_extensions_com_grt_googlereviewteaser.description
                        tt_content_defValues {
                            CType = gdprextensionscomgrt_googlereviewteaser
                        }
                    }
                }
                show = *
            }
       }'
    );

    $GLOBALS['TYPO3_CONF_VARS']['SC_OPTIONS']['scheduler']['tasks'][\GdprExtensionsCom\GdprExtensionsComGrt\Commands\SyncReviewsTask::class] = [
        'extension' => 'GdprExtensionsComGrt',
        'title' => 'Fetch Google Reviews',
        'description' => 'Fetch google reviews from GDPR-extensions-com dashboard',
        'additionalFields' => \GdprExtensionsCom\GdprExtensionsComGrt\Commands\SyncReviewsTask::class,
    ];


})();
$GLOBALS['TYPO3_CONF_VARS']['SC_OPTIONS']['t3lib/class.t3lib_tcemain.php']['processDatamapClass'][] = \GdprExtensionsCom\GdprExtensionsComGrt\Hooks\DataHandlerHook::class;
if (!isset($GLOBALS['TYPO3_CONF_VARS']['SYS']['caching']['cacheConfigurations']['GdprExtensionsComGrt'])) {
    $GLOBALS['TYPO3_CONF_VARS']['SYS']['caching']['cacheConfigurations']['GdprExtensionsComGrt'] = [
        'frontend' => \TYPO3\CMS\Core\Cache\Frontend\VariableFrontend::class,
        'backend' => \TYPO3\CMS\Core\Cache\Backend\FileBackend::class,
        'groups' => ['all', 'GdprExtensionsComGrt'],
        'options' => [
            'defaultLifetime' => 3600, // Cache lifetime in seconds
        ],
    ];
}
