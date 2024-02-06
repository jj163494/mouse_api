<?php

/**
 * This is the model class for table "td_heart".
 *
 * The followings are the available columns in table 'td_heart':
 * @property string $id
 * @property integer $userid
 * @property double $distance
 * @property integer $click_num
 * @property integer $left_click_num
 * @property integer $right_click_num
 * @property integer $knock_counters
 * @property integer $created_time
 */
class TdHeart extends CActiveRecord
{
	/**
	 * @return string the associated database table name
	 */
	public function tableName()
	{
		return 'td_heart';
	}

	/**
	 * @return array validation rules for model attributes.
	 */
	public function rules()
	{
		// NOTE: you should only define rules for those attributes that
		// will receive user inputs.
		return array(
			array('userid', 'required'),
			array('userid, click_num, left_click_num, right_click_num, knock_counters, created_time', 'numerical', 'integerOnly'=>true),
			array('distance', 'numerical'),
			// The following rule is used by search().
			// @todo Please remove those attributes that should not be searched.
			array('id, userid, distance, click_num, left_click_num, right_click_num, created_time', 'safe', 'on'=>'search'),
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
			'userid' => 'Userid',
			'distance' => 'Distance',
			'click_num' => 'Click Num',
			'left_click_num' => 'Left Click Num',
			'right_click_num' => 'Right Click Num',
			'knock_counters' => 'Knock Counters',
			'created_time' => 'Created Time',
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

		$criteria->compare('id',$this->id,true);
		$criteria->compare('userid',$this->userid);
		$criteria->compare('distance',$this->distance);
		$criteria->compare('click_num',$this->click_num);
		$criteria->compare('left_click_num',$this->left_click_num);
		$criteria->compare('right_click_num',$this->right_click_num);
		$criteria->compare('knock_counters',$this->knock_counters);
		$criteria->compare('created_time',$this->created_time);

		return new CActiveDataProvider($this, array(
			'criteria'=>$criteria,
		));
	}

	/**
	 * Returns the static model of the specified AR class.
	 * Please note that you should have this exact method in all your CActiveRecord descendants!
	 * @param string $className active record class name.
	 * @return TdHeart the static model class
	 */
	public static function model($className=__CLASS__)
	{
		return parent::model($className);
	}
}
