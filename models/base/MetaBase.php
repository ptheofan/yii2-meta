<?php

namespace ptheofan\meta\models\base;

use Yii;

/**
 * This is the model class for table "meta".
 *
 * @property integer $id_meta
 * @property string $route
 * @property resource $params
 * @property string $robots_index
 * @property string $robots_follow
 * @property string $meta_title
 * @property string $meta_keywords
 * @property string $meta_description
 * @property integer $sitemap
 * @property string $sitemap_change_freq
 * @property string $sitemap_priority
 * @property string $created_at
 * @property string $updated_at
 */
class MetaBase extends \yii\db\ActiveRecord
{
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'meta';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['route', 'created_at', 'updated_at'], 'required'],
            [['params', 'robots_index', 'robots_follow', 'meta_keywords', 'meta_description'], 'string'],
            [['sitemap'], 'integer'],
            [['created_at', 'updated_at'], 'safe'],
            [['route', 'meta_title'], 'string', 'max' => 255],
            [['sitemap_change_freq'], 'string', 'max' => 20],
            [['sitemap_priority'], 'string', 'max' => 4]
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'id_meta' => 'Id Meta',
            'route' => 'Route',
            'params' => 'Params',
            'robots_index' => 'Robots Index',
            'robots_follow' => 'Robots Follow',
            'meta_title' => 'Meta Title',
            'meta_keywords' => 'Meta Keywords',
            'meta_description' => 'Meta Description',
            'sitemap' => 'Sitemap',
            'sitemap_change_freq' => 'Sitemap Change Freq',
            'sitemap_priority' => 'Sitemap Priority',
            'created_at' => 'Created At',
            'updated_at' => 'Updated At',
        ];
    }
}
