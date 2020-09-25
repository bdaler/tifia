<?php
/**
 * author: BDaler (dalerkbtut@gmail.com)
 * @date: 25.09.2020 21:45
 */

namespace app\commands;


use app\models\User;
use yii\console\Controller;
use yii\console\ExitCode;

/**
 * Class ReferralsController
 * @package app\commands
 */
class ReferralsController extends Controller
{
    /**
     * @var int
     */
    public $clientUid;

    /**
     * @var bool
     */
    public $directRefs;

    /**
     * @param  string  $actionID
     * @return array|string[]
     */
    public function options($actionID)
    {
        return array_merge(parent::options($actionID),['clientUid', 'directRefs']);
    }

    /**
     * посчитать количество прямых рефералов и количество всех рефералов клиента
     * @return int
     */
    public function actionIndex(): int
    {
        $count = 0;
        $users = User::findAll(['client_uid' => $this->clientUid]);
        foreach ($users as $user) {
            $count+= $this->countRefs($user->client_uid, $count, $this->directRefs);
        }
        $this->stdout("ClientUid: $this->clientUid RefsCount: {$count} DirectRefs: {$this->directRefs}");
        return ExitCode::OK;
    }

    /**
     * @param   int  $clientUid
     * @param  int  $count
     * @param  bool  $directRefs
     * @return int
     */
    private function countRefs(int $clientUid, int &$count, bool $directRefs): int
    {
        $users = User::findAll(['partner_id' => $clientUid]);
        foreach ($users as $user) {
            if ($directRefs) {
                $count++;
                $this->countRefs($user->client_uid, $count, $directRefs);
            } elseif ($user->partner_id !== 0) {
                $count++;
                $this->countRefs($user->client_uid, $count, $directRefs);
            }
        }
        return $count;
    }

    /**
     * посчитать количество уровней реферальной сетки
     * @return int
     */
    public function actionRefLevels(): int
    {
        $users = User::findAll(['client_uid' => $this->clientUid]);
        foreach ($users as $user) {
            $level = $this->levelRef($user->client_uid);
            $this->stdout("ClientUid: $this->clientUid RefsLevel: {$level}");
        }
        return ExitCode::OK;
    }

    /**
     * @param  int  $clientUid
     * @return int
     */
    private function levelRef(int $clientUid): int
    {
        $level = 0;
        $users = User::findAll(['partner_id' => $clientUid]);
        foreach ($users as $user) {
            if ($user->partner_id !== 0) {
                $level++;
                $this->levelRef($user->client_uid);
            }
        }
        return $level;
    }
}