<?php

namespace xj\sitemap\actions;

use Yii;
use yii\base\Action;
use yii\helpers\Url;
use xj\sitemap\models\Sitemap;
use xj\sitemap\formaters\IndexResponseFormatter;

class SitemapIndexAction extends Action
{
    /**
     * dataProvider
     * @var \yii\data\ActiveDataProvider
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
     * @var \Closure
     * // Closure
     * function($currentPage, $pageParam) {return new Sitemap();}
     */
    public $remap;

    public function init()
    {
        parent::init();

        if (is_callable($this->remap)) {
            $this->isClosure = true;
        } else {
            $this->isClosure = false;
            $this->remap = null;
        }
    }

    /**
     * execute run()
     * @return Url[]
     */
    public function run()
    {
        $response = Yii::$app->response;
        $response->formatters[IndexResponseFormatter::FORMAT_INDEX] = new IndexResponseFormatter();
        $response->format = IndexResponseFormatter::FORMAT_INDEX;

        if (!empty($this->dataList)) {
            $indexModels = $this->dataList;
        } else {
            $indexModels = $this->getFromDataProvider();
        }
        return $indexModels;
    }

    /**
     * getFromDataProvider
     * @return Sitemap[]
     */
    private function getFromDataProvider()
    {
        $dataProvider = $this->dataProvider;
        $dataProvider->prepare();
        $pagination = $dataProvider->getPagination();
        $pageCount = $pagination->pageCount;
        $pageParam = $pagination->pageParam;

        $indexModels = [];
        for ($i = 0; $i < $pageCount; $i++) {
            if ($this->isClosure) {
                $indexModels[] = call_user_func($this->remap, $i + 1, $pageParam);
            } else {
                $indexModels[] = $this->getModel($i + 1, $this->route, $pageParam);
            }
        }

        return $indexModels;
    }

    /**
     * get Default Model
     * @param int $currentPage
     * @param array $route
     * @param int $pageParam
     * @return Sitemap
     */
    private function getModel($currentPage, $route, $pageParam)
    {
        $route[$pageParam] = $currentPage;
        $loc = Url::toRoute($route, true);
        $lastmod = date(DATE_W3C);
        return Sitemap::create($loc, $lastmod);
    }
}
