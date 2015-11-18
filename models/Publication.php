<?php

namespace xj\sitemap\models;

use yii\base\Model;

/**
 * @author xjflyttp <xjflyttp@gmail.com>
 */
class Publication extends Model
{

    public $name;
    public $language;

    public function rules()
    {
        return [
            [['name', 'language'], 'required'],
        ];
    }

    /**
     * @param string $name
     * @param string $language
     * @return Publication
     */
    public static function create($name, $language)
    {
        return new static([
            'name' => $name,
            'language' => $language,
        ]);
    }

    /**
     * @param \DOMElement $nodeElement
     * @param \DOMDocument $doc
     */
    public function buildXml(&$nodeElement, &$doc)
    {
        $node = $nodeElement->appendChild($doc->createElement('news:publication'));
        $node->appendChild($doc->createElement('news:name', $this->name));
        $node->appendChild($doc->createElement('news:language', $this->language));
    }
}
