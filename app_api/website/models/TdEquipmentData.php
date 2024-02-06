<?php


/**
 * This is the model class for table "td_equipment_data".
 *
 * The followings are the available columns in table 'td_equipment_data':
 * @property integer $id
 * @property integer $userid
 * @property string $device_type
 * @property string device_model$
 * @property string $device_name
 * @property string $device_code
 * @property string $key_knock_detail
 * @property integer $click_num_left
 * @property integer $click_num_right
 * @property integer $move_distance
 * @property integer $old_click_num_left
 * @property integer $old_click_num_right
 * @property integer $old_move_distance
 * @property integer $update_time
 */
class TdEquipmentData extends CActiveRecord
{
	/**
	 * @return string the associated database table name
	 */
	public function tableName()
	{
		return 'td_equipment_data';
	}

	/**
	 * @return array validation rules for model attributes.
	 */
	public function rules()
	{
		// NOTE: you should only define rules for those attributes that
		// will receive user inputs.
		return array(
			array('userid, device_type, device_model, device_name, device_code, key_knock_detail, click_num_left, click_num_right, move_distance, old_click_num_left, old_click_num_right, old_move_distance, update_time', 'required'),
			array('userid, device_type, click_num_left, click_num_right, move_distance, old_click_num_left, old_click_num_right, old_move_distance, update_time', 'numerical', 'integerOnly'=>true),
			array('device_model', 'length', 'max'=>10),
			array('device_code', 'length', 'max'=>32),
			array('key_knock_detail', 'length', 'max'=>10000),
			// The following rule is used by search().
			// @todo Please remove those attributes that should not be searched.
			array('id, userid, device_type, device_model, device_name, key_knock_detail, click_num_left, click_num_right, move_distance, old_click_num_left, old_click_num_right, old_move_distance, update_time', 'safe', 'on'=>'search'),
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
			'device_type' => 'Device Class',
			'device_model' => 'Device Type',
			'device_name' => 'Device Name',
			'device_code' => 'Device',
			'key_knock_detail' => 'Keycap Knock',
			'click_num_left' => 'Mouse Knock Left',
			'click_num_right' => 'Mouse Knock Right',
			'move_distance' => 'Move Meter',
			'old_click_num_left' => 'Old Mouse Knock Left',
			'old_click_num_right' => 'Old Mouse Knock Right',
			'old_move_distance' => 'Old Move Meter',
			'update_time' => 'Update Time',
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
		$criteria->compare('userid',$this->userid);
		$criteria->compare('device_type',$this->device_type,true);
		$criteria->compare('device_model',$this->device_model,true);
		$criteria->compare('device_name',$this->device_name,true);
		$criteria->compare('device_code',$this->device_code,true);
		$criteria->compare('key_knock_detail',$this->key_knock_detail,true);
		$criteria->compare('click_num_left',$this->click_num_left);
		$criteria->compare('click_num_right',$this->click_num_right);
		$criteria->compare('move_distance',$this->move_distance);
		$criteria->compare('old_click_num_left',$this->old_click_num_left);
		$criteria->compare('old_click_num_right',$this->old_click_num_right);
		$criteria->compare('old_move_distance',$this->old_move_distance);
		$criteria->compare('update_time',$this->update_time);

		return new CActiveDataProvider($this, array(
			'criteria'=>$criteria,
		));
	}

	/**
	 * Returns the static model of the specified AR class.
	 * Please note that you should have this exact method in all your CActiveRecord descendants!
	 * @param string $className active record class name.
	 * @return TdEquipmentData the static model class
	 */
	public static function model($className=__CLASS__)
	{
		return parent::model($className);
	}
}
