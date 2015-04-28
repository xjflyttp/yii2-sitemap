<?php

namespace xj\sitemap\actions;

use Yii;
use yii\base\Action;
use yii\base\ErrorException;
use xj\sitemap\models\Url;
use xj\sitemap\formaters\UrlsetResponseFormatter;

class SitemapUrlsetAction extends Action
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
     * remap type
     * @var bool
     */
    private $isClosure;

    /**
     * Remap Data to Url
     * @var \Closure|array
     */
    public $remap;

    /**
     * gzip package.
     * @var bool
     */
    public $gzip = false;

    public function init()
    {
        parent::init();

        if (!empty($this->dataList)) {
            $this->isClosure = false;
            $this->remap = null;
        } elseif (is_array($this->remap)) {
            $this->isClosure = false;
        } elseif (is_callable($this->remap)) {
            $this->isClosure = true;
        } else {
            throw new ErrorException('remap is wrong type!.');
        }
    }

    /**
     * execute run()
     * @return Url[]
     */
    public function run()
    {
        $formatterModel = new UrlsetResponseFormatter(['gzip' => $this->gzip]);

        $response = Yii::$app->response;
        $response->formatters[UrlsetResponseFormatter::FORMAT_URLSET] = $formatterModel;
        $response->format = UrlsetResponseFormatter::FORMAT_URLSET;

        if (!empty($this->dataList)) {
            $urlModels = $this->dataList;
            $formatterModel->gzipFilename = 'sitemap.xml.gz';
        } else {
            $urlModels = $this->getFromDataProvider();
            $formatterModel->gzipFilename = 'sitemap.' . $this->dataProvider->getPagination()->getPage() . '.xml.gz';
        }
        return $urlModels;
    }

    /**
     * getFromDataProvider
     * @return Url[]
     */
    private function getFromDataProvider()
    {
        $remap = $this->remap;
        $models = $this->dataProvider->getModels();
        $oModels = [];
        foreach ($models as $model) {
            if ($this->isClosure) {
                $oModels[] = call_user_func($remap, $model);
            } else {
                $oModels[] = $this->remapModel($model, $this->remap);
            }
        }
        return $oModels;
    }

    /**
     * SourceModel Remap to SitemapModel
     * @param \yii\base\Model $model SourceModel
     * @param array $remap Remap Table
     * @return Url
     */
    private function remapModel($model, $remap)
    {
        $oModel = new Url();
        foreach ($remap as $dst => $src) {
            if (is_callable($src)) {
                $oModel->$dst = call_user_func($src, $model);
            } else {
                $oModel->$dst = $model->$src;
            }
        }
        return $oModel;
    }

}
