<?php

declare(strict_types=1);

namespace app\modules\api\controllers;

use app\models\search\TrackSearch;
use app\models\Track;
use yii\data\ActiveDataProvider;
use yii\filters\AccessControl;
use yii\filters\auth\HttpBearerAuth;
use yii\filters\ContentNegotiator;
use yii\filters\VerbFilter;
use yii\rest\Controller;
use yii\web\NotFoundHttpException;
use yii\web\Response;

class TrackController extends Controller
{
    public $modelClass = 'app\\models\\Track';

    public function behaviors(): array
    {
        $behaviors = parent::behaviors();
        $behaviors['authenticator'] = [
            'class' => HttpBearerAuth::class,
            'except' => ['index', 'view'],
        ];
        $behaviors['contentNegotiator'] = [
            'class' => ContentNegotiator::class,
            'formats' => [
                'application/json' => Response::FORMAT_JSON,
            ],
        ];
        $behaviors['verbs'] = [
            'class' => VerbFilter::class,
            'actions' => [
                'index'     => ['GET'],
                'view'      => ['GET'],
                'create'    => ['POST'],
                'update'    => ['PUT', 'PATCH'],
                'delete'    => ['DELETE'],
            ],
        ];
        $behaviors['access'] = [
            'class' => AccessControl::class,
            'rules' => [
                [
                    'actions' => ['index', 'view'],
                    'allow' => true,
                    'roles' => ['?'],
                ],
                [
                    'actions' => ['create', 'update', 'delete'],
                    'allow' => true,
                    'roles' => ['@'],
                ],
            ],
        ];

        return $behaviors;
    }

    public function actionIndex(): ActiveDataProvider
    {
        $searchModel = new TrackSearch();
        return $searchModel->search(\Yii::$app->request->queryParams);
    }

    public function actionView(int $id): Track
    {
        return $this->findModel($id);
    }

    public function actionCreate(): array
    {
        $model = new Track();
        $model->load(\Yii::$app->request->post(), '');
        if ($model->save()) {
            \Yii::$app->response->setStatusCode(201);

            return $model->attributes;
        }

        return ['errors' => $model->errors];
    }

    public function actionUpdate(int $id): array
    {
        $model = $this->findModel($id);

        $model->load(\Yii::$app->request->getBodyParams(), '');
        if ($model->save()) {
            return $model->attributes;
        }

        return ['errors' => $model->errors];
    }

    public function actionDelete(int $id): void
    {
        $this->findModel($id)->delete();
        \Yii::$app->response->setStatusCode(204);
    }

    /**
     * @throws NotFoundHttpException
     */
    protected function findModel(int $id): Track
    {
        $model = Track::findOne($id);
        if ($model !== null) {
            return $model;
        }

        throw new NotFoundHttpException('Track not found.');
    }
}