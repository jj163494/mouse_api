<?php

/**
 * This is the model class for table "td_contest_config".
 *
 * The followings are the available columns in table 'td_contest_config':
 * @property integer $cc_id
 * @property integer $cm_id
 * @property string $device_model
 * @property integer $device_type
 * @property string $config_content
 * @property integer $created_time
 */
class TdContestConfig extends CActiveRecord
{
	/**
	 * @return string the associated database table name
	 */
	public function tableName()
	{
		return 'td_contest_config';
	}

	/**
	 * @return array validation rules for model attributes.
	 */
	public function rules()
	{
		// NOTE: you should only define rules for those attributes that
		// will receive user inputs.
		return array(
			array('cm_id, device_type, config_content, created_time', 'required'),
			array('cm_id, device_type, created_time', 'numerical', 'integerOnly'=>true),
			array('device_model', 'length', 'max'=>64),
			// The following rule is used by search().
			// @todo Please remove those attributes that should not be searched.
			array('cc_id, cm_id, device_model, device_type, config_content, created_time', 'safe', 'on'=>'search'),
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
			'cc_id' => '竞技模式配置id',
			'cm_id' => '竞技模式id 100以下为推荐游戏id,100以上为竞技模式id',
			'device_model' => '设备型号',
			'device_type' => '设备类型 1鼠标 2键盘',
			'config_content' => '配置内容',
			'created_time' => '创建时间',
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

		$criteria->compare('cc_id',$this->cc_id);
		$criteria->compare('cm_id',$this->cm_id);
		$criteria->compare('device_model',$this->device_model,true);
		$criteria->compare('device_type',$this->device_type);
		$criteria->compare('config_content',$this->config_content,true);
		$criteria->compare('created_time',$this->created_time);

		return new CActiveDataProvider($this, array(
			'criteria'=>$criteria,
		));
	}

	/**
	 * Returns the static model of the specified AR class.
	 * Please note that you should have this exact method in all your CActiveRecord descendants!
	 * @param string $className active record class name.
	 * @return TdContestConfig the static model class
	 */
	public static function model($className=__CLASS__)
	{
		return parent::model($className);
	}


	/**
	 * 获取竞技模式配置列表
	 * @param string $condition
	 * @param string $field
	 * @param int $page_size
	 * @param int $offset
	 * @param string $order
	 * @param string $group
	 * @return array|mixed|null
	 */
	public function getContestConfigList($condition = "", $field = '*', $page_size = 1000, $offset = 0, $order = 'cc_id DESC', $group = ''){
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
	 * 获取竞技模式配置明细
	 * @param string $condition
	 * @param string $field
	 * @return array|mixed|null
	 */
	public function getContestConfigInfo($condition = "", $field = "*"){
		$criteria = array(
			'select' => $field,
			'condition' => $condition,
		);
		return $this->find($criteria);
	}
}
