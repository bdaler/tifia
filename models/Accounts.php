<?php
/**
 * author: BDaler (dalerkbtut@gmail.com)
 * @date: 25.09.2020 21:35
 */

namespace app\models;


use yii\db\ActiveRecord;

/**
 * Class Accounts
 * @package app\models
 *
 * @property $id
 * @property $client_uid
 * @property $login
 */
class Accounts extends ActiveRecord
{

    public static function tableName()
    {
        return '{{accounts}}';
    }
}