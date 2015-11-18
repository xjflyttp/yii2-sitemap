yii2-sitemap
============

Note
---
```
ver2.0+ Api has change.
Please read README
```

composer.json
---
```json
"require": {
    "xj/yii2-sitemap": "~2.0"
},
```

Actions
---
```php
use app\models\User;
use yii\data\ActiveDataProvider;
use yii\data\ArrayDataProvider;
use xj\sitemap\models\Url;
use xj\sitemap\models\BaiduUrl;
use xj\sitemap\models\Image;
use xj\sitemap\models\News;
use xj\sitemap\actions\SitemapUrlsetAction;
use xj\sitemap\actions\SitemapIndexAction;

public function actions()
{
    return [
        //Google Sitemap By ActiveRecord
        'sitemap-google-index' => [
            'class' => SitemapIndexAction::className(),
            'route' => ['sitemap-google-urlset'],
            'dataProvider' => new ActiveDataProvider([
                'query' => User::find(),
                'pagination' => [
                    'pageParam' => 'p',
                    'pageSize' => 1, //per page 1 record
                ]]),
        ],
        'sitemap-google-urlset' => [
            'class' => SitemapUrlsetAction::className(),
            'gzip' => YII_DEBUG ? false : true,
            'dataProvider' => new ActiveDataProvider([
                'query' => User::find(),
                'pagination' => [
                    'pageParam' => 'p',
                    'pageSize' => 1,
                ]]),
            'remap' => function ($model) {
                    /* @var $model User */
                    $url = Url::create([
                        'loc' => \yii\helpers\Url::to(['user/view', 'username' => $model->username], true),
                        'lastmod' => date(DATE_W3C, $model->updated_at),
                        'changefreq' => Url::CHANGEFREQ_MONTHLY,
                        'priority' => '0.5',
                    ]);
                    //BEGIN AddImage
                    $url->addImage(new Image([
                        'loc' => 'http://example.com/image1.jpg',
                    ]));
                    $url->addImage(new Image([
                        'loc' => 'http://example.com/image2.jpg',
                        'caption' => 'caption of the image.',
                        'geo_location' => 'Limerick, Ireland',
                        'title' => 'The title of the image.',
                        'license' => 'A URL to the license of the image.',
                    ]));
                    //END AddImage
                    // I'm a SplitLine
                    //BEGIN AddNews
                    $url->addNews(new News([
                        'publication' => [
                            'name' => 'The Example Times',
                            'language' => 'en',
                        ],
                        'access' => 'Subscription',
                        'genres' => 'PressRelease, Blog',
                        'publication_date' => '2008-12-23',
                        'title' => 'Companies A, B in Merger Talks',
                        'keywords' => 'business, merger, acquisition, A, B',
                        'stock_tickers' => 'NASDAQ:A, NASDAQ:B',
                    ]));
                    //END AddNews
                    return $url;
            },
        ],

        //Baidu Mobile Sitemap By ActiveRecord
        'sitemap-baidumobile-index' => [
            'class' => SitemapIndexAction::className(),
            'route' => ['sitemap-baidumobile-urlset'],
            'dataProvider' => new ActiveDataProvider([
                'query' => User::find(),
                'pagination' => [
                    'pageParam' => 'p',
                    'pageSize' => 1, //per page 1 record
                ]]),
        ],
        'sitemap-baidumobile-urlset' => [
            'class' => SitemapUrlsetAction::className(),
            'urlClass' => BaiduUrl::className(), //for Baidu
            'gzip' => YII_DEBUG ? false : true,
            'dataProvider' => new ActiveDataProvider([
                'query' => User::find(),
                'pagination' => [
                    'pageParam' => 'p',
                    'pageSize' => 1,
                ]]),
            'remap' => function ($model) {
                /* @var $model User */
                //return Array will auto using $urlClass::create()
                return [
                    'loc' => \yii\helpers\Url::to(['user/view', 'username' => $model->username], true),
                    'lastmod' => date(DATE_W3C, $model->updated_at),
                    'changefreq' => Url::CHANGEFREQ_MONTHLY,
                    'priority' => '0.5',
                    'baiduType' => BaiduUrl::BAIDU_TYPE_MOBILE, // BaiduUrl::BAIDU_TYPE_ADAP | BaiduUrl::BAIDU_TYPE_HTMLADAP
                ];
            },
        ],

        //FOR DIRECT DATA
        'sitemap-direct-index' => [
            'class' => SitemapIndexAction::className(),
            'route' => ['sitemap-direct'],
            'dataProvider' => new ArrayDataProvider([
                'allModels' => [
                    1, 1, 1, 1 //only need number// p=1 | p=2 | p=3 | p=4
                ],
                'pagination' => [
                    'pageParam' => 'p',
                    'pageSize' => 1,
                ]
            ]),
        ],
        'sitemap-direct-urlset' => [
            'class' => SitemapUrlsetAction::className(),
            'gzip' => YII_DEBUG ? false : true,
            'dataProvider' => new ArrayDataProvider([
                'allModels' => [
                    [
                        'loc' => 'http://url-a',
                        'lastmod' => date(DATE_W3C),
                        'changefreq' => Url::CHANGEFREQ_ALWAYS,
                    ],
                    [
                        'loc' => 'http://url-b',
                        'lastmod' => date(DATE_W3C),
                        'changefreq' => Url::CHANGEFREQ_DAILY,
                    ],
                    [
                        'loc' => 'http://error-model',
                        'lastmod' => date(DATE_W3C),
                        'changefreq' => Url::CHANGEFREQ_HOURLY,
                        'priority' => 'errorPriority',
                    ],
                    [
                        'loc' => 'http://url-c',
                        'lastmod' => date(DATE_W3C),
                        'changefreq' => Url::CHANGEFREQ_HOURLY,
                    ],
                ],
                'pagination' => [
                    'pageParam' => 'p',
                    'pageSize' => 4,
                ]
            ]),
            'remap' => function ($model) {
                /* @var $model array */
                return Url::create()->setAttributes($model);
            },
        ],

    ];
}
```

UrlManager
---
```php
[
    'class' => 'yii\web\UrlManager',
    'showScriptName' => false,
    'enablePrettyUrl' => true,
    'rules' => [
        'sitemap.xml' => 'sitemap/sitemap-google-index',
        'sitemap.<p:\d+>.xml.gz' => 'sitemap/sitemap-google-urlset',
    ],
];
```

Access
---
```
http://domain/sitemap.xml
http://domain/sitemap.1.xml.gz
```