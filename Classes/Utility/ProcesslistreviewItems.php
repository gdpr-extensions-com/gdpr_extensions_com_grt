<?php

declare(strict_types=1);

namespace GdprExtensionsCom\GdprExtensionsComGrt\Utility;

use TYPO3\CMS\Core\Database\ConnectionPool;
use TYPO3\CMS\Core\Utility\GeneralUtility;
use GdprExtensionsCom\GdprExtensionsComGrt\Utility\Helper;

class ProcesslistreviewItems
{
    public function __construct()
    {

    }

    public function getReviewsforRoodPid(array &$params)
    {
        $connectionPool = GeneralUtility::makeInstance(ConnectionPool::class);
        $helper = GeneralUtility::makeInstance(Helper::class);
        $rootpid = $helper->getRootPage($params['row']['pid']);
        if($params['field'] == 'gdpr_business_locations_teaser_rev'){
            $bussinesLocations =  $params['row']['gdpr_business_locations_teaser'];
//            if(!empty($bussinesLocations)){
                $reviewsQB = $connectionPool->getQueryBuilderForTable('tx_gdprclientreviews_domain_model_reviews');
                $locationsreviewsQB = $connectionPool->getQueryBuilderForTable('gdpr_multilocations');
                $locationNamesList = [];
//                foreach ($bussinesLocations as $uid) {
                    $locationResult = $locationsreviewsQB->select('dashboard_api_key')
                        ->from('gdpr_multilocations')
                        ->where(
                            $locationsreviewsQB->expr()
                                ->eq('root_pid', $locationsreviewsQB->createNamedParameter($rootpid))
                        )
                        ->executeQuery();
                    $locationName = $locationResult->fetchOne();
                    $locationNamesList[] = $locationName;
//                }
                if ($locationNamesList) {
                    $reviews = [];
                    foreach ($locationNamesList as $location) {

                        $reviewsResult = $reviewsQB->select('*')
                            ->from('tx_gdprclientreviews_domain_model_reviews')
                            ->where(
                                $reviewsQB->expr()
                                    ->eq('dashboard_api_key', $reviewsQB->createNamedParameter($location)),
                            )
                            ->executeQuery();

                        $reviewsData = $reviewsResult->fetchAllAssociative();

                        $reviews = array_merge($reviews, $reviewsData);


                    }
                }
//            }
            foreach ($reviews as $review){
                $params['items'][] = [$review['reviewer_display_name'].' ('. $review['comment'] . ')', $review['uid']];
            }

        }

        return $params;
    }

    public function getReviewsForRootPid(array &$params)
    {
        $helper = GeneralUtility::makeInstance(Helper::class);
        $result = $this->fetchReviewsforRoot($helper->getRootPage($params['row']['pid']));

        while ($ret = $result->fetchAssociative()) {
            if (strlen($ret['comment']) < 1) {
                continue;
            }

            $params['items'][] = [
                str_repeat('★', $ret['star_rating']) . ' ' . $ret['comment'] . ' (' . $ret['reviewer_display_name'] . ')',
                $ret['uid'],
            ];
        }

        return $params;
    }

    private function fetchReviewsforRoot($rootPid)
    {
        $connectionPool = GeneralUtility::makeInstance(ConnectionPool::class);
        $reviewsQB = $connectionPool->getQueryBuilderForTable('tx_goclientreviews_domain_model_reviews');

        return $reviewsQB->select('*')
            ->from('tx_goclientreviews_domain_model_reviews')
            ->where($reviewsQB->expr() ->eq('root_pid', $reviewsQB->createNamedParameter($rootPid)))
            ->orderBy('star_rating', 'DESC')
            ->executeQuery();
    }
}
