<?php

/**
 * This is the model class for table "td_task".
 *
 * The followings are the available columns in table 'td_task':
 * @property string $id
 * @property integer $task_type
 * @property integer $task_goal
 * @property integer $task_reward
 * @property string $task_name
 */
class TdTask extends CActiveRecord
{
	/**
	 * @return string the associated database table name
	 */
	public function tableName()
	{
		return 'td_task';
	}

	/**
	 * @return array validation rules for model attributes.
	 */
	public function rules()
	{
		// NOTE: you should only define rules for those attributes that
		// will receive user inputs.
		return array(
			array('task_type, task_goal, task_reward', 'numerical', 'integerOnly'=>true),
			array('task_name', 'length', 'max'=>255),
			// The following rule is used by search().
			// @todo Please remove those attributes that should not be searched.
			array('id, task_type, task_goal, task_reward, task_name', 'safe', 'on'=>'search'),
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
			'task_type' => 'Task Type',
			'task_goal' => 'Task Goal',
			'task_reward' => 'Task Reward',
			'task_name' => 'Task Name',
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
		$criteria->compare('task_type',$this->task_type);
		$criteria->compare('task_goal',$this->task_goal);
		$criteria->compare('task_reward',$this->task_reward);
		$criteria->compare('task_name',$this->task_name,true);

		return new CActiveDataProvider($this, array(
			'criteria'=>$criteria,
		));
	}

	/**
	 * Returns the static model of the specified AR class.
	 * Please note that you should have this exact method in all your CActiveRecord descendants!
	 * @param string $className active record class name.
	 * @return TdTask the static model class
	 */
	public static function model($className=__CLASS__)
	{
		return parent::model($className);
	}
}
