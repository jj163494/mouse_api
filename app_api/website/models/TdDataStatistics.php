<?php

/**
 * This is the model class for table "td_data_statistics".
 *
 * The followings are the available columns in table 'td_data_statistics':
 * @property integer $id
 * @property integer $userid
 * @property string $datetime
 * @property integer $time_0
 * @property integer $time_2
 * @property integer $time_4
 * @property integer $time_6
 * @property integer $time_8
 * @property integer $time_10
 * @property integer $time_12
 * @property integer $time_14
 * @property integer $time_16
 * @property integer $time_18
 * @property integer $time_20
 * @property integer $time_22
 * @property integer $today_total_statistics
 * @property integer $mouse_knock
 * @property integer $keycap_knock
 */
class TdDataStatistics extends CActiveRecord
{
	/**
	 * @return string the associated database table name
	 */
	public function tableName()
	{
		return 'td_data_statistics';
	}

	/**
	 * @return array validation rules for model attributes.
	 */
	public function rules()
	{
		// NOTE: you should only define rules for those attributes that
		// will receive user inputs.
		return array(
			array('userid, datetime, today_total_statistics', 'required'),
			array('userid, time_0, time_2, time_4, time_6, time_8, time_10, time_12, time_14, time_16, time_18, time_20, time_22, today_total_statistics', 'numerical', 'integerOnly'=>true),
			array('datetime', 'length', 'max'=>10),
			// The following rule is used by search().
			// @todo Please remove those attributes that should not be searched.
			array('id, userid, datetime, time_2, time_4, time_6, time_8, time_10, time_12, time_14, time_16, time_18, time_20, time_22, time_0, today_total_statistics, mouse_knock, keycap_knock', 'safe', 'on'=>'search'),
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
			'datetime' => 'Datetime',
			'time_2' => 'Time 2',
			'time_4' => 'Time 4',
			'time_6' => 'Time 6',
			'time_8' => 'Time 8',
			'time_10' => 'Time 10',
			'time_12' => 'Time 12',
			'time_14' => 'Time 14',
			'time_16' => 'Time 16',
			'time_18' => 'Time 18',
			'time_20' => 'Time 20',
			'time_22' => 'Time 22',
			'time_0' => 'Time 0',
			'today_total_statistics' => 'Today Total Statistics',
			'mouse_knock' => 'Mouse Knock',
			'keycap_knock' => 'Keycap Knock',
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
		$criteria->compare('datetime',$this->datetime,true);
		$criteria->compare('time_2',$this->time_2);
		$criteria->compare('time_4',$this->time_4);
		$criteria->compare('time_6',$this->time_6);
		$criteria->compare('time_8',$this->time_8);
		$criteria->compare('time_10',$this->time_10);
		$criteria->compare('time_12',$this->time_12);
		$criteria->compare('time_14',$this->time_14);
		$criteria->compare('time_16',$this->time_16);
		$criteria->compare('time_18',$this->time_18);
		$criteria->compare('time_20',$this->time_20);
		$criteria->compare('time_22',$this->time_22);
		$criteria->compare('time_0',$this->time_0);
		$criteria->compare('today_total_statistics',$this->today_total_statistics);
		$criteria->compare('mouse_knock',$this->mouse_knock);
		$criteria->compare('keycap_knock',$this->keycap_knock);

		return new CActiveDataProvider($this, array(
			'criteria'=>$criteria,
		));
	}

	/**
	 * Returns the static model of the specified AR class.
	 * Please note that you should have this exact method in all your CActiveRecord descendants!
	 * @param string $className active record class name.
	 * @return TdDataStatistics the static model class
	 */
	public static function model($className=__CLASS__)
	{
		return parent::model($className);
	}
}
