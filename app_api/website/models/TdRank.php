<?php

/**
 * This is the model class for table "td_rank".
 *
 * The followings are the available columns in table 'td_rank':
 * @property integer $id
 * @property integer $userId
 * @property integer $order_type
 * @property integer $game_type
 * @property integer $created_time
 * @property integer $target_num
 * @property integer $rank
 */
class TdRank extends CActiveRecord
{
	/**
	 * @return string the associated database table name
	 */
	public function tableName()
	{
		return 'td_rank';
	}

	/**
	 * @return array validation rules for model attributes.
	 */
	public function rules()
	{
		// NOTE: you should only define rules for those attributes that
		// will receive user inputs.
		return array(
			array('userId, order_type, game_type, created_time, target_num, rank', 'numerical', 'integerOnly'=>true),
			// The following rule is used by search().
			// @todo Please remove those attributes that should not be searched.
			array('id, userId, order_type, game_type, created_time, target_num, rank', 'safe', 'on'=>'search'),
		);
	}

	/**
	 * @return array relational rules.
	 */
	public function relations()
	{
		// NOTE: you may need to adjust the relation name and the related
		// class name for the relations automatically generated below.
		return array(
		);
	}

	/**
	 * @return array customized attribute labels (name=>label)
	 */
	public function attributeLabels()
	{
		return array(
			'id' => 'ID',
			'userId' => 'User',
			'order_type' => 'Order Type',
			'game_type' => 'Game Type',
			'created_time' => 'Created Time',
			'target_num' => 'Target Num',
			'rank' => 'Rank',
		);
	}

	/**
	 * Retrieves a list of models based on the current search/filter conditions.
	 *
	 * Typical usecase:
	 * - Initialize the model fields with values from filter form.
	 * - Execute this method to get CActiveDataProvider instance which will filter
	 * models according to data in model fields.
	 * - Pass data provider to CGridView, CListView or any similar widget.
	 *
	 * @return CActiveDataProvider the data provider that can return the models
	 * based on the search/filter conditions.
	 */
	public function search()
	{
		// @todo Please modify the following code to remove attributes that should not be searched.

		$criteria=new CDbCriteria;

		$criteria->compare('id',$this->id);
		$criteria->compare('userId',$this->userId);
		$criteria->compare('order_type',$this->order_type);
		$criteria->compare('game_type',$this->game_type);
		$criteria->compare('created_time',$this->created_time);
		$criteria->compare('target_num',$this->target_num);
		$criteria->compare('rank',$this->rank);

		return new CActiveDataProvider($this, array(
			'criteria'=>$criteria,
		));
	}

	/**
	 * Returns the static model of the specified AR class.
	 * Please note that you should have this exact method in all your CActiveRecord descendants!
	 * @param string $className active record class name.
	 * @return TdRank the static model class
	 */
	public static function model($className=__CLASS__)
	{
		return parent::model($className);
	}
}
