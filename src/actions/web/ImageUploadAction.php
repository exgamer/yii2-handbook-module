<?php
namespace concepture\yii2handbook\actions\web;

use concepture\yii2logic\actions\Action;

/**
 * Class ImageUploadAction
 * @package concepture\yii2handbook\actions\web
 * @author Olzhas Kulzhambekov <exgamer@live.ru>
 */
class ImageUploadAction extends Action
{
    public function run()
    {
        $form = $this->getForm();

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


//        $modelClass = $this->getModelClass();
//        $model = new $modelClass();
//        if (property_exists($model, 'entity_id')) {
//            $model->entity_id = $entity_id;
//        }
//        $result = ImageUploadHelper::loadFormImage($form, $model, $this->attribute, null, true);
//        $model->{$this->attribute} = $result['url'];
//        $model->save(false);
//        $result['id'] = $model->id;
//        $result['deleteUrl'] = 'delete?id=' . $model->id;
//        $result['deleteType'] = 'POST';
//
//        return Json::encode([
//            'files' => [
//                $result
//            ],
//        ]);
    }
}