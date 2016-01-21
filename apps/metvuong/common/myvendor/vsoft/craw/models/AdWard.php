<?php

namespace vsoft\craw\models;

use Yii;

/**
 * This is the model class for table "ad_ward".
 *
 * @property integer $id
 * @property integer $district_id
 * @property string $name
 * @property string $geometry
 * @property string $center
 * @property string $color
 * @property string $pre
 * @property integer $order
 * @property integer $status
 *
 * @property AdProduct[] $adProducts
 * @property AdDistrict $district
 */
class AdWard extends \yii\db\ActiveRecord
{
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'ad_ward';
    }

    /**
     * @return \yii\db\Connection the database connection used by this AR class.
     */
    public static function getDb()
    {
        return Yii::$app->get('dbCraw');
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['district_id', 'name'], 'required'],
            [['district_id', 'order', 'status'], 'integer'],
            [['geometry'], 'string'],
            [['name', 'center', 'color', 'pre'], 'string', 'max' => 32]
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'district_id' => 'District ID',
            'name' => 'Name',
            'geometry' => 'Geometry',
            'center' => 'Center',
            'color' => 'Color',
            'pre' => 'Pre',
            'order' => 'Order',
            'status' => 'Status',
        ];
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getAdProducts()
    {
        return $this->hasMany(AdProduct::className(), ['ward_id' => 'id']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getDistrict()
    {
        return $this->hasOne(AdDistrict::className(), ['id' => 'district_id']);
    }
}
