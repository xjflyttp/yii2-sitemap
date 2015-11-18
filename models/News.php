<?php

namespace xj\sitemap\models;

use yii\base\Model;

/**
 * @author xjflyttp <xjflyttp@gmail.com>
 * @see https://support.google.com/news/publisher/answer/74288?hl=en
 */
class News extends Model
{
    /**
     * @var array|Publication
     */
    public $publication;
    public $access;
    public $genres;
    public $publication_date;
    public $title;
    public $keywords;
    public $stock_tickers;

    public function rules()
    {
        return [
            [['publication', 'publication_date', 'title'], 'required'],
            [['access', 'genres', 'keywords', 'stock_tickers'], 'safe'],
        ];
    }

    public function init()
    {
        parent::init();

        if (is_array($this->publication)) {
            $this->publication = new Publication($this->publication);
        }
    }

    /**
     * @param array|Publication $data
     * @return $this
     */
    public function addPublication($data)
    {
        if ($data instanceof Publication) {
            $this->publication = $data;
        } else {
            $this->publication = new Publication($data);
        }
        return $this;
    }

    /**
     * @param \DOMElement $nodeElement
     * @param \DOMDocument $doc
     */
    public function buildXml(&$nodeElement, &$doc)
    {
        $image = $nodeElement->appendChild($doc->createElement('news:news'));
        $this->publication->buildXml($image, $doc);
        if (!empty($this->access)) {
            $image->appendChild($doc->createElement('news:access', $this->access));
        }
        if (!empty($this->genres)) {
            $image->appendChild($doc->createElement('news:genres', $this->genres));
        }
        if (!empty($this->publication_date)) {
            $image->appendChild($doc->createElement('news:publication_date', $this->publication_date));
        }
        if (!empty($this->title)) {
            $image->appendChild($doc->createElement('news:title', $this->title));
        }
        if (!empty($this->keywords)) {
            $image->appendChild($doc->createElement('news:keywords', $this->keywords));
        }
        if (!empty($this->stock_tickers)) {
            $image->appendChild($doc->createElement('news:stock_tickers', $this->stock_tickers));
        }
    }
}
