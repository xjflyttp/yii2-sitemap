yii2-sitemap
============

composer.json
-----
```json
"require": {
    "xj/yii2-sitemap": "*"
},
```

Action:
===
sitemap urlset
---
```php
'sitemap' => [
    'class' => 'xj\sitemap\actions\SitemapUrlsetAction',
    'dataProvider' => new \yii\data\ActiveDataProvider([
        'query' => \common\models\Tech::find(),
        'pagination' => [
            'pageSize' => 5,
            'pageParam' => 'p',
        ]]),
    'remap' => [
        'loc' => function($model){return 'http://www.163.com/';},
        'lastmod' => function($model){return date(DATE_W3C);},
        'changefreq' => function($model){return \xj\sitemap\models\Url::CHANGEFREQ_NEVER;},
        'priority' => function($model){return 1;},
    ],
    'remap' => function($model) {
        /* @var $model Tech */
        return \xj\sitemap\models\Url::create($loc, $lastmod, $changeFreq, $priority);
    },
],
```

sitemap index
---
```php
//From dataProvider
'sitemap-index' => [
    'class' => 'xj\sitemap\actions\SitemapIndexAction',
    'dataProvider' => new ActiveDataProvider([
        'query' => Tech::find(),
        'pagination' => [
            'pageSize' => 5,
            'pageParam' => 'p',
        ]
    ]),
],
//OR Direct Data
'sitemap-index' => [
    'class' => 'xj\sitemap\actions\SitemapIndexAction',
    'dataList' => [
        \xj\sitemap\models\Sitemap::create('http://sitemap-url-a.xml.gz', date(DATE_W3C)),
        \xj\sitemap\models\Sitemap::create('http://sitemap-url-b.xml.gz', date(DATE_W3C)),
    ]
],
```
UrlManager Rules
---
```php
[
    'class' => 'yii\web\UrlManager',
    'showScriptName' => false,
    'enablePrettyUrl' => true,
    'rules' => [
        'sitemap.xml' => 'site/sitemap-index',
        'sitemap.<p:\d+>.xml' => 'site/sitemap',
    ],
];

```