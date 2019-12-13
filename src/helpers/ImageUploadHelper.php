<?php

namespace concepture\yii2handbook\helpers;

use Imagine\Image\ManipulatorInterface;
use Yii;
use yii\base\Exception;
use yii\web\UploadedFile;
use yii\helpers\StringHelper;
use yii\helpers\FileHelper;
use yii\imagine\Image;

/**
 * Class ImageUploadHelper
 * @package concepture\yii2handbook\helpers
 * @author Olzhas Kulzhambekov <exgamer@live.ru>
 */
class ImageUploadHelper
{
    public static function imagePath($model, $field)
    {
        if (!file_exists(self::getImagesFolder($model) . $model->{$field})) {
            return null;
        }

        return self::getImagesPath($model) . $model->{$field};
    }

    public static function thumbPath($model, $field)
    {
        if (!file_exists(self::getThumbsFolder($model) . $model->{$field})) {
            return null;
        }

        return self::getThumbsPath($model) . $model->{$field};
    }

    public static function getImagesPath($model)
    {
        $folder = self::entityName($model);
        $pathPart = DIRECTORY_SEPARATOR;
        if ($model->id > 0){
            $pathPart .= $model->id. DIRECTORY_SEPARATOR;
        }

        return DIRECTORY_SEPARATOR . "uploads" . DIRECTORY_SEPARATOR . $folder . $pathPart ;
    }

    public static function getThumbsPath($model)
    {
        $directory = self::getImagesPath($model);
        $pathArray = explode('/', $directory);
        $file = array_pop($pathArray);
        $pathArray[] = 'thumb';
        $pathArray[] = $file;

        return implode("/", $pathArray);
    }

    public static function getImagesFolder($model)
    {

        return  Yii::getAlias('@public') . self::getImagesPath($model);
    }

    public static function getThumbsFolder($model)
    {
        $directory =  self::getImagesFolder($model);
        $pathArray = explode('/', $directory);
        $file = array_pop($pathArray);
        $pathArray[] = 'thumb';
        $pathArray[] = $file;

        return implode("/", $pathArray);
    }

    public static function loadFormImage($form, $model, $field, $oldFileName = null, $thumb = true)
    {
        $imageFile = UploadedFile::getInstance($form, $field);
        if (!$imageFile) {
            return [
                'name' => null,
                'size' => null,
                'url' => $oldFileName,
                'thumbnailUrl' => null
            ];
        }
        $directory = self::getImagesFolder($model);
        if (! is_dir($directory)) {
            FileHelper::createDirectory($directory);
        }
        $thumbDirectory = self::getThumbsFolder($model);
        if ($oldFileName) {
            $a = explode("/", $oldFileName );
            $oldFileName = array_pop($a);
            $oldFilePath = $directory . $oldFileName;
            if (file_exists($oldFilePath)) {
                FileHelper::unlink($oldFilePath);
            }
            $oldFileThumbPath = $thumbDirectory . $oldFileName;
            if (file_exists($oldFileThumbPath)) {
                FileHelper::unlink($oldFileThumbPath);
            }
        }
        $fileName = self::saveImage($imageFile, $directory);
        $model->{$field} = $fileName;
        if ($thumb){
            self::thumb($model, $field);
        }

        return [
            'name' => $fileName,
            'size' => $imageFile->size,
            'url' => self::imagePath($model, $field),
            'thumbnailUrl' => self::thumbPath($model, $field)
        ];
    }

    public static function saveImage($file, $directory)
    {
        $uid = uniqid(time(), true);
        $fileName = $uid . '.' . $file->extension;
        $filePath = $directory . $fileName;
        $file->saveAs($filePath);

        return $fileName;
    }

    public static function thumb($model, $field, $width = 300, $height = null, $quality = 80, $outbond = true)
    {
        $directory = self::getImagesFolder($model);
        if (! is_dir($directory)) {
            FileHelper::createDirectory($directory);
        }
        $thumbDirectory = self::getThumbsFolder($model);
        if (! is_dir($thumbDirectory)) {
            FileHelper::createDirectory($thumbDirectory);
        }
        $thumbPath = $thumbDirectory . $model->{$field};
        Image::thumbnail($directory . $model->{$field}, $width, $height, $outbond ? ManipulatorInterface::THUMBNAIL_OUTBOUND : ManipulatorInterface::THUMBNAIL_INSET)
            ->save($thumbPath, ['quality' => $quality]);
    }

    public static function entityName($model)
    {
        $entityName = StringHelper::basename(get_class($model));

        return   strtolower($entityName);

    }

    public static function remove($model, $field)
    {
        $filePath = Yii::getAlias('@public') . $model->{$field};
        if (file_exists($filePath)) {
            FileHelper::unlink($filePath);
        }
        $pathArray = explode('/', $filePath);
        $file = array_pop($pathArray);
        $pathArray[] = 'thumb';
        $pathArray[] = $file;
        $fileThumbPath = implode("/", $pathArray);
        if (file_exists($fileThumbPath)) {
            FileHelper::unlink($fileThumbPath);
        }
    }
}
