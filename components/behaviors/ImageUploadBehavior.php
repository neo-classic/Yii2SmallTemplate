<?php
namespace app\components\behaviors;

use app\components\helpers\CommonHelper;
use yii\base\Behavior;
use yii\db\ActiveRecord;
use yii\web\UploadedFile;

class ImageUploadBehavior extends Behavior
{

    private $_fileInstance = [];
    private $_oldLogo = [];
    private $_fileName = [];

    public $fields = [
        'file' => [                     // Имя поля в модели
            'path' => '',               // Директория для хранения файлов
            'translitField' => null,    // Поле, по которому делать транслит имени файла
        ],
    ];

    public function events()
    {
        return [
            ActiveRecord::EVENT_BEFORE_INSERT => 'beforeSave',
            ActiveRecord::EVENT_BEFORE_UPDATE => 'beforeSave',
            ActiveRecord::EVENT_AFTER_INSERT => 'afterSave',
            ActiveRecord::EVENT_AFTER_UPDATE => 'afterSave',
            ActiveRecord::EVENT_AFTER_FIND => 'afterFind',
            ActiveRecord::EVENT_BEFORE_DELETE => 'beforeDelete',
        ];
    }

    public function beforeSave($event)
    {
        foreach ($this->fields as $field => $options) {
            $this->_fileInstance[$field] = UploadedFile::getInstance($this->owner, $field);

            if ($this->_fileInstance[$field]) {
                $this->owner->{$field} = $this->getFileName($field);
            } else {
                $this->owner->{$field} = $this->_oldLogo[$field];
            }
        }
    }

    public function afterSave($event)
    {
        if (is_array($this->_fileInstance) && count($this->_fileInstance) >= 1) {
            foreach ($this->_fileInstance as $field => $uploadInstance) {
                if (!is_null($uploadInstance)) {
                    $this->makeDefDir($field);

                    $folder = $this->getFolderPath($field);
                    $this->_fileInstance[$field]->saveAs($folder . $this->getFileName($field));
                    /*\Yii::$app->image->load($folder . $this->owner->{$field})->
                    resize(160, 90, Image::WIDTH)->quality(90)->
                    save($folder . 'thumb_' . $this->getFileName($field));*/
                } else {
                    continue;
                }
            }
        }
    }

    public function afterFind($event)
    {
        if (!$this->owner->isNewRecord) {
            foreach ($this->fields as $field => $options) {
                $this->_oldLogo[$field] = $this->owner->{$field};
            }
        }
    }

    public function beforeDelete($event)
    {
        $this->deleteFiles();
    }

    public function getFileName($field)
    {
        if (!isset($this->_fileName[$field])) {
            if (is_null($this->fields[$field]['translitField'])) {
                $this->_fileName[$field] = md5(microtime()) . '.' . pathinfo($this->_fileInstance[$field], PATHINFO_EXTENSION);
            } else {
                $this->_fileName[$field] = CommonHelper::translit($this->owner->{$this->fields[$field]['translitField']})
                    . '.' . pathinfo($this->_fileInstance[$field], PATHINFO_EXTENSION);
            }
        }

        return $this->_fileName[$field];
    }

    public function makeDefDir($field)
    {
        $folder = $this->getFolderPath($field);

        if (is_dir($folder) == false) {
            mkdir($folder, 0755, true);
        }
    }

    public function getFolderPath($field)
    {
        return \Yii::getAlias('@webroot') . '/' . $this->fields[$field]['path'] . '/' . $this->owner->getPrimaryKey() . '/';
    }

    public function getBaseImageUrl($field)
    {
        return \Yii::$app->params['urlImg'] . $this->fields[$field]['path'] . '/' . $this->owner->getPrimaryKey() . '/';
    }

    public function deleteFiles()
    {
        foreach ($this->fields as $field => $options) {
            $file = $this->getFolderPath($field) . $this->owner->{$field};
            if (file_exists($file) && !is_dir($file)) {
                unlink($file);
                if (count(scandir($this->getFolderPath($field)) < 3)) {
                    `rm -rf {$this->getFolderPath($field)}`;
                }
                $this->owner->{$field} = '';
            }
        }
    }

    public function getImageUrl($field)
    {
        return $this->getBaseImageUrl($field) . $this->owner->{$field};
    }

    public function getImagePath($field)
    {
        return $this->getFolderPath($field) . $this->owner->{$field};
    }

    public function getImageThumbUrl($field)
    {
        return $this->getBaseImageUrl($field) . 'thumb_' . $this->owner->{$field};
    }

    public function createWM($image)
    {
        Yii::app()->ih
            ->load($image)
            ->watermark($_SERVER['DOCUMENT_ROOT'] . '/images/watermark.png', 10, 20, CImageHandler::CORNER_RIGHT_TOP)
            ->save($image);
    }

    public function removeImage()
    {
        $this->deleteFiles();

        foreach ($this->fields as $field => $options) {
            $sql = 'UPDATE `' . $this->owner->tableName() . '` SET ' . $field . '=NULL WHERE id=' . $this->owner->getPrimaryKey();
            if (Yii::app()->db->createCommand($sql)->execute()) {
                return true;
            } else {
                return false;
            }
        }
    }

}
