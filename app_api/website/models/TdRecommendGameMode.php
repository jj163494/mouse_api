<?php

/**
 * This is the model class for table "td_recommend_game_mode".
 *
 * The followings are the available columns in table 'td_recommend_game_mode':
 * @property integer $id
 * @property string $device_code
 * @property string $mode_name
 * @property integer $game_type
 * @property integer $polling_rate
 * @property integer $repeat_speed
 * @property integer $no_rush_mode
 * @property integer $kill_keys
 * @property string $lamp_light_name
 * @property string $lamp_light_content
 * @property string $keycap_light_name
 * @property string $keycap_light_content
 * @property integer $resconstruct_project_type
 * @property string $resconstruct_project_name
 */
class TdRecommendGameMode extends CActiveRecord
{
	/**
	 * @return string the associated database table name
	 */
	public function tableName()
	{
		return 'td_recommend_game_mode';
	}

	/**
	 * @return array validation rules for model attributes.
	 */
	public function rules()
	{
		// NOTE: you should only define rules for those attributes that
		// will receive user inputs.
		return array(
			array('device_code, mode_name, game_type, polling_rate, repeat_speed, no_rush_mode, lamp_light_name, lamp_light_content, keycap_light_name, keycap_light_content, resconstruct_project_type, resconstruct_project_name', 'required'),
			array('game_type, polling_rate, repeat_speed, no_rush_mode, resconstruct_project_type', 'numerical', 'integerOnly'=>true),
			array('device_code', 'length', 'max'=>65),
			array('mode_name, lamp_light_name, keycap_light_name, resconstruct_project_name', 'length', 'max'=>54),
			// The following rule is used by search().
			// @todo Please remove those attributes that should not be searched.
			array('id, device_code, mode_name, game_type, polling_rate, repeat_speed, no_rush_mode, kill_keys, lamp_light_name, lamp_light_content, keycap_light_name, keycap_light_content, resconstruct_project_type, resconstruct_project_name', 'safe', 'on'=>'search'),
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
			'device_code' => 'Device Code',
			'mode_name' => 'Mode Name',
			'game_type' => 'Game Type',
			'polling_rate' => 'Polling Rate',
			'repeat_speed' => 'Repeat Speed',
			'no_rush_mode' => 'No Rush Mode',
			'kill_keys' => 'Kill Keys',
			'lamp_light_name' => 'Lamp Light Name',
			'lamp_light_content' => 'Lamp Light Content',
			'keycap_light_name' => 'Keycap Light Name',
			'keycap_light_content' => 'Keycap Light Content',
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
		$criteria->compare('device_code',$this->device_code,true);
		$criteria->compare('mode_name',$this->mode_name,true);
		$criteria->compare('game_type',$this->game_type);
		$criteria->compare('polling_rate',$this->polling_rate);
		$criteria->compare('repeat_speed',$this->repeat_speed);
		$criteria->compare('no_rush_mode',$this->no_rush_mode);
		$criteria->compare('kill_keys',$this->kill_keys);
		$criteria->compare('lamp_light_name',$this->lamp_light_name,true);
		$criteria->compare('lamp_light_content',$this->lamp_light_content,true);
		$criteria->compare('keycap_light_name',$this->keycap_light_name,true);
		$criteria->compare('keycap_light_content',$this->keycap_light_content,true);
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
	 * @return TdRecommendGameMode the static model class
	 */
	public static function model($className=__CLASS__)
	{
		return parent::model($className);
	}

	/**
	 * 获取游戏列表
	 * @param string $condition
	 * @param string $field
	 * @param int $page_size
	 * @param int $offset
	 * @param string $order
	 * @param string $group
	 * @return array|mixed|null
	 */
	public function getGameList($condition = "", $field = '*', $page_size = 1000, $offset = 0, $order = 'id DESC', $group = ''){
		$criteria = array(
			'select' => $field,
			'condition' => $condition,
			'limit' => $page_size,
			'offset' => $offset,
			'order' => $order,
			'group' => $group
		);
		return $this->findAll($criteria);
	}


	/**
	 * 获取游戏明细
	 * @param string $condition
	 * @param string $field
	 * @param string $order
	 * @return array|mixed|null
	 */
	public function getGameInfo($condition = "", $field = "*", $order = "id DESC"){
		$criteria = array(
			'select' => $field,
			'condition' => $condition,
			'order' => $order,
		);
		return $this->find($criteria);
	}
}
