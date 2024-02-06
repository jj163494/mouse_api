<?php

/**
 * This is the model class for table "td_user_game_mode".
 *
 * The followings are the available columns in table 'td_user_game_mode':
 * @property integer $id
 * @property integer $userid
 * @property string $device_code
 * @property string $mode_name
 * @property integer $game_type
 * @property integer $game_type_name
 * @property integer $polling_rate
 * @property integer $repeat_speed
 * @property integer $no_rush_mode
 * @property integer $kill_keys
 * @property integer $lamp_type
 * @property string $lamp_light_name
 * @property integer $keycap_type
 * @property string $keycap_light_name
 * @property integer $resconstruct_type
 * @property string $resconstruct_project_name
 */
class TdUserGameMode extends CActiveRecord
{
	/**
	 * @return string the associated database table name
	 */
	public function tableName()
	{
		return 'td_user_game_mode';
	}

	/**
	 * @return array validation rules for model attributes.
	 */
	public function rules()
	{
		// NOTE: you should only define rules for those attributes that
		// will receive user inputs.
		return array(
			array('userid, device_code, mode_name, polling_rate, repeat_speed, no_rush_mode, lamp_light_type, keycap_light_type, resconstruct_project_type', 'required'),
			array('userid, polling_rate, no_rush_mode, lamp_light_type, keycap_light_type, recommended_game_id,resconstruct_project_type', 'numerical', 'integerOnly'=>true),
			array('device_code', 'length', 'max'=>65),
			array('mode_name', 'length', 'max'=>54),
			array('lamp_light_name, keycap_light_name, resconstruct_project_name', 'length', 'max'=>32),
			// The following rule is used by search().
			// @todo Please remove those attributes that should not be searched.
			array('id, userid, device_code, mode_name, polling_rate, repeat_speed, no_rush_mode, kill_keys, lamp_light_type, lamp_light_name, keycap_light_type, keycap_light_name, resconstruct_project_type, resconstruct_project_name,game_type_name', 'safe', 'on'=>'search'),
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
			'device_code' => 'Device Code',
			'mode_name' => 'Mode Name',	
			'game_type_name' => 'Game Type Name',
			'polling_rate' => 'Polling Rate',
			'repeat_speed' => 'Repeat Speed',
			'no_rush_mode' => 'No Rush Mode',
			'kill_keys' => 'Kill Keys',
			'lamp_light_type' => 'Lamp Light Type',
			'lamp_light_name' => 'Lamp Light Name',
			'keycap_light_type' => 'Keycap Light Type',
			'keycap_light_name' => 'Keycap Light Name',
			'resconstruct_project_type' => 'Resconstruct Project Type',
			'resconstruct_project_name' => 'Resconstruct Project Name',
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
		$criteria->compare('device_code',$this->device_code,true);
		$criteria->compare('mode_name',$this->mode_name,true);
		$criteria->compare('game_type_name',$this->game_type_name);
		$criteria->compare('polling_rate',$this->polling_rate);
		$criteria->compare('repeat_speed',$this->repeat_speed);
		$criteria->compare('no_rush_mode',$this->no_rush_mode);
		$criteria->compare('kill_keys',$this->kill_keys);
		$criteria->compare('lamp_light_type',$this->lamp_light_type);
		$criteria->compare('lamp_light_name',$this->lamp_light_name,true);
		$criteria->compare('keycap_light_type',$this->keycap_light_type);
		$criteria->compare('keycap_light_name',$this->keycap_light_name,true);
		$criteria->compare('resconstruct_project_type',$this->resconstruct_project_type);
		$criteria->compare('resconstruct_project_name',$this->resconstruct_project_name,true);

		return new CActiveDataProvider($this, array(
			'criteria'=>$criteria,
		));
	}

	/**
	 * Returns the static model of the specified AR class.
	 * Please note that you should have this exact method in all your CActiveRecord descendants!
	 * @param string $className active record class name.
	 * @return TdUserGameMode the static model class
	 */
	public static function model($className=__CLASS__)
	{
		return parent::model($className);
	}
}
