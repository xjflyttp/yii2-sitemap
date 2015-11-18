<?php

namespace xj\sitemap\models;

use DOMDocument;
use DOMElement;
use yii\helpers\ArrayHelper;

/**
 * @author xjflyttp <xjflyttp@gmail.com>
 */
class BaiduUrl extends Url
{
    const BAIDU_TYPE_MOBILE = 1;// '<mobile:mobile type="mobile"/>';
    const BAIDU_TYPE_ADAP = 2;//'<mobile:mobile type="pc,mobile"/>';
    const BAIDU_TYPE_HTMLADAP = 3;//'<mobile:mobile type="htmladapt"/>';

    public $baiduType;

    /**
     * @return array
     */
    public function rules()
    {
        return ArrayHelper::merge(parent::rules(), [
            ['baiduType', 'in', 'range' => array_keys(static::getBaiduTypeOptions())],
        ]);
    }

    /**
     * @return array
     */
    public static function getXmlns()
    {
        return ArrayHelper::merge(parent::getXmlns(), [
            'xmlns:mobile' => 'http://www.baidu.com/schemas/sitemap-mobile/1/',
        ]);
    }

    /**
     * @return array
     */
    public static function getBaiduTypeOptions()
    {
        return [
            self::BAIDU_TYPE_MOBILE => 'mobile',
            self::BAIDU_TYPE_ADAP => 'pc,mobile',
            self::BAIDU_TYPE_HTMLADAP => 'htmladapt',
        ];
    }

    /**
     * @return string
     */
    public function getBaiduTypeText()
    {
        $options = static::getBaiduTypeOptions();
        return isset($options[$this->baiduType]) ? $options[$this->baiduType] : '';
    }

    /**
     * @param DOMElement $nodeElement
     * @param DOMDocument $doc
     */
    public function appendBaiduType(&$nodeElement, &$doc)
    {
        if (empty($this->baiduType)) {
            return;
        }
        $baiduTypeElement = $doc->createElement('mobile:mobile');
        $baiduTypeElement->setAttribute('type', $this->getBaiduTypeText());
        $nodeElement->appendChild($baiduTypeElement);
    }

    /**
     * @param DOMElement $nodeElement
     * @param DOMDocument $doc
     */
    public function buildUrlXml(&$nodeElement, &$doc)
    {
        parent::buildUrlXml($nodeElement, $doc);
        $this->appendBaiduType($nodeElement, $doc);
    }
}