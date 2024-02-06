<?php

/**
 * This is the model class for table "td_feedback".
 *
 * The followings are the available columns in table 'td_feedback':
 * @property integer $id
 * @property string $question_type
 * @property string $question
 * @property integer $userid
 * @property string $device_sn
 * @property string $image1
 * @property integer $created_time
 * @property string $image2
 * @property string $image3
 */
class TdFeedback extends CActiveRecord
{
	/**
	 * @return string the associated database table name
	 */
	public function tableName()
	{
		return 'td_feedback';
	}

	/**
	 * @return array validation rules for model attributes.
	 */
	public function rules()
	{
		// NOTE: you should only define rules for those attributes that
		// will receive user inputs.
		return array(
			array('userid, created_time', 'numerical', 'integerOnly'=>true),
			array('question_type', 'length', 'max'=>255),
			array('question', 'length', 'max'=>256),
			array('device_sn', 'length', 'max'=>32),
			array('image1, image2, image3', 'length', 'max'=>128),
			// The following rule is used by search().
			// @todo Please remove those attributes that should not be searched.
			array('id, question_type, question, userid, device_sn, image1, created_time, image2, image3', 'safe', 'on'=>'search'),
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
			'question_type' => 'Question Type',
			'question' => 'Question',
			'userid' => 'Userid',
			'device_sn' => 'Device Sn',
			'image1' => 'Image1',
			'created_time' => 'Created Time',
			'image2' => 'Image2',
			'image3' => 'Image3',
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
		$criteria->compare('question_type',$this->question_type,true);
		$criteria->compare('question',$this->question,true);
		$criteria->compare('userid',$this->userid);
		$criteria->compare('device_sn',$this->device_sn,true);
		$criteria->compare('image1',$this->image1,true);
		$criteria->compare('created_time',$this->created_time);
		$criteria->compare('image2',$this->image2,true);
		$criteria->compare('image3',$this->image3,true);

		return new CActiveDataProvider($this, array(
			'criteria'=>$criteria,
		));
	}

	/**
	 * Returns the static model of the specified AR class.
	 * Please note that you should have this exact method in all your CActiveRecord descendants!
	 * @param string $className active record class name.
	 * @return TdFeedback the static model class
	 */
	public static function model($className=__CLASS__)
	{
		return parent::model($className);
	}
}
