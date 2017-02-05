<?php
namespace app\components\behaviors;

use yii\base\Behavior;
use yii\imagine\Image;

class ImageFromDbBehavior extends Behavior
{
    public $imageField = 'file';
    public $imageMimeField = 'file_mime';
    public $imageDir = '/uploads';

    public function getImage($name, $size = '')
    {
        $absCroppedFilename = $this->getAbsImageDir() . $this->getFileName($name, $size);
        if (!file_exists($absCroppedFilename)) {
            $originalFilename = $this->getAbsImageDir() . $this->getFileName($name);
            $this->mkdir();
            if (!file_exists($originalFilename)) {
                file_put_contents($this->getAbsImageDir() . $this->getFileName($name), $this->owner->{$this->imageField});
            }
            $sizeArr = explode('x', $size);
            Image::thumbnail($originalFilename, $sizeArr[0], $sizeArr[1])->save($absCroppedFilename, ['quality' => 90]);
        }
        return $this->getImageDir($name, $size) . $this->getFileName($name, $size);
    }

    public function mkdir()
    {
        $folder = $this->getAbsImageDir();
        if (is_dir($folder) == false) {
            mkdir($folder, 0755, true);
        }
    }

    public function getImageDir()
    {
        return '/' . $this->imageDir . '/' . $this->owner->id . '/';
    }

    public function getAbsImageDir()
    {
        return param('absImgPath') . $this->getImageDir();
    }

    public function getFileName($name, $size = '')
    {
        return $name . '_' . $size . '.' . $this->owner->{$this->imageMimeField};
    }

    public function deleteAllFiles()
    {
        foreach (glob($this->getAbsImageDir().'*') as $file) {
            unlink($file);
        }
    }

    public function deleteFile($name, $size = '')
    {
        unlink($this->getAbsImageDir().$this->getFileName($name, $size));
    }
}