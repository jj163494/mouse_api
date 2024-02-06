<?php

/**
 * This is the model class for table "td_activity".
 *
 * The followings are the available columns in table 'td_activity':
 * @property integer $id
 * @property integer $activity_type
 * @property string $title
 * @property string $message
 * @property integer $start_time
 * @property integer $end_time
 * @property string $activity_image
 * @property string $activity_body
 * @property string $activity_url
 * @property integer $created_time
 */
class TdActivity extends CActiveRecord
{
	/**
	 * @return string the associated database table name
	 */
	public function tableName()
	{
		return 'td_activity';
	}

	/**
	 * @return array validation rules for model attributes.
	 */
	public function rules()
	{
		// NOTE: you should only define rules for those attributes that
		// will receive user inputs.
		return array(
			array('activity_type, start_time, end_time, created_time', 'numerical', 'integerOnly'=>true),
			array('title, activity_image', 'length', 'max'=>256),
			array('activity_url', 'length', 'max'=>128),
			array('message, activity_body', 'safe'),
			// The following rule is used by search().
			// @todo Please remove those attributes that should not be searched.
			array('id, activity_type, title, message, start_time, end_time, activity_image, activity_body, activity_url, created_time', 'safe', 'on'=>'search'),
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
			'activity_type' => 'Activity Type',
			'title' => 'Title',
			'message' => 'Message',
			'start_time' => 'Start Time',
			'end_time' => 'End Time',
			'activity_image' => 'Activity Image',
			'activity_body' => 'Activity Body',
			'activity_url' => 'Activity Url',
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

		$criteria->compare('id',$this->id);
		$criteria->compare('activity_type',$this->activity_type);
		$criteria->compare('title',$this->title,true);
		$criteria->compare('message',$this->message,true);
		$criteria->compare('start_time',$this->start_time);
		$criteria->compare('end_time',$this->end_time);
		$criteria->compare('activity_image',$this->activity_image,true);
		$criteria->compare('activity_body',$this->activity_body,true);
		$criteria->compare('activity_url',$this->activity_url,true);
		$criteria->compare('created_time',$this->created_time);

		return new CActiveDataProvider($this, array(
			'criteria'=>$criteria,
		));
	}

	/**
	 * Returns the static model of the specified AR class.
	 * Please note that you should have this exact method in all your CActiveRecord descendants!
	 * @param string $className active record class name.
	 * @return TdActivity the static model class
	 */
	public static function model($className=__CLASS__)
	{
		return parent::model($className);
	}
}
