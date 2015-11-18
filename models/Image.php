<?php

namespace xj\sitemap\models;

use yii\base\Model;

/**
 * @author xjflyttp <xjflyttp@gmail.com>
 * @see https://support.google.com/webmasters/answer/178636?hl=en
 */
class Image extends Model
{

    public $loc;
    public $caption;
    public $geo_location;
    public $title;
    public $license;

    public function rules()
    {
        return [
            ['loc', 'required'],
            ['caption', 'string'],
            ['geo_location', 'string'],
            ['title', 'string'],
            ['license', 'string'],
        ];
    }

    /**
     * Create Sitemap
     * @param string $loc
     * @param string $lastmod
     * @return Sitemap
     */
    public static function create($loc, $lastmod)
    {
        $model = new static();
        $model->attributes = [
            'loc' => $loc,
            'lastmod' => $lastmod,
        ];
        return $model;
    }

    /**
     * @param \DOMElement $urlElement
     * @param \DOMDocument $doc
     */
    public function buildXml(&$urlElement, &$doc)
    {
        $image = $urlElement->appendChild($doc->createElement('image:image'));
        $image->appendChild($doc->createElement('image:loc', $this->loc));
        if (!empty($this->caption)) {
            $image->appendChild($doc->createElement('image:caption', $this->caption));
        }
        if (!empty($this->geo_location)) {
            $image->appendChild($doc->createElement('image:geo_location', $this->geo_location));
        }
        if (!empty($this->title)) {
            $image->appendChild($doc->createElement('image:title', $this->title));
        }
        if (!empty($this->license)) {
            $image->appendChild($doc->createElement('image:license', $this->license));
        }
    }
}
