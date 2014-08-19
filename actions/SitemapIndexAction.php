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
     * custom data list
     * @var []
     */
    public $dataList = [];

    /**
     * default route to Sitemap urlset
     * @var []
     */
    public $route = ['site/sitemap'];

    /**
     * remap type
     * @var bool
     */
    private $isClosure;
    
    /**
     * Custom Loc Index
     * @var Closure
     * // Closure
     * function($currentPage, $pageParm) {return new Sitemap();}
     */
    public $remap;

    public function init() {

        if (is_callable($this->remap)) {
            $this->isClosure = true;
        } else {
            $this->isClosure = false;
            $this->remap = null;
        }

        return parent::init();
    }

    /**
     * execute run()
     * @return []Url
     */
    public function run() {
        $response = Yii::$app->response;
        $response->formatters[IndexResponseFormatter::FORMAT_INDEX] = new IndexResponseFormatter([]);
        $response->format = IndexResponseFormatter::FORMAT_INDEX;

        $indexModels = [];
        if (!empty($this->dataList)) {
            $indexModels = $this->dataList;
        } else {
            $indexModels = $this->getFromDataProvider();
        }
        return $indexModels;
    }

    /**
     * getFromDataProvider
     * @return []Sitemap
     */
    private function getFromDataProvider() {
        $dataProvider = $this->dataProvider;
        $pagination = $dataProvider->getPagination();
        $pageCount = $dataProvider->getCount();
        $pageParm = $pagination->pageParam;

        $indexModels = [];
        for ($i = 0; $i < $pageCount; ++$i) {
            if ($this->isClosure) {
                $indexModels[] = call_user_func($this->remap, $i, $pageParm);
            } else {
                $indexModels[] = $this->getModel($i, $this->route, $pageParm);
            }
        }

        return $indexModels;
    }

    /**
     * get Default Model
     * @param int $currentPage
     * @param array $route
     * @param int $pageParm
     * @return Sitemap
     */
    private function getModel($currentPage, $route, $pageParm) {
        $route[$pageParm] = $currentPage;
        $loc = Url::toRoute($route, true);
        $lastmod = date(DATE_W3C);
        return Sitemap::create($loc, $lastmod);
    }

}
