<?php
namespace app\components;

/**
 * Class Sitemap
 * Генерирует sitemap.xml для выбранных моделей
 *
 * В модели нужно добавить:
 *
 * private $_url;
 *
 * public function scopes() {
 *    return array(
 *      'published' => array(
 *          'condition' => 'status = ' . self::STATUS_PUBLISHED,
 *          'order' => 'createTime DESC',
 *      ),
 *    );
 * }
 *
 * public function getUrl() {
 *    if ($this->_url === null)
 *      $this->_url = Yii::app()->createUrl('post/view', array('url' => $this->url));
 *    return $this->_url;
 * }
 */
class Sitemap
{

    const ALWAYS = 'always';
    const HOURLY = 'hourly';
    const DAILY = 'daily';
    const WEEKLY = 'weekly';
    const MONTHLY = 'monthly';
    const YEARLY = 'yearly';
    const NEVER = 'never';

    protected $items = array();

    /**
     * @param $url
     * @param string $changeFreq
     * @param float $priority
     * @param int $lastmod
     */
    public function addUrl($url, $changeFreq = self::DAILY, $priority = 0.5, $lastMod = 0)
    {
        $host = \Yii::$app->request->hostInfo;
        $item = array(
            'loc' => $host . $url,
            'changefreq' => $changeFreq,
            'priority' => $priority
        );
        if ($lastMod) {
            $item['lastmod'] = $this->dateToW3C($lastMod);
        }

        $this->items[] = $item;
    }

    /**
     * @param ActiveRecord[] $models
     * @param string $changeFreq
     * @param float $priority
     */
    public function addModels($models, $changeFreq = self::DAILY, $priority = 0.5)
    {
        $host = \Yii::$app->request->hostInfo;
        foreach ($models as $model) {
            $item = array(
                'loc' => $host . $model->getUrl(),
                'changefreq' => $changeFreq,
                'priority' => $priority
            );

            if ($model->hasAttribute('updateTime')) {
                $item['lastmod'] = date('Y-m-d', $model->createTime);
            }

            $this->items[] = $item;
        }
    }

    /**
     * @return string XML code
     */
    public function render()
    {
        $dom = new DOMDocument('1.0', 'utf-8');
        $urlset = $dom->createElement('urlset');
        $urlset->setAttribute('xmlns', 'http://www.sitemaps.org/schemas/sitemap/0.9');
        $urlset->setAttribute('xmlns:xsi', 'http://www.w3.org/2001/XMLSchema-instance');
        $urlset->setAttribute('xsi:schemaLocation',
            'http://www.sitemaps.org/schemas/sitemap/0.9 http://www.sitemaps.org/schemas/sitemap/0.9/sitemap.xsd');
        foreach ($this->items as $item) {
            $url = $dom->createElement('url');

            foreach ($item as $key => $value) {
                $elem = $dom->createElement($key);
                $elem->appendChild($dom->createTextNode($value));
                $url->appendChild($elem);
            }

            $urlset->appendChild($url);
        }
        $dom->appendChild($urlset);

        return $dom->saveXML();
    }
} 