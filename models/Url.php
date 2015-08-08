<?php

namespace xj\sitemap\models;

use DOMElement;
use DOMText;
use yii\base\Model;

class Url extends Model
{

    const CHANGEFREQ_ALWAYS = 'always';
    const CHANGEFREQ_HOURLY = 'hourly';
    const CHANGEFREQ_DAILY = 'daily';
    const CHANGEFREQ_WEEKLY = 'weekly';
    const CHANGEFREQ_MONTHLY = 'monthly';
    const CHANGEFREQ_YEARLY = 'yearly';
    const CHANGEFREQ_NEVER = 'never';

    /**
     * @var string
     */
    public $loc;

    /**
     * @var string
     */
    public $lastmod;

    /**
     * @var string
     */
    public $changefreq;

    /**
     * @var string
     */
    public $priority;

    public function rules()
    {
        return [
            ['loc', 'required'],
            ['lastmod', 'string'],
            ['changefreq', 'in', 'range' => array_keys(static::getChangefreqOptions())],
            ['priority', 'number', 'min' => 0.1, 'max' => 1],
        ];
    }

    public function attributeLabels()
    {
        return [
            'loc' => 'Loc',
            'lastmod' => 'LastMod',
            'changefreq' => 'ChangeFreq',
            'priority' => 'Priority',
        ];
    }

    /**
     * @return array
     */
    public static function getXmlns()
    {
        return [
            'xmlns' => 'http://www.sitemaps.org/schemas/sitemap/0.9',
        ];
    }

    /**
     * Change Freq Options
     * @return []
     */
    public static function getChangefreqOptions()
    {
        return [
            self::CHANGEFREQ_ALWAYS => 'Always',
            self::CHANGEFREQ_HOURLY => 'Hourly',
            self::CHANGEFREQ_DAILY => 'Daily',
            self::CHANGEFREQ_WEEKLY => 'Weekly',
            self::CHANGEFREQ_MONTHLY => 'Monthly',
            self::CHANGEFREQ_YEARLY => 'Yearly',
            self::CHANGEFREQ_NEVER => 'Never',
        ];
    }

    /**
     * Change Freq Text
     * @return string
     */
    public function getChangeFreqText()
    {
        $options = $this->getChangefreqOptions();
        return isset($options[$this->changefreq]) ? $options[$this->changefreq] : $this->changefreq;
    }

    /**
     * Create SitemapUrl
     * @return Url
     */
    public static function create($attributes = [])
    {
        $model = new static();
        return $model->setAttributes($attributes);
    }

    /**
     * @param array $values
     * @param bool|true $safeOnly
     * @return $this
     */
    public function setAttributes($values, $safeOnly = true)
    {
        parent::setAttributes($values, $safeOnly);
        return $this;
    }

    /**
     * @param \DOMElement $urlElement <url> Element
     */
    public function buildUrlXml(&$urlElement)
    {
        $loc = new DOMElement('loc');
        $urlElement->appendChild($loc);
        $loc->appendChild(new DOMText($this->loc));

        if (!empty($this->lastmod)) {
            $lastmod = new DOMElement('lastmod');
            $urlElement->appendChild($lastmod);
            $lastmod->appendChild(new DOMText($this->lastmod));
        }

        if (!empty($this->changefreq)) {
            $changefreq = new DOMElement('changefreq');
            $urlElement->appendChild($changefreq);
            $changefreq->appendChild(new DOMText($this->getChangeFreqText()));
        }

        if (!empty($this->priority)) {
            $priority = new DOMElement('priority');
            $urlElement->appendChild($priority);
            $priority->appendChild(new DOMText($this->priority));
        }
    }
}
