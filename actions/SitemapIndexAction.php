<?php

namespace xj\sitemap\actions;

use Yii;
use yii\base\Action;
use yii\helpers\Url;
use xj\sitemap\models\Sitemap;
use xj\sitemap\formaters\IndexResponseFormatter;

class SitemapIndexAction extends Action {

    /**
     * dataProvider
     * @var ActiveDataProvider
     */
    public $dataProvider;

    /**
     * default route to Sitemap urlset
     * @var []
     */
    public $route = ['site/sitemap'];

    /**
     * Custom Loc Index
     * @var Closure
     * @example
     * function($currentPage, $pageParam) {return new Sitemap();}
     */
    public $remap;

    /**
     * if $remap === null
     * will use $indexClass to create model
     * @var string
     */
    public $indexClass = 'xj\sitemap\models\Sitemap';

    public function init() {
        //init dataProvider
        $this->dataProvider->prepare();

        return parent::init();
    }

    /**
     * 
     * @return []Sitemap
     */
    public function run() {
        //set format
        $this->setFormatters();

        //return Sitemap models
        return $this->getFromDataProvider();
    }

    protected function setFormatters() {
        $response = Yii::$app->response;
        $response->formatters[IndexResponseFormatter::FORMAT_INDEX] = new IndexResponseFormatter();
        $response->format = IndexResponseFormatter::FORMAT_INDEX;
    }

    /**
     * getFromDataProvider
     * @return Sitemap[]
     */
    protected function getFromDataProvider() {
        $pagination = $this->dataProvider->getPagination();
        $pageCount = $pagination->pageCount;
        $pageParam = $pagination->pageParam;

        $outModels = [];
        $hasRemap = $this->remap !== null;
        for ($i = 0; $i < $pageCount; ++$i) {
            $currentPage = $i + 1;
            if ($hasRemap) {
                $outModels[] = call_user_func($this->remap, $currentPage, $pageParam);
            } else {
                $outModels[] = $this->getModel($currentPage, $this->route, $pageParam);
            }
        }

        return $outModels;
    }

    /**
     * get Default Model
     * @param int $currentPage
     * @param array $route
     * @param int $pageParam
     * @return Sitemap
     */
    protected function getModel($currentPage, $route, $pageParam) {
        $indexClassName = $this->indexClass;
        $route[$pageParam] = $currentPage;
        $loc = Url::toRoute($route, true);
        $lastmod = date(DATE_W3C);
        return call_user_func([$indexClassName, 'create'], ['loc' => $loc, 'lastmod' => $lastmod]);
    }

}
