<?php

namespace models;

use components\Curl;
use components\FileCache;
use DOMDocument;
use DOMXPath;

class AdSearch extends Model
{
    public $kkv1 = null;
    public $kkv2 = null;
    public $price1 = null;
    public $price2 = null;
    public $metro = null;

    public $metro_options;

    public function __construct(array $params = [])
    {
        parent::__construct($params);

        $this->metro_options = $this->getMetroSelectData();
    }

    public function search()
    {
        $url = 'https://www.bn.ru/zap_fl.phtml?' . http_build_query([
            'kkv1' => $this->kkv1,
            'kkv2' => $this->kkv2,
            'price1' => $this->price1,
            'price2' => $this->price2,
            'metro' => $this->metro,
        ]);

        libxml_use_internal_errors(true);

        $dom = new DOMDocument();
        $dom->loadHTML(Curl::execute($url));

        $table = $this->findDomElementByClass($dom, 'results')->item(0);

        $data = [];

        if (!$table) {
            return $data;
        }

        foreach ($table->childNodes as $i => $node) {
            if ($this->isValidRow($i, $node)) {
                $data[] = new Ad($this->getAdSchema($node));
            }
        };

        $detail_pages = Curl::executeMultiple($this->getUrlsArrayFromData($data));

        foreach ($detail_pages as $i => $result) {
            $data[$i]->metro = $this->getMetro($result);
        }

        return $data;
    }

    private function getMetroSelectData()
    {
        $cache = new FileCache();
        $cache_id = 'metro';
        $url = 'https://www.bn.ru/zap_fl_w.phtml?err=0#';

        if ($data = $cache->get($cache_id)) {
            return $data;
        };

        $dom = new DOMDocument();
        $dom->loadHTML(Curl::execute($url));
        $select = $dom->getElementById('metro');

        $data = [];

        foreach ($select->childNodes as $node) {
            $data[] = [
                'option' => $node->nodeValue,
                'value' => $node->getAttribute('value'),
            ];
        };

        $cache->save($cache_id, $data);

        return $data;
    }

    private function isValidRow($i, $node)
    {
        return $i > 1 && $node->hasChildNodes() ? $node->childNodes->length > 1 : false;
    }

    private function getAdSchema($node)
    {
        $i = $node->childNodes->item(0)->attributes->length === 0 ? 2 : 1;

        return [
            'url' => 'https://www.bn.ru' . $node->childNodes->item($i)->getElementsByTagName('a')->item(0)->getAttribute('href'),
            'address' => $node->childNodes->item($i)->nodeValue,
            'floor' => explode('/', $node->childNodes->item($i + 1)->nodeValue)[0],
            'building_height' => explode('/', $node->childNodes->item($i + 1)->nodeValue)[1],
            'building_type' => $node->childNodes->item($i + 2)->nodeValue,
            'total_area' => $node->childNodes->item($i + 3)->nodeValue,
            'living_area' => $node->childNodes->item($i + 4)->nodeValue,
            'kitchen_area' => $node->childNodes->item($i + 5)->nodeValue,
            'bathroom' => $node->childNodes->item($i + 6)->nodeValue,
            'subject' => $node->childNodes->item($i + 9)->nodeValue,
            'contact' => $node->childNodes->item($i + 10)->nodeValue,
            'note' => $node->childNodes->item($i + 11)->nodeValue,
        ];
    }

    private function getMetro($result)
    {
        $dom = new DOMDocument();
        $dom->loadHTML($result);
        $metro = $this->findDomElementByClass($dom, 'metro');
        $result = [];
        foreach ($metro as $m) {
            $result[] = $m->nodeValue;
        }
        return implode(', ', $result);
    }

    private function findDomElementByClass($dom, $class)
    {
        $finder = new DomXPath($dom);
        $nodes = $finder->query('//*[contains(concat(\' \', normalize-space(@class), \' \'), \' ' . $class . ' \')]');

        return $nodes;
    }

    private function getUrlsArrayFromData($data)
    {
        $urls = [];
        foreach ($data as $i => $ad) {
            $urls[$i] = $ad->url;
        }
        return $urls;
    }
}