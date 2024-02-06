<?php

/**
 * This is the model class for table "td_log".
 *
 * The followings are the available columns in table 'td_log':
 * @property string $id
 * @property integer $userid
 * @property integer $soft_type
 * @property integer $device_type
 * @property string $soft_version
 * @property string $mobile_model
 * @property string $os_version
 * @property string $log_type
 * @property string $section
 * @property string $message
 * @property integer $created_time
 */
class TdLog extends CActiveRecord
{
	/**
	 * @return string the associated database table name
	 */
	public function tableName()
	{
		return 'td_log';
	}

	/**
	 * @return array validation rules for model attributes.
	 */
	public function rules()
	{
		// NOTE: you should only define rules for those attributes that
		// will receive user inputs.
		return array(
			array('created_time', 'required'),
			array('userid, soft_type, device_type, created_time', 'numerical', 'integerOnly'=>true),
			array('soft_version, mobile_model, os_version', 'length', 'max'=>24),
			array('log_type', 'length', 'max'=>16),
			array('section', 'length', 'max'=>32),
			// The following rule is used by search().
			// @todo Please remove those attributes that should not be searched.
			array('id, userid, soft_type, device_type, soft_version, mobile_model, os_version, log_type, section, message, created_time', 'safe', 'on'=>'search'),
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
			'soft_type' => 'Soft Type',
			'device_type' => 'Device Type',
			'soft_version' => 'Soft Version',
			'mobile_model' => 'Mobile Model',
			'os_version' => 'Os Version',
			'log_type' => 'Log Type',
			'section' => 'Section',
			'message' => 'Message',
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
		$criteria->compare('soft_type',$this->soft_type);
		$criteria->compare('device_type',$this->device_type);
		$criteria->compare('soft_version',$this->soft_version,true);
		$criteria->compare('mobile_model',$this->mobile_model,true);
		$criteria->compare('os_version',$this->os_version,true);
		$criteria->compare('log_type',$this->log_type,true);
		$criteria->compare('section',$this->section,true);
		$criteria->compare('message',$this->message,true);
		$criteria->compare('created_time',$this->created_time);

		return new CActiveDataProvider($this, array(
			'criteria'=>$criteria,
		));
	}

	/**
	 * Returns the static model of the specified AR class.
	 * Please note that you should have this exact method in all your CActiveRecord descendants!
	 * @param string $className active record class name.
	 * @return TdLog the static model class
	 */
	public static function model($className=__CLASS__)
	{
		return parent::model($className);
	}
}
