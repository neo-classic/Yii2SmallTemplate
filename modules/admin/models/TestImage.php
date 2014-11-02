<?php

namespace app\modules\admin\models;

use Yii;

/**
 * This is the model class for table "test_image".
 *
 * @property integer $id
 * @property integer $test_id
 * @property string $file
 *
 * @property Test $test
 */
class TestImage extends \yii\db\ActiveRecord
{
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'test_image';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['test_id', 'file'], 'required'],
            [['test_id'], 'integer'],
            [['file'], 'string', 'max' => 255]
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'test_id' => 'Test ID',
            'file' => 'File',
        ];
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getTest()
    {
        return $this->hasOne(Test::className(), ['id' => 'test_id']);
    }
}