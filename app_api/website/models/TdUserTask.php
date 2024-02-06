<?php

/**
 * This is the model class for table "td_user_task".
 *
 * The followings are the available columns in table 'td_user_task':
 * @property integer $id
 * @property integer $userid
 * @property integer $task_id
 * @property string $task_name
 * @property integer $task_type
 * @property integer $task_goal
 * @property integer $task_reward
 * @property integer $task_status
 * @property integer $created_time
 */
class TdUserTask extends CActiveRecord
{
	/**
	 * @return string the associated database table name
	 */
	public function tableName()
	{
		return 'td_user_task';
	}

	/**
	 * @return array validation rules for model attributes.
	 */
	public function rules()
	{
		// NOTE: you should only define rules for those attributes that
		// will receive user inputs.
		return array(
			array('userid, task_id, task_type, task_goal, task_reward, task_status, created_time', 'numerical', 'integerOnly'=>true),
			array('task_name', 'length', 'max'=>100),
			// The following rule is used by search().
			// @todo Please remove those attributes that should not be searched.
			array('id, userid, task_id, task_name, task_type, task_goal, task_reward, task_status, created_time', 'safe', 'on'=>'search'),
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
			'id' => '自增id',
			'userid' => '用户id',
			'task_id' => '任务id',
			'task_name' => '任务名',
			'task_type' => '任务类型',
			'task_goal' => '任务目标',
			'task_reward' => '目标奖励',
			'task_status' => '任务状态',
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

		$criteria->compare('id',$this->id);
		$criteria->compare('userid',$this->userid);
		$criteria->compare('task_id',$this->task_id);
		$criteria->compare('task_name',$this->task_name,true);
		$criteria->compare('task_type',$this->task_type);
		$criteria->compare('task_goal',$this->task_goal);
		$criteria->compare('task_reward',$this->task_reward);
		$criteria->compare('task_status',$this->task_status);
		$criteria->compare('created_time',$this->created_time);

		return new CActiveDataProvider($this, array(
			'criteria'=>$criteria,
		));
	}

	/**
	 * Returns the static model of the specified AR class.
	 * Please note that you should have this exact method in all your CActiveRecord descendants!
	 * @param string $className active record class name.
	 * @return TdUserTask the static model class
	 */
	public static function model($className=__CLASS__)
	{
		return parent::model($className);
	}
}
