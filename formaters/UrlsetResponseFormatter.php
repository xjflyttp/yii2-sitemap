<?php

namespace xj\sitemap\formaters;

use DOMDocument;
use DOMElement;
use DOMText;
use xj\sitemap\models\Url;
use yii\base\Arrayable;
use yii\base\Component;
use yii\helpers\ArrayHelper;
use yii\web\Response;
use yii\web\ResponseFormatterInterface;

/**
 * UrlsetResponseFormatter
 */
class UrlsetResponseFormatter extends Component implements ResponseFormatterInterface
{

    const FORMAT_URLSET = 'sitemap-urlset';

    /**
     * @var string the Content-Type header for the response
     */
    public $contentType = 'application/xml';

    /**
     * gzip contentType
     * @var string
     */
    public $gzipContentType = 'application/x-gzip';

    /**
     * @var string the XML version
     */
    public $version = '1.0';

    /**
     * @var string the XML encoding. If not set, it will use the value of [[Response::charset]].
     */
    public $encoding;

    /**
     * @var string the name of the root element.
     */
    public $rootTag = 'urlset';

    /**
     * @var string the name of the elements that represent the array elements with numeric keys.
     */
    public $itemTag = 'url';

    /**
     * xmlns
     * @var string[]
     */
    public $xmlns;

    /**
     * gzip enable.
     * @var bool
     */
    public $gzip = false;

    /**
     * gzip filename
     * @var string
     */
    public $gzipFilename = 'sitemap.xml.gz';

    /**
     * @param string $attribute
     * @param string $value
     * @reutn $this
     */
    public function addXmlns($options)
    {
        $this->xmlns = ArrayHelper::merge($this->xmlns, $options);
        return $this;
    }

    /**
     * Formats the specified response.
     * @param Response $response the response to be formatted.
     */
    public function format($response)
    {
        $charset = $this->encoding === null ? $response->charset : $this->encoding;
        if ($this->gzip) {
            $this->contentType = $this->gzipContentType;
        } elseif (stripos($this->contentType, 'charset') === false) {
            $this->contentType .= '; charset=' . $charset;
        }
        $response->getHeaders()->set('Content-Type', $this->contentType);
        $dom = new DOMDocument($this->version, $charset);
        $dom->formatOutput = true;
        $urlsetElement = $dom->createElement($this->rootTag); //urlset
        $dom->appendChild($urlsetElement);
        foreach ($this->xmlns as $xmlnsAttributeName => $xmlnsAttributeValue) {
            $urlsetElement->setAttributeNS('http://www.w3.org/2000/xmlns/', $xmlnsAttributeName, $xmlnsAttributeValue);
        }

        $this->buildXml($urlsetElement, $response->data, $dom);
        $xmlData = $dom->saveXML();
        //output
        if ($this->gzip) {
            $response->content = gzencode($xmlData);
            $response->getHeaders()->set('Content-Disposition', "attachment; filename=\"{$this->gzipFilename}\"");
        } else {
            $response->content = $xmlData;
        }
    }

    /**
     * @param DOMElement $urlSetElement
     * @param Url[] $urls
     */
    protected function buildXml(&$urlSetElement, $urls, &$dom)
    {
        foreach ($urls as $url) {
            if (false === $url->validate()) {
                continue;//ignore error model
            }
            $urlElement = new DOMElement($this->itemTag);
            $urlSetElement->appendChild($urlElement);
            $url->buildUrlXml($urlElement, $dom);
        }
    }

}
