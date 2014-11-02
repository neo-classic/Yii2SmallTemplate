<?php

namespace app\modules\admin\models;

use app\components\behaviors\MultiFileUploadBehavior;
use Yii;

/**
 * This is the model class for table "test".
 *
 * @property integer $id
 * @property string $title
 * @property string $url
 * @property string $logo
 * @property string $image
 * @property string $created_date
 * @property string $updated_date
 *
 * @property TestImage[] $testImages
 */
class Test extends \yii\db\ActiveRecord
{
    public $imageArray = [];
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'test';
    }

    public function behaviors()
    {
        return [
            [
                'class' => MultiFileUploadBehavior::className(),
                'savePath' => 'uploads/test',
                'fileField' => 'imageArray',
                'relatedModel' => 'app\modules\admin\models\TestImage',
                'relatedModelField' => 'file',
                'relatedOwnerField' => 'test_id',
            ],
        ];
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['title', 'url'], 'required'],
            [['created_date', 'updated_date', 'imageArray'], 'safe'],
            [['title', 'url'], 'string', 'max' => 50],
            [['logo', 'image'], 'string', 'max' => 255]
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'title' => 'Название',
            'url' => 'Url',
            'logo' => 'Logo',
            'image' => 'Image',
            'created_date' => 'Добавлено',
            'updated_date' => 'Обновлено',
        ];
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getImages()
    {
        return $this->hasMany(TestImage::className(), ['test_id' => 'id']);
    }
}