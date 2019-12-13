<?php
namespace concepture\yii2handbook\actions\web;

use concepture\yii2logic\actions\Action;

/**
 * Class ImageDeleteAction
 * @package concepture\yii2handbook\actions\web
 * @author Olzhas Kulzhambekov <exgamer@live.ru>
 */
class ImageDeleteAction extends Action
{
    public $redirect = 'index';

    public function run($id)
    {
        \Yii::$app->response->format = \yii\web\Response::FORMAT_JSON;
        return
            [
                'success' => [
                    [
                        'id' => 1,
                        'path' => 'dfsfds',
                        'url' => '/assets/d0307205/placeholders/placeholder.jpg',
                        'size' => 20,
                        'height' => 20,
                        'width' => 20,
                        'ratio' => 20,
                        'thumbs' => [
                            'sdfasd'
                        ]
                    ]
                ]
            ];

//        $service = $this->getService();
//        $image = $service->findById($id);
//        if (!$image){
//            throw new NotFoundHttpException();
//        }
//        ImageUploadHelper::remove($image, 'path');
//        $service->delete($image);
//
//        $request = Yii::$app->request;
//        if (! $request->isAjax) {
//            return $this->redirect([$this->redirect]);
//        }
////        $dirPath = $this->getFileFolder($path);
////        $files = FileHelper::findFiles($dirPath);
////        $getPath = $this->getFileFolder($image->path);
//        $output = [];
////        foreach ($files as $file) {
////            $fileName = basename($file);
////            $filePath = $getPath . DIRECTORY_SEPARATOR . $fileName;
////            $output['files'][] = [
////                'name' => $fileName,
////                'size' => filesize($file),
////                'url' => $filePath,
////                'thumbnailUrl' => $filePath,
////                'deleteUrl' => 'delete',
////                'deleteType' => 'POST',
////            ];
////        }
//
//        return Json::encode($output);
    }

    public function getFileFolder($path)
    {
        $pathArray = explode("/",$path);
        array_pop($pathArray);

        return implode("/", $pathArray);
    }
}