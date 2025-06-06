<?php

namespace app\modules\api\controllers;

use yii\rest\Controller;
use yii\filters\ContentNegotiator;
use yii\web\Response;
use app\models\User;
use yii\web\BadRequestHttpException;

class AuthController extends Controller
{
    public function behaviors(): array
    {
        $behaviors = parent::behaviors();
        $behaviors['contentNegotiator'] = [
            'class' => ContentNegotiator::class,
            'formats' => [
                'application/json' => Response::FORMAT_JSON,
            ],
        ];
        return $behaviors;
    }

    /**
     * Авторизация и выдача JWT.
     *
     * @throws BadRequestHttpException
     */
    public function actionLogin()
    {
        $params = \Yii::$app->request->post();
        if (empty($params['username']) || empty($params['password'])) {
            throw new BadRequestHttpException('Username and password are required.');
        }

        $user = User::findOne(['username' => $params['username']]);
        if ($user && $user->validatePassword($params['password'])) {
            return [
                'token' => $user->generateJwtToken(),
                'user_id' => $user->id,
                'username' => $user->username,
            ];
        }

        throw new BadRequestHttpException('Invalid username or password.');
    }
}