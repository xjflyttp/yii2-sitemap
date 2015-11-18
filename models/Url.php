<?php

namespace xj\sitemap\models;

use DOMDocument;
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

    /**
     * @var Image[]
     */
    public $images = [];

    /**
     * @var News[]
     */
    public $news = [];

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
            'xmlns:image' => 'http://www.google.com/schemas/sitemap-image/1.1',
            'xmlns:news' => 'http://www.google.com/schemas/sitemap-news/0.9',
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
     * @param array|Image $data
     * @return $this
     */
    public function addImage($data)
    {
        if ($data instanceof Image) {
            $this->images[] = $data;
        } else {
            $this->images[] = new Image($data);
        }
        return $this;
    }

    /**
     * @param array|News $data
     * @return $this
     */
    public function addNews($data)
    {
        if ($data instanceof News) {
            $this->news[] = $data;
        } else {
            $this->news[] = new News($data);
        }
        return $this;
    }

    /**
     * Create SitemapUrl
     * @param array $attributes
     * @return Url
     */
    public static function create($attributes = [])
    {
        return new static($attributes);
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
     * @param DOMElement $urlElement
     * @param DOMDocument $doc
     */
    public function buildUrlXml(&$urlElement, &$doc)
    {
        $urlElement->appendChild($doc->createElement('loc', $this->loc));

        if (!empty($this->lastmod)) {
            $urlElement->appendChild($doc->createElement('lastmod', $this->lastmod));
        }

        if (!empty($this->changefreq)) {
            $urlElement->appendChild($doc->createElement('changefreq', $this->changefreq));
        }

        if (!empty($this->priority)) {
            $urlElement->appendChild($doc->createElement('priority', $this->priority));
        }

        if (count($this->images) > 0) {
            foreach ($this->images as $mImage) {
                $mImage->buildXml($urlElement, $doc);
            }
        }

        if (count($this->news) > 0) {
            foreach ($this->news as $mNews) {
                $mNews->buildXml($urlElement, $doc);
            }
        }
    }

}
