<?php

/**
 * This is the model class for table "td_computer_history".
 *
 * The followings are the available columns in table 'td_computer_history':
 * @property integer $ch_id
 * @property integer $pc_id
 * @property integer $time_long
 * @property integer $run_time_0
 * @property integer $run_time_1
 * @property integer $run_time_2
 * @property integer $run_time_3
 * @property integer $run_time_4
 * @property integer $run_time_5
 * @property integer $run_time_6
 * @property integer $run_time_7
 * @property integer $run_time_8
 * @property integer $run_time_9
 * @property integer $run_time_10
 * @property integer $run_time_11
 * @property integer $run_time_12
 * @property integer $run_time_13
 * @property integer $run_time_14
 * @property integer $run_time_15
 * @property integer $run_time_16
 * @property integer $run_time_17
 * @property integer $run_time_18
 * @property integer $run_time_19
 * @property integer $run_time_20
 * @property integer $run_time_21
 * @property integer $run_time_22
 * @property integer $run_time_23
 * @property integer $power_on
 * @property integer $power_off
 * @property integer $created_time
 * @property integer $update_time
 * @property integer $is_delete
 *
 * The followings are the available model relations:
 * @property TdComputer $pc
 */
class TdComputerHistory extends CActiveRecord
{
	/**
	 * @return string the associated database table name
	 */
	public function tableName()
	{
		return 'td_computer_history';
	}

	/**
	 * @return array validation rules for model attributes.
	 */
	public function rules()
	{
		// NOTE: you should only define rules for those attributes that
		// will receive user inputs.
		return array(
			array('pc_id, time_long, power_on, power_off', 'required'),
			array('pc_id, time_long, run_time_0, run_time_1, run_time_2, run_time_3, run_time_4, run_time_5, run_time_6, run_time_7, run_time_8, run_time_9, run_time_10, run_time_11, run_time_12, run_time_13, run_time_14, run_time_15, run_time_16, run_time_17, run_time_18, run_time_19, run_time_20, run_time_21, run_time_22, run_time_23, power_on, power_off, created_time, update_time, is_delete', 'numerical', 'integerOnly'=>true),
			// The following rule is used by search().
			// @todo Please remove those attributes that should not be searched.
			array('ch_id, pc_id, time_long, run_time_0, run_time_1, run_time_2, run_time_3, run_time_4, run_time_5, run_time_6, run_time_7, run_time_8, run_time_9, run_time_10, run_time_11, run_time_12, run_time_13, run_time_14, run_time_15, run_time_16, run_time_17, run_time_18, run_time_19, run_time_20, run_time_21, run_time_22, run_time_23, power_on, power_off, created_time, update_time, is_delete', 'safe', 'on'=>'search'),
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
			'pc' => array(self::BELONGS_TO, 'TdComputer', 'pc_id'),
		);
	}

	/**
	 * @return array customized attribute labels (name=>label)
	 */
	public function attributeLabels()
	{
		return array(
			'ch_id' => '主机运行历史id',
			'pc_id' => '主机id',
			'time_long' => '运行时长(秒)',
			'run_time_0' => '运行时段(0点)',
			'run_time_1' => '运行时段(1点)',
			'run_time_2' => '运行时段(2点)',
			'run_time_3' => '运行时段(3点)',
			'run_time_4' => '运行时段(4点)',
			'run_time_5' => '运行时段(5点)',
			'run_time_6' => '运行时段(6点)',
			'run_time_7' => '运行时段(7点)',
			'run_time_8' => '运行时段(8点)',
			'run_time_9' => '运行时段(9点)',
			'run_time_10' => '运行时段(10点)',
			'run_time_11' => '运行时段(11点)',
			'run_time_12' => '运行时段(12点)',
			'run_time_13' => '运行时段(13点)',
			'run_time_14' => '运行时段(14点)',
			'run_time_15' => '运行时段(15点)',
			'run_time_16' => '运行时段(16点)',
			'run_time_17' => '运行时段(17点)',
			'run_time_18' => '运行时段(18点)',
			'run_time_19' => '运行时段(19点)',
			'run_time_20' => '运行时段(20点)',
			'run_time_21' => '运行时段(21点)',
			'run_time_22' => '运行时段(22点)',
			'run_time_23' => '运行时段(23点)',
			'power_on' => '开机时间',
			'power_off' => '关机时间',
			'created_time' => '创建时间',
			'update_time' => '修改时间',
			'is_delete' => '0未删除,1已删除',
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

		$criteria->compare('ch_id',$this->ch_id);
		$criteria->compare('pc_id',$this->pc_id);
		$criteria->compare('time_long',$this->time_long);
		$criteria->compare('run_time_0',$this->run_time_0);
		$criteria->compare('run_time_1',$this->run_time_1);
		$criteria->compare('run_time_2',$this->run_time_2);
		$criteria->compare('run_time_3',$this->run_time_3);
		$criteria->compare('run_time_4',$this->run_time_4);
		$criteria->compare('run_time_5',$this->run_time_5);
		$criteria->compare('run_time_6',$this->run_time_6);
		$criteria->compare('run_time_7',$this->run_time_7);
		$criteria->compare('run_time_8',$this->run_time_8);
		$criteria->compare('run_time_9',$this->run_time_9);
		$criteria->compare('run_time_10',$this->run_time_10);
		$criteria->compare('run_time_11',$this->run_time_11);
		$criteria->compare('run_time_12',$this->run_time_12);
		$criteria->compare('run_time_13',$this->run_time_13);
		$criteria->compare('run_time_14',$this->run_time_14);
		$criteria->compare('run_time_15',$this->run_time_15);
		$criteria->compare('run_time_16',$this->run_time_16);
		$criteria->compare('run_time_17',$this->run_time_17);
		$criteria->compare('run_time_18',$this->run_time_18);
		$criteria->compare('run_time_19',$this->run_time_19);
		$criteria->compare('run_time_20',$this->run_time_20);
		$criteria->compare('run_time_21',$this->run_time_21);
		$criteria->compare('run_time_22',$this->run_time_22);
		$criteria->compare('run_time_23',$this->run_time_23);
		$criteria->compare('power_on',$this->power_on);
		$criteria->compare('power_off',$this->power_off);
		$criteria->compare('created_time',$this->created_time);
		$criteria->compare('update_time',$this->update_time);
		$criteria->compare('is_delete',$this->is_delete);

		return new CActiveDataProvider($this, array(
			'criteria'=>$criteria,
		));
	}

	/**
	 * Returns the static model of the specified AR class.
	 * Please note that you should have this exact method in all your CActiveRecord descendants!
	 * @param string $className active record class name.
	 * @return TdComputerHistory the static model class
	 */
	public static function model($className=__CLASS__)
	{
		return parent::model($className);
	}


	/**
	 * 获取主机运行历史明细
	 * @param string $condition
	 * @param string $field
	 * @param string $order
	 * @return array|mixed|null
	 */
	public function getHistoryInfo($condition = "", $field = "*", $order = "ch_id DESC"){
		$condition .= " AND is_delete = 0";

		$criteria = array(
			'select' => $field,
			'condition' => $condition,
			'order' => $order,
		);
		return $this->find($criteria);
	}
}
