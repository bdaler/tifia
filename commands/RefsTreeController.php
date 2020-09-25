<?php
/**
 * author: BDaler (dalerkbtut@gmail.com)
 * @date: 25.09.2020 21:30
 */

namespace app\commands;


use app\models\User;
use yii\console\Controller;
use yii\console\ExitCode;

class RefsTreeController extends Controller
{
    /**
     * @var int
     */
    public $clientUid;

    /**
     * @param  string  $actionID
     * @return array|string[]
     */
    public function options($actionID)
    {
        return array_merge(parent::options($actionID),['clientUid']);
    }

    /**
     * Построить дерево рефералов на основе поля partner_id таблицы Users
     * @return int
     */
    public function actionIndex(): int
    {

//        $users = User::find()->all();
        $users = User::findAll(['client_uid' => $this->clientUid]);
        foreach ($users as $user) {
            $this->stdout("  |--{$user->client_uid}\n");
            $this->printRrfs($user->client_uid, 2);
        }

        return ExitCode::OK;
    }

    /**
     * @param $clientUid
     * @param  int  $level
     */
    private function printRrfs($clientUid, int $level): void
    {
        $users = User::findAll(['partner_id' => $clientUid]);
        foreach ($users as $user) {
            if ($user->partner_id !== 0) {
                $pipe = str_repeat('  |', $level);
                $this->stdout("{$pipe}--{$user->client_uid}\n");
                $this->printRrfs($user->client_uid, $level + 1);
            }
        }
    }
}