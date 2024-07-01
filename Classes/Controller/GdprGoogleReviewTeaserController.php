<?php

declare(strict_types=1);

namespace GdprExtensionsCom\GdprExtensionsComGrt\Controller;


use GdprExtensionsCom\GdprExtensionsComGrt\Utility\Helper;
use TYPO3\CMS\Core\Database\ConnectionPool;
use TYPO3\CMS\Core\Utility\GeneralUtility;
use Psr\Http\Message\ResponseInterface;
// use Symfony\Component\HttpFoundation\JsonResponse;
use Laminas\Diactoros\Response\JsonResponse;

/**
 * This file is part of the "gdpr-extensions-com-google_reviewlist" Extension for TYPO3 CMS.
 *
 * For the full copyright and license information, please read the
 * LICENSE.txt file that was distributed with this source code.
 *
 * (c) 2023
 */

/**
 * googlereviewteaserController
 */
class GdprGoogleReviewTeaserController extends \TYPO3\CMS\Extbase\Mvc\Controller\ActionController
{


    /**
     * gdprManagerRepository
     *
     * @var \GdprExtensionsCom\GdprExtensionsComGrt\Domain\Repository\GdprManagerRepository
     */

    protected $gdprManagerRepository = null;

    /**
     * ContentObject
     *
     * @var ContentObject
     */
    protected $contentObject = null;

    /**
     * array
     */
    protected $reviewArray = [];

    /**
     * Action initialize
     */
    protected function initializeAction()
    {
        $this->contentObject = $this->configurationManager->getContentObject();

        // intialize the content object
    }

    /**
     * @param \GdprExtensionsCom\GdprExtensionsComGrt\Domain\Repository\GdprManagerRepository $gdprManagerRepository
     */
    public function injectGdprManagerRepository(\GdprExtensionsCom\GdprExtensionsComGrt\Domain\Repository\GdprManagerRepository $gdprManagerRepository)
    {
        $this->gdprManagerRepository = $gdprManagerRepository;
    }

    /**
     * action index
     *
     * @return \Psr\Http\Message\ResponseInterface
     */
    public function indexAction(): \Psr\Http\Message\ResponseInterface
    {
        $reviewsQB = GeneralUtility::makeInstance(ConnectionPool::class)->getQueryBuilderForTable('tx_gdprclientreviews_domain_model_reviews');
        $reviewsResult = $reviewsQB->select('*')
            ->from('tx_gdprclientreviews_domain_model_reviews')
            ->where(
                $reviewsQB->expr()
                    ->eq('uid', $reviewsQB->createNamedParameter($this->contentObject->data['gdpr_business_locations_teaser_rev'])),
            )
            ->executeQuery();

        $reviewsData = $reviewsResult->fetchAssociative();

        $this->view->assign('review', $reviewsData);
        $this->view->assign('data', $this->contentObject->data);
        return $this->htmlResponse();
    }

    public function showReviewsAction()
    {
        $reviewsToFetch = GeneralUtility::_GP('reveiwsToFetch') ?: 10;
        $sort = GeneralUtility::_GP('sort');
        $contentElementUid = $this->configurationManager->getContentObject()->data['uid']; // Example to get current content element UID

        $cacheManager = \TYPO3\CMS\Core\Utility\GeneralUtility::makeInstance(\TYPO3\CMS\Core\Cache\CacheManager::class);
        $cache = $cacheManager->getCache('GdprExtensionsComGrt');

        // Adjusted cache identifier to be more specific and include content element UID
        $cacheIdentifier = 'reviewArray_' . $contentElementUid;
        $cacheTag = 'content_element_' . $contentElementUid; // Cache tag based on content element UID

        $reviewArray = $cache->get($cacheIdentifier);

        if (!$reviewArray) {
            $reviewArray = $this->fetchReviews();
            $cache->set($cacheIdentifier, $reviewArray, [$cacheTag], 3600);
        }

        $reviewsSlice = array_slice($reviewArray, 0, (int)$reviewsToFetch);
        if ($sort == '1') {
            usort($reviewsSlice, function ($a, $b) {
                return $a['date_sort'] - $b['date_sort'];
            });
        } elseif ($sort == '2') {
            usort($reviewsSlice, function ($a, $b) {
                return $b['date_sort'] - $a['date_sort'];
            });
        }
        if(count($reviewArray) > 0){
            $completed = (count($reviewsSlice) == count($reviewArray)) ? 1 : 0;
        }
        $result = ['fetchedReviews' => $reviewsSlice ,'completed'=>$completed ];
        die(json_encode($result));
        return $this->jsonResponse(json_encode($result));
    }


    public function fetchReviews()
    {
        dd($this->contentObject->data);


        return $reviewsData;
    }
}
