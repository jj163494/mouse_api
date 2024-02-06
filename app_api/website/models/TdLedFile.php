<?php

/**
 * This is the model class for table "td_led_file".
 *
 * The followings are the available columns in table 'td_led_file':
 * @property integer $id
 * @property integer $group_id
 * @property string $title
 * @property string $led_file_url
 * @property string $desc
 */
class TdLedFile extends CActiveRecord
{
	/**
	 * @return string the associated database table name
	 */
	public function tableName()
	{
		return 'td_led_file';
	}

	/**
	 * @return array validation rules for model attributes.
	 */
	public function rules()
	{
		// NOTE: you should only define rules for those attributes that
		// will receive user inputs.
		return array(
			array('group_id', 'numerical', 'integerOnly'=>true),
			array('title, led_file_url', 'length', 'max'=>32),
			array('desc', 'length', 'max'=>128),
			// The following rule is used by search().
			// @todo Please remove those attributes that should not be searched.
			array('id, group_id, title, led_file_url, desc', 'safe', 'on'=>'search'),
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
			'group_id' => 'Group',
			'title' => 'Title',
			'led_file_url' => 'Led File Url',
			'desc' => 'Desc',
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
		$criteria->compare('group_id',$this->group_id);
		$criteria->compare('title',$this->title,true);
		$criteria->compare('led_file_url',$this->led_file_url,true);
		$criteria->compare('desc',$this->desc,true);

		return new CActiveDataProvider($this, array(
			'criteria'=>$criteria,
		));
	}

	/**
	 * Returns the static model of the specified AR class.
	 * Please note that you should have this exact method in all your CActiveRecord descendants!
	 * @param string $className active record class name.
	 * @return TdLedFile the static model class
	 */
	public static function model($className=__CLASS__)
	{
		return parent::model($className);
	}
}
