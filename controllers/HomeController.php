<?php

namespace controllers;

use models\AdSearch;

class HomeController extends Controller
{
    public function actionIndex()
    {
        $params = isset($_GET['AdSearch']) ? $_GET['AdSearch'] : [];

        $search = new AdSearch($params);

        $data = $search->search();

        if ($this->isAjaxRequest()) {
            foreach ($data as $i => $model) {
                $this->renderPartial('_ad', [
                    'model' => $model,
                    'i' => $i + 1
                ]);
            }
            return null;
        }

        return $this->render('index', [
            'data' => $data,
            'search' => $search
        ]);
    }
}