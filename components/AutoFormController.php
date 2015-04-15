<?php
namespace app\components;
trait AutoFormController {
    public function actionIndex()
    {
        $cn = $this -> searchClass;
        $filterModel = new $cn();
        $dataProvider = $filterModel->search(\Yii::$app->request->get());
        $columns = $this -> gridColumns;
        $columns [] = [
            'class' => 'yii\grid\ActionColumn',
            'template' => '{update} {delete}',
        ];
        return $this->render('@app/views/autoform/index', [
            'indexTitle' => $this -> indexTitle,
            'filterModel' => $filterModel,
            'dataProvider' => $dataProvider,
            'columns' => $columns,
            'createTitle' => $this->createTitle,
        ]);
    }

    public function getModel ($id) {
        $model = call_user_func([$this -> modelClass, 'findOne'], [$id]);
        if ($model !== null) {
            return $model;
        } else {
            throw new NotFoundHttpException('Запрашиваемая страница не найдена');
        }
    }

    public function getFormFields ($form, $model) {
        $this -> initFormFields($form, $model);
        $data = '';
        foreach ($this->formFields as $fld) {
            if ($fld ['type'] == 'field') {
                $fo = $form->field($model, $fld ['fieldName']);
                $params = isset ($fld ['params']) ? $fld ['params'] : [];
                $data .= call_user_func_array ([$fo, $fld['fieldType']], $params);
            } else if ($fld ['type'] == 'render') {
                $data .= $this -> renderPartial ($fld ['path'], $fld['params']);
            }
        }
        return $data;
    }

    public function initFormFields ($form, $model) {}
    public function saveDependences ($model) {}
    public function deleteDependences ($model) {}

    public function actionCreate()
    {
        $cn = $this -> modelClass;
        $model = new $cn();

        if ($model->load(\Yii::$app->request->post()) && $model->save()) {
            $this -> saveDependences($model);
            return $this->redirect(['update', 'id' => $model->getPrimaryKey()]);
        }
        return $this->render('@app/views/autoform/create', ['model' => $model, 'indexTitle' => $this->indexTitle, 'createTitle' => $this->createTitle, 'formController' => $this]);
    }

    public function actionUpdate($id)
    {
        $model = $this->getModel($id);

        if ($model->load(\Yii::$app->request->post()) && $model->save()) {
            $this -> saveDependences($model);
            return $this->redirect(['update', 'id' => $model->getPrimaryKey()]);
        }
        return $this->render('@app/views/autoform/update', ['model' => $model, 'indexTitle' => $this->indexTitle, 'updateTitle' => $this->updateTitle, 'formController' => $this, 'titleField' => $this->titleField]);
    }

    public function actionDelete($id)
    {
        $model = $this->getModel($id);
        $this->deleteDependences ($model);
        $model->delete();
        return $this->redirect(['index']);
    }
}