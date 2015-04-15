<?php
namespace app\modules\admin\controllers;

use app\components\AutoFormController;
use app\models\Implementer;
use app\models\ImplementerDocument;
use app\modules\admin\components\AdminController;
use kartik\select2\Select2;
use kartik\switchinput\SwitchInput;
use yii\helpers\Json;
use yii\helpers\Url;
use yii\web\JsExpression;

class ImplementerController extends AdminController
{
    use AutoFormController;
    protected $indexTitle = 'Исполнители';
    protected $createTitle = 'Добавление';
    protected $updateTitle = 'Редактирование';
    protected $searchClass = 'app\models\search\ImplementerSearch';
    protected $modelClass  = 'app\models\Implementer';
    protected $gridColumns = null;
    protected $titleField = 'name';
    protected $formFields = null;

    public function init () {
        parent::init ();
        $this->gridColumns = [
            'id',
            [
                'attribute' => 'status_id',
                'format' => 'raw',
                'filter' => Implementer::getStatusOptions(),
                'value' => function ($data) {
                    return Implementer::getStatusOptions()[$data->status_id];
                }
            ], [
                'attribute' => 'type_id',
                'filter' => Implementer::getTypeOptions(),
                'value' => function ($data) {
                    return Implementer::getTypeOptions()[$data->type_id];
                }
            ],
            'name',
        ];
    }

    public function initFormFields ($form, $model) {
        $userUrlList = Url::toRoute(['json-list']);
        $userSelect2init = <<< SCRIPT
function (element, callback) {
    var id=\$(element).val();
    if (id !== "") {
        \$.ajax("{$userUrlList}?id=" + id, {
            dataType: "json"
        }).done(function(data) { callback(data.results);});
    }
}
SCRIPT;

        $this->formFields = [
            ['type' => 'field', 'fieldName' => 'status_id', 'fieldType' => 'widget', 'params' => [SwitchInput::classname()]],
            ['type' => 'field', 'fieldName' => 'user_id', 'fieldType' => 'widget',    'params' => [Select2::classname(), [
                'options' => [
                    'placeholder' => 'пользователь...',
                    'multiple' => false,
                ],
                'pluginOptions' => [
                    'allowClear' => true,
                    'minimumInputLength' => 3,
                    'ajax' => [
                        'url' => $userUrlList,
                        'dataType' => 'json',
                        'data' => new JsExpression('function(term,page) { return {search:term}; }'),
                        'results' => new JsExpression('function(data,page) { return {results:data.results}; }'),
                    ],
                    'initSelection' => new JsExpression($userSelect2init)
                ],
            ]]],
            ['type' => 'field', 'fieldName' => 'type_id', 'fieldType' => 'dropDownList', 'params' => [Implementer::getTypeOptions()]],
            ['type' => 'field', 'fieldName' => 'name',     'fieldType' => 'textInput',    'params' => [['maxlength' => 255]]],
            ['type' => 'field', 'fieldName' => 'info',     'fieldType' => 'textarea',    'params' => [['cols' => 5]]],
            ['type' => 'field', 'fieldName' => 'passport1', 'fieldType' => 'fileInput'],
            ['type' => 'render', 'path' => '@app/modules/admin/views/implementer/downloadDoc',  'params' => ['model' => $model, 'type' => ImplementerDocument::TYPE_PASSPORT_MAIN]],
            ['type' => 'field', 'fieldName' => 'passport2', 'fieldType' => 'fileInput'],
            ['type' => 'render', 'path' => '@app/modules/admin/views/implementer/downloadDoc',  'params' => ['model' => $model, 'type' => ImplementerDocument::TYPE_PASSPORT_ADDRESS]],
            ['type' => 'field', 'fieldName' => 'agreement_file', 'fieldType' => 'fileInput'],
            ['type' => 'render', 'path' => '@app/modules/admin/views/implementer/downloadDoc',  'params' => ['model' => $model, 'type' => ImplementerDocument::TYPE_AGREEMENT]],
            ['type' => 'field', 'fieldName' => 'has_agreement', 'fieldType' => 'checkbox'],
        ];

        $js = <<< JS
            $('#implementer-type_id').change(function() {
                if ($(this).val() == 2) {
                    $('.field-implementer-passport1, .field-implementer-passport2').hide();
                } else {
                    $('.field-implementer-passport1, .field-implementer-passport2').show()
                }
            });

            if ($('#implementer-type_id').val() == 2) {
                $('.field-implementer-passport1, .field-implementer-passport2').hide();
            }

            $('.delDocument').click(function() {
                if (confirm('Точно удалить?')) {
                    return true;
                }
                return false;
            });
JS;
        $this->getView()->registerJs($js);
    }

    public function actionGetDocument($id)
    {
        $doc = $this->loadModel('app\models\ImplementerDocument', $id);

        header('Content-Type: ' . $doc->mime_type);
        echo $doc->content;
        \Yii::$app->end();
    }

    public function actionDelDocument($id)
    {
        $doc = $this->loadModel('app\models\ImplementerDocument', $id);
        $impId = $doc->implementer_id;
        $doc->delete();
        $this->redirect(['update', 'id' => $impId]);
    }

    public function actionJsonList ($search = null, $id = null) {
        $out = ['more' => false];
        if (!is_null($search)) {
            $data = db()->createCommand("
                SELECT id, CONCAT(username, ' (', email, ')' ) AS text FROM user
                 WHERE username LIKE ".\Yii::$app->db->quoteValue('%' . $search . '%')."
                 OR email LIKE ".\Yii::$app->db->quoteValue('%' . $search . '%')."
                 LIMIT 10
            ")->queryAll();
            $out['results'] = $data;
        } elseif ($id > 0) {
            $idArr = explode(',', $id);
            $data = db()->createCommand("
                SELECT id, CONCAT(username, ' (', email, ')' ) AS text FROM user
                 WHERE id in(" . join (',', array_map([\Yii::$app->db, 'quoteValue'], $idArr)) . ")
            ")->queryAll();
            if (count($data) == 1) $out ['results'] = $data [0];
            else $out ['results'] = $data;
        } else {
            $out['results'] = ['id' => 0, 'text' => 'Ничего не найдено'];
        }
        echo Json::encode($out);
    }
}