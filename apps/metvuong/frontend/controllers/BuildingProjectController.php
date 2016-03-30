<?php
namespace frontend\controllers;

use frontend\components\Controller;
//use vsoft\buildingProject\models\BuildingProject;
use frontend\models\Elastic;
use vsoft\ad\models\AdProduct;
use Yii;
use yii\data\Pagination;
use vsoft\ad\models\AdBuildingProject;
use yii\web\NotFoundHttpException;
use yii\helpers\Url;
use yii\web\Response;

class BuildingProjectController extends Controller
{
	public $layout = '@app/views/layouts/layout';

    /**
     * @return string
     */
    public function beforeAction($action)
    {
        $this->view->params['menuProject'] = true;
        return parent::beforeAction($action);
    }

	function actionIndex() {
        $models = AdBuildingProject::find()->where('`status` = ' . AdBuildingProject::STATUS_ENABLED)
            ->andWhere('`city_id` is not null')
            ->orderBy(['city_id'=> SORT_ASC, 'id' => SORT_DESC]);
        $count = $models->count();
        $pagination = new Pagination(['totalCount' => $count]);
        $pagination->defaultPageSize = 12;
        $models = $models->offset($pagination->offset)->limit($pagination->limit)->all();
        return $this->render('index', ['models' => $models, 'pagination' => $pagination]);

	}
	
	function actionView($slug) {
		$model = AdBuildingProject::find()->where('`slug` = :slug', [':slug' => $slug])->one();
		if($model) {
            if($model->is_crawl == 1)
                return $this->render('viewbds', ['model' => $model]);

            return $this->render('view', ['model' => $model]);
		} else {
			throw new NotFoundHttpException('The requested page does not exist.');
		}
	}
	
	function actionDetail($id) {
		\Yii::$app->response->format = \yii\web\Response::FORMAT_JSON;
		
		$model = AdBuildingProject::find()->where('`id` = :id', [':id' => $id])->asArray(true)->one();
	
		$model['url'] = Url::to(['building-project/view', 'slug' => $model['slug']]);
		
		return $model;
	}

    function actionFind() {
        $name = \Yii::$app->request->get("project_name");
        $models = AdBuildingProject::find()->where('`status` = ' . AdBuildingProject::STATUS_ENABLED)
            ->andWhere('`city_id` is not null')
            ->orderBy(['city_id'=> SORT_ASC, 'id' => SORT_DESC]);
        if(!empty($name)) {
            $models = $models->andWhere('name LIKE :query')->addParams([':query' => '%' . $name . '%']);
        }
        $count = $models->count();
        $pagination = new Pagination(['totalCount' => $count]);
        $pagination->defaultPageSize = 12;
        $models = $models->offset($pagination->offset)
            ->limit($pagination->limit)
            ->all();
        return $this->render('index', ['models' => $models, 'project_name' => $name, 'pagination' => $pagination]);
    }

    public function actionSearch() {
        $v = \Yii::$app->request->get('v');

        if(Yii::$app->request->isAjax) {
            Yii::$app->response->format = \yii\web\Response::FORMAT_JSON;

            $v = Elastic::transform($v);

            $params = [
                'query' => [
                    'match_phrase_prefix' => [
                        'search_field' => [
                            'query' => $v,
                            'max_expansions' => 100
                        ]
                    ],
                ],
                'sort' => [
                    'total_sell' => [
                        'order' => 'desc',
                        'mode'	=> 'sum'
                    ],
                    'total_rent' => [
                        'order' => 'desc',
                        'mode'	=> 'sum'
                    ],
                ],
                'size' => 6,
                '_source' => ['full_name']
            ];

            $ch = curl_init(Yii::$app->params['elastic']['config']['hosts'][0] . '/term/project_building/_search');
            curl_setopt($ch, CURLOPT_CUSTOMREQUEST, "GET");
            curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($params));
            curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);

            $result = json_decode(curl_exec($ch), true);
            $response = [];

            foreach ($result['hits']['hits'] as $k => $hit) {
                $response[$k] = $hit['_source'];
            }

            return $response;
        } else {
            $models = AdBuildingProject::find()->where('`status` = ' . AdBuildingProject::STATUS_ENABLED)
                ->andWhere('`city_id` is not null')
                ->orderBy(['city_id'=> SORT_ASC, 'id' => SORT_DESC]);
            if(!empty($v)) {
                $models = $models->andWhere('name LIKE :query')->addParams([':query' => '%' . $v . '%']);
            }
            $count = $models->count();
            $pagination = new Pagination(['totalCount' => $count]);
            $pagination->defaultPageSize = 12;
            $models = $models->offset($pagination->offset)
                ->limit($pagination->limit)
                ->all();
            return $this->render('index', ['models' => $models, 'project_name' => $v, 'pagination' => $pagination]);
        }
    }

}