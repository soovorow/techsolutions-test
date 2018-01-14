<?php

namespace models;

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
        $dom->loadHTML($this->request($url));

        $table = $this->findDomElementByClass($dom,'results')->item(0);

        $data = [];

        if (!$table) {
            return $data;
        }

        foreach ($table->childNodes as $i => $node) {

            if ($this->isValidRow($i, $node)) {
                $data[] = new Ad($this->getAdSchema($node));
            }

        };

        return $data;
    }

    private function getMetroSelectData()
    {
        $cache = new FileCache();
        $cache_id = 'metro';

        if ($data = $cache->get($cache_id)) {
            return $data;
        };

        $dom = new DOMDocument();
        $dom->loadHTML($this->request('https://www.bn.ru/zap_fl_w.phtml?err=0#'));
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

    private function findDomElementByClass($dom, $class)
    {
        $finder = new DomXPath($dom);
        $nodes = $finder->query('//*[contains(concat(\' \', normalize-space(@class), \' \'), \' ' . $class . ' \')]');

        return $nodes;
    }

    private function isValidRow($i, $node)
    {
        return $i > 1 && $node->hasChildNodes() ? $node->childNodes->length > 1 : false;
    }

    private function getAdSchema($node)
    {
        $i = $node->childNodes->item(0)->attributes->length === 0 ? 2 : 1;

        return [
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

    private function request($href)
    {
        $curl = curl_init();
        curl_setopt($curl, CURLOPT_SSL_VERIFYPEER, FALSE);
        curl_setopt($curl, CURLOPT_HEADER, false);
        curl_setopt($curl, CURLOPT_FOLLOWLOCATION, true);
        curl_setopt($curl, CURLOPT_URL, $href);
        curl_setopt($curl, CURLOPT_REFERER, $href);
        curl_setopt($curl, CURLOPT_RETURNTRANSFER, TRUE);
        curl_setopt($curl, CURLOPT_USERAGENT, "Mozilla/5.0 (Windows; U; Windows NT 6.1; en-US) AppleWebKit/533.4 (KHTML, like Gecko) Chrome/5.0.375.125 Safari/533.4");
        $str = curl_exec($curl);
        curl_close($curl);

        return $str;
    }

}