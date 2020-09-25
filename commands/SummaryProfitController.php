<?php
/**
 * @author Daler Bahritdinov <dbahritdinov@htc-cs.ru>
 * Date: 25.09.2020
 * Time: 21:45
 */

namespace app\commands;


use app\models\Accounts;
use app\models\Trades;
use app\models\User;
use Yii;
use yii\console\Controller;
use yii\db\Exception;

/**
 * Class SummaryProfitController
 * @package app\commands
 */
class SummaryProfitController extends Controller
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
     * посчитать суммарный объем volume * coeff_h * coeff_cr
     * по всем уровням реферральной системы за период времени
     */
    public function actionIndex(): void
    {
        $sum = 0;
        $refs = User::findAll(['partner_id' => $this->clientUid]);
        foreach ($refs as $ref) {
            $sum+= $this->refs($ref->client_uid, $sum);
        }
        $this->stdout("ClientUID: {$this->clientUid} Summary profit: {$sum} in period: {$this->startDate} => {$this->endDate}");
    }

    /**
     * @param $clientUid
     * @param $sum
     * @return false|string|null
     * @throws Exception
     */
    public function refs($clientUid, $sum)
    {
        $refs = User::findAll(['partner_id' => $clientUid]);
        foreach ($refs as $ref) {
            $accounts = Yii::$app->db->createCommand("SELECT login FROM accounts WHERE client_uid=:client_uid",
                [':client_uid' => $ref->client_uid])->queryColumn();

            foreach ($accounts as $login){
                $sql = Yii::$app->db->createCommand(
                    "select sum(prof) from (SELECT (t.volume * t.coeff_h * t.coeff_cr) as prof from trades t where login =:login and t.close_time >= :startDate and t.close_time <= :endDate) t1",
                );
                $sql->bindValues([':login' => $login, ':startDate' => $this->startDate, ':endDate' => $this->endDate]);
                $profitSum = $sql->queryColumn();
                $sum += $profitSum[0];
                $this->stdout("Let me done my job, please don't stop me :) login: {$login}, sum: {$sum} \n");
            }
            if ($ref->partner_id !== 0) {
                $this->refs($ref->client_uid, $sum);
            }
        }
        return $sum;
    }

}