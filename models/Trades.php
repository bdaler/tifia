<?php
/**
 * author: BDaler (dalerkbtut@gmail.com)
 * @date: 25.09.2020 22:36
 */

namespace app\models;


use yii\db\ActiveRecord;

/**
 * Class Trades
 * @package app\models
 * @property int $id
 * @property int $ticket
 * @property int $login
 * @property string $symbol
 * @property int $cmd
 * @property float $volume
 * @property \DateTime $open_time
 * @property \DateTime $close_time
 * @property float $profit
 * @property float $coeff_h
 * @property float $coeff_cr
 */
class Trades extends ActiveRecord
{
    public static function tableName()
    {
        return '{{trades}}';
    }
}