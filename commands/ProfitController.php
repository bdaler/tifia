<?php
/**
 * @author Daler Bahritdinov <dbahritdinov@htc-cs.ru>
 * Date: 25.09.2020
 * Time: 21:01
 */

namespace app\commands;


use app\models\Accounts;
use app\models\Trades;
use app\models\User;
use yii\console\Controller;

/**
 * Class ProfitController
 * @package app\commands
 */
class ProfitController extends Controller
{

    /**
     * @var int
     */
    public $clientUid;
    /**
     * @var string
     */
    public $startDate;
    /**
     * @var string
     */
    public $endDate;

    /**
     * @param  string  $actionID
     * @return array|string[]
     */
    public function options($actionID)
    {
        return array_merge(parent::options($actionID),['clientUid', 'startDate', 'endDate']);
    }

    /**
     * посчитать прибыльность (сумма profit) за определенный период времени
     */
    public function actionIndex()
    {
        $sum = 0;
        $users = User::findAll(['partner_id' => $this->clientUid]);
        foreach ($users as $user) {
            $sum+= $this->refs($user->client_uid, $sum);
        }
        print_r("ClientUID: {$this->clientUid} Profit: {$sum} in period: {$this->startDate} => {$this->endDate}");
    }

    /**
     * @param $clientUid
     * @param $sum
     * @return false|string|null
     */
    public function refs($clientUid, $sum)
    {
        $refs = User::findAll(['partner_id' => $clientUid]);
        foreach ($refs as $ref) {
            $accounts = Accounts::findAll(['client_uid' => $ref->client_uid]);
            foreach ($accounts as $account) {
                $profitSum = Trades::findBySql(
                    "SELECT SUM(profit) from trades t where t.login =:login and t.close_time >= :startDate and t.close_time <= :endDate",
                    [':login' => $account->login, ':startDate' => $this->startDate, ':endDate' => $this->endDate])
                    ->scalar();
                $sum += $profitSum;
                $this->stdout("I do my job, please don't stop me :) clientUid: {$account->client_uid}, accountLogin: {$account->login} sum: {$sum} \n");
            }
            if ($ref->partner_id !== 0) {
                $this->refs($ref->client_uid, $sum);
            }
        }
        return $sum;
    }
}