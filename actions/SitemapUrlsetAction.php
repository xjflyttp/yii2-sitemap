<?php

namespace xj\sitemap\actions;

use Yii;
use yii\base\Action;
use yii\base\ErrorException;
use xj\sitemap\models\Url;
use xj\sitemap\formaters\UrlsetResponseFormatter;
use yii\base\InvalidConfigException;

class SitemapUrlsetAction extends Action
{

    /**
     * dataProvider
     * @var \yii\data\ActiveDataProvider
     */
    public $dataProvider;

    /**
     * return (array) OR ('xj\sitemap\models\Url' | 'xj\sitemap\models\BaiduUrl')
     * array will using $urlClass::create() instance
     * @var Closure
     */
    public $remap;

    /**
     * enable gzip
     * @var bool
     */
    public $gzip = false;

    /**
     * if $remap === null
     * will use $urlClass to create model
     * @var string
     */
    public $urlClass = 'xj\sitemap\models\Url';

    /**
     * addition xmlns
     * @var []
     * @example
     * ['xmlns:mobile' => 'http://www.baidu.com/schemas/sitemap-mobile/1/']
     */
    public $xmlns;

    /**
     * init
     */
    public function init()
    {
        //init dataProvider
        $this->dataProvider->prepare();

        return parent::init();
    }

    /**
     * run
     * @return Url[]
     */
    public function run()
    {
        //setFormat
        $this->setFormatters();

        //return Url models
        return $this->getFromDataProvider();
    }

    /**
     * set Action Response Formatter
     */
    protected function setFormatters()
    {
        $currentPage = $this->dataProvider->getPagination()->getPage() + 1;
        $response = Yii::$app->response;
        $formatter = new UrlsetResponseFormatter([
            'gzip' => $this->gzip,
            'gzipFilename' => 'sitemap.' . $currentPage . '.xml.gz',
        ]);
        $xmlns = $this->xmlns;
        if (null === $xmlns) {
            $xmlns = call_user_func([$this->urlClass, 'getXmlns']);
        }
        $formatter->addXmlns($xmlns);
        $response->formatters[UrlsetResponseFormatter::FORMAT_URLSET] = $formatter;
        $response->format = UrlsetResponseFormatter::FORMAT_URLSET;
    }

    /**
     *
     * @return Url[]
     */
    protected function getFromDataProvider()
    {
        $urlClassName = $this->urlClass;
        $models = $this->dataProvider->getModels();
        $outModels = [];
        $hasRemap = $this->remap !== null;
        foreach ($models as $model) {
            if ($hasRemap) {
                $remapResult = call_user_func($this->remap, $model);
                if (is_array($remapResult)) {
                    $outModels[] = call_user_func([$urlClassName, 'create'], $remapResult);
                } else {
                    $outModels[] = $remapResult;
                }
            } else {
                $outModels[] = $model;
            }
        }
        return $outModels;
    }

}
