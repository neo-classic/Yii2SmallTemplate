<?php
namespace app\components\behaviors;

use yii\base\Behavior;
use yii\db\ActiveRecord;
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

    private function saveModels($event)
    {
        $files = UploadedFile::getInstance($this->owner, $this->fileField);
        if (isset($files) && count($files) > 0) {
            foreach ($files as $k => $file) {
                $fileName = $this->_getFileName($file);
                if ($file->saveAs($this->_getFolder() . $fileName)) {
                    $fileModel = new $this->relatedModel;
                    $fileModel->{$this->relatedOwnerField} = $this->owner->getPrimaryKey();
                    $fileModel->{$this->relatedModelField} = $fileName;
                    $fileModel->save();
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
        return mt_rand() . '.' . pathinfo($file, PATHINFO_EXTENSION);
    }

    public function getMultiFilePath()
    {
        return \Yii::$app->basePath . '/' . $this->savePath . '/' . $this->owner->getPrimaryKey() . '/';
    }

    public function getFilePath()
    {
        return $this->getMultiFilePath();
    }

    public function deleteFile($fileName)
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
            $this->deleteFile($model->{$this->relatedModelField});
    }
} 