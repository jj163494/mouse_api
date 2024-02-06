<?php

/**
 * This is the model class for table "td_message".
 *
 * The followings are the available columns in table 'td_message':
 * @property integer $id
 * @property integer $userid
 * @property string $title
 * @property string $message
 * @property integer $message_type
 * @property integer $send_userid
 * @property string $send_username
 * @property integer $is_read
 * @property integer $created_time
 */
class TdMessage extends CActiveRecord
{
	/**
	 * @return string the associated database table name
	 */
	public function tableName()
	{
		return 'td_message';
	}

	/**
	 * @return array validation rules for model attributes.
	 */
	public function rules()
	{
		// NOTE: you should only define rules for those attributes that
		// will receive user inputs.
		return array(
			array('userid, message_type, send_userid, is_read, created_time', 'numerical', 'integerOnly'=>true),
			array('title', 'length', 'max'=>128),
			array('send_username', 'length', 'max'=>32),
			array('message', 'safe'),
			// The following rule is used by search().
			// @todo Please remove those attributes that should not be searched.
			array('id, userid, title, message, message_type, send_userid, send_username, is_read, created_time', 'safe', 'on'=>'search'),
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
			'title' => 'Title',
			'message' => 'Message',
			'message_type' => 'Message Type',
			'send_userid' => 'Send Userid',
			'send_username' => 'Send Username',
			'is_read' => 'Is Read',
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
		$criteria->compare('userid',$this->userid);
		$criteria->compare('title',$this->title,true);
		$criteria->compare('message',$this->message,true);
		$criteria->compare('message_type',$this->message_type);
		$criteria->compare('send_userid',$this->send_userid);
		$criteria->compare('send_username',$this->send_username,true);
		$criteria->compare('is_read',$this->is_read);
		$criteria->compare('created_time',$this->created_time);

		return new CActiveDataProvider($this, array(
			'criteria'=>$criteria,
		));
	}

	/**
	 * Returns the static model of the specified AR class.
	 * Please note that you should have this exact method in all your CActiveRecord descendants!
	 * @param string $className active record class name.
	 * @return TdMessage the static model class
	 */
	public static function model($className=__CLASS__)
	{
		return parent::model($className);
	}
}
