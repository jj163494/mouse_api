<?php

/**
 * This is the model class for table "td_user_device".
 *
 * The followings are the available columns in table 'td_user_device':
 * @property integer $id
 * @property integer $userId
 * @property string $device_code
 * @property integer $device_type
 * @property integer $created_time
 * @property string $device_nickname
 */
class TdUserDevice extends CActiveRecord
{
	/**
	 * @return string the associated database table name
	 */
	public function tableName(){
		return 'td_user_device';
	}

	/**
	 * @return array validation rules for model attributes.
	 */
	public function rules(){
		// NOTE: you should only define rules for those attributes that
		// will receive user inputs.
		return array(
			array('userId, device_type, created_time', 'numerical', 'integerOnly'=>true),
			array('device_code', 'length', 'max'=>64),
			// The following rule is used by search().
			// @todo Please remove those attributes that should not be searched.
			array('id, userId, device_code, device_type, created_time,device_nickname', 'safe', 'on'=>'search'),
		);
	}

	/**
	 * @return array relational rules.
	 */
	public function relations(){
		// NOTE: you may need to adjust the relation name and the related
		// class name for the relations automatically generated below.
		return array(
		);
	}

	/**
	 * @return array customized attribute labels (name=>label)
	 */
	public function attributeLabels(){
		return array(
			'id' => 'ID',
			'userId' => 'User',
			'device_code' => 'Device Code',
			'device_type' => 'Device Type',
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
	public function search(){
		// @todo Please modify the following code to remove attributes that should not be searched.

		$criteria=new CDbCriteria;

		$criteria->compare('id',$this->id);
		$criteria->compare('userId',$this->userId);
		$criteria->compare('device_code',$this->device_code,true);
		$criteria->compare('device_type',$this->device_type);
		$criteria->compare('created_time',$this->created_time);

		return new CActiveDataProvider($this, array(
			'criteria'=>$criteria,
		));
	}

	/**
	 * Returns the static model of the specified AR class.
	 * Please note that you should have this exact method in all your CActiveRecord descendants!
	 * @param string $className active record class name.
	 * @return TdUserDevice the static model class
	 */
	public static function model($className=__CLASS__){
		return parent::model($className);
	}


}
