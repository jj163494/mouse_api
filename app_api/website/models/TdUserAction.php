<?php

/**
 * This is the model class for table "td_user_action".
 *
 * The followings are the available columns in table 'td_user_action':
 * @property string $id
 * @property integer $userid
 * @property integer $action_type
 * @property integer $soft_type
 * @property integer $device_type
 * @property string $networkType
 * @property string $action_detail
 * @property string $action_time
 * @property string $mac_address
 * @property integer $created_time
 */
class TdUserAction extends CActiveRecord
{
	/**
	 * @return string the associated database table name
	 */
	public function tableName()
	{
		return 'td_user_action';
	}

	/**
	 * @return array validation rules for model attributes.
	 */
	public function rules()
	{
		// NOTE: you should only define rules for those attributes that
		// will receive user inputs.
		return array(
			array('userid, created_time', 'required'),
			array('userid, action_type, soft_type, device_type, created_time', 'numerical', 'integerOnly'=>true),
			array('networkType', 'length', 'max'=>16),
			array('action_time, mac_address', 'length', 'max'=>32),
			array('action_detail', 'length', 'max'=>255),
			// The following rule is used by search().
			// @todo Please remove those attributes that should not be searched.
			array('id, userid, action_type, soft_type, device_type, networkType, action_detail, action_time, mac_address, created_time', 'safe', 'on'=>'search'),
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
			'action_type' => 'Action Type',
			'soft_type' => 'Soft Type',
			'device_type' => 'Device Type',
			'networkType' => 'Network Type',
			'action_detail' => '操作内容',
			'action_time' => '操作日期',
			'mac_address' => 'MAC地址',
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
		$criteria->compare('action_type',$this->action_type);
		$criteria->compare('soft_type',$this->soft_type);
		$criteria->compare('device_type',$this->device_type);
		$criteria->compare('networkType',$this->networkType,true);
		$criteria->compare('action_detail',$this->action_detail);
		$criteria->compare('action_time',$this->action_time);
		$criteria->compare('mac_address',$this->mac_address);
		$criteria->compare('created_time',$this->created_time);

		return new CActiveDataProvider($this, array(
			'criteria'=>$criteria,
		));
	}

	/**
	 * Returns the static model of the specified AR class.
	 * Please note that you should have this exact method in all your CActiveRecord descendants!
	 * @param string $className active record class name.
	 * @return TdUserAction the static model class
	 */
	public static function model($className=__CLASS__)
	{
		return parent::model($className);
	}
}
