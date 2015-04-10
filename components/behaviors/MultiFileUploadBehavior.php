<?php
namespace app\components\behaviors;

use yii\base\Behavior;
use yii\db\ActiveRecord;
use yii\imagine\Image;
use yii\web\UploadedFile;

class MultiFileUploadBehavior extends Behavior
{

    public $savePath;                       // папка для сохранения (upload/stock)
    public $fileField = 'images';           // поле с файлами из формы
    public $relatedModel;                   // модель в которую сохраняем файлы (StockImage)
    public $relatedModelField = 'file';     // поле модели в которое сохраняем имя файла (StockImage->file)
    public $relatedOwnerField;              // поле модели с id-модели владельца (StockImage->stock_id)

    public function events()
    {
        return [
            ActiveRecord::EVENT_AFTER_INSERT => 'saveModels',
            ActiveRecord::EVENT_AFTER_UPDATE => 'saveModels',
            ActiveRecord::EVENT_BEFORE_DELETE => 'beforeDelete',
        ];
    }

    public function saveModels($event)
    {
        $files = UploadedFile::getInstances($this->owner, $this->fileField);
        if (isset($files) && count($files) > 0) {
            foreach ($files as $k => $file) {
                $fileName = $this->_getFileName($file);
                if ($file->saveAs($this->_getFolder() . 'o_' . $fileName)) {
                    $fileModel = new $this->relatedModel;
                    $fileModel->{$this->relatedOwnerField} = $this->owner->getPrimaryKey();
                    $fileModel->{$this->relatedModelField} = $fileName;
                    $fileModel->save();

                    Image::thumbnail($this->_getFolder() . 'o_' . $fileName, 160, 90)
                        ->save($this->_getFolder() . 's_' . $fileName, ['quality' => 90]);
                    Image::thumbnail($this->_getFolder() . 'o_' . $fileName, 960, 540)
                        ->save($this->_getFolder() . 'l_' . $fileName, ['quality' => 90]);
                    Image::thumbnail($this->_getFolder() . 'o_' . $fileName, 400, 225)
                        ->save($this->_getFolder() . 'm_' . $fileName, ['quality' => 90]);
                    Image::crop($this->_getFolder() . 'm_' . $fileName, 160, 90, [0, 0])
                        ->save($this->_getFolder() . 'c_' . $fileName, ['quality' => 90]);
                } else {
                    die(var_dump($file->getErrors()));
                }
            }
        }
    }

    protected function _getFolder()
    {
        $folder = \Yii::getAlias('@webroot') . '/' . $this->savePath . '/' . $this->owner->getPrimaryKey() . '/';
        if (is_dir($folder) == false)
            mkdir($folder, 0755, true);
        return $folder;
    }

    protected function _getFileName(UploadedFile $file)
    {
        if (isset($this->owner->url)) {
            return $this->owner->url . '_' . mt_rand() . pathinfo($file, PATHINFO_EXTENSION);
        }
        return mt_rand() . '.' . pathinfo($file, PATHINFO_EXTENSION);
    }

    public function getMultiFilePath()
    {
        return $this->_getFolder();
    }

    public function getMultiFileUrl()
    {
        return \Yii::$app->params['urlImg'] . $this->savePath . '/' . $this->owner->getPrimaryKey() . '/';
    }

    public function deleteMultiFile($fileName)
    {
        $file = $this->_getFolder() . $fileName;
        if (file_exists($file) && !is_dir($file)) {
            unlink($file);
        }
    }

    public function beforeDelete($event)
    {
        $models = call_user_func([$this->relatedModel, 'findAll'], [$this->relatedModelField => $this->owner->getPrimaryKey()]);
        foreach ($models as $model)
            $this->deleteMultiFile($model->{$this->relatedModelField});
    }
} 