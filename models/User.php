<?php

namespace app\models;

use yii\db\ActiveRecord;

/**
 * Class User
 * @package app\models
 * @property int $id
 * @property int $client_uid
 * @property string $email
 * @property string $gender
 * @property string $fullname
 * @property string $country
 * @property string $region
 * @property string $city
 * @property string $address
 * @property int $partner_id
 * @property \DateTime $reg_date
 * @property int $status
 */
class User extends ActiveRecord
{
    public static function tableName()
    {
        return '{{users}}';
    }

    public function findByPartnerId(int $id)
    {
        return self::findAll(['partner_id' => $id]);
    }
}
