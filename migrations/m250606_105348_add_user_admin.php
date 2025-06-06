<?php

use app\models\User;
use yii\db\Migration;

class m250606_105348_add_user_admin extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp(): void
    {
        $user = new User();
        $user->username = 'admin';
        $user->setPassword('admin123');
        if (!$user->save()) {
            throw new \Exception('Failed to create test user: ' . json_encode($user->errors));
        }
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown(): void
    {
        $user = User::findOne(['username' => 'admin']);
        if ($user) {
            $user->delete();
        }
    }
}
