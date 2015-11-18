<?php

namespace xj\sitemap\models;

use yii\base\Model;

/**
 * @author xjflyttp <xjflyttp@gmail.com>
 */
class Sitemap extends Model
{
    public $loc;
    public $lastmod;

    public function rules()
    {
        return [
            ['loc', 'required'],
            ['lastmod', 'string'],
        ];
    }

    public function attributeLabels()
    {
        return [
            'loc' => 'Loc',
            'lastmod' => 'LastMod',
        ];
    }

    /**
     * Create Sitemap Model
     * @return Url
     */
    public static function create($attributes = [])
    {
        $model = new static();
        return $model->setAttributes($attributes);
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
}
