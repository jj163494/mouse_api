<?php

/**
 * This is the model class for table "td_dpi".
 *
 * The followings are the available columns in table 'td_dpi':
 * @property integer $id
 * @property integer $userId
 * @property integer $dpi
 * @property integer $created_time
 * @property string $username_en
 * @property string $username_cn
 * @property string $nick
 * @property string $profession
 * @property string $achievement
 * @property string $equipment
 * @property string $desc
 * @property string $star_type
 */
class TdDpi extends CActiveRecord
{
	/**
	 * @return string the associated database table name
	 */
	public function tableName()
	{
		return 'td_dpi';
	}

	/**
	 * @return array validation rules for model attributes.
	 */
	public function rules()
	{
		// NOTE: you should only define rules for those attributes that
		// will receive user inputs.
		return array(
			array('userId', 'required'),
			array('userId, dpi, created_time', 'numerical', 'integerOnly'=>true),
			array('username_en, username_cn, nick, profession', 'length', 'max'=>45),
			array('achievement, equipment', 'length', 'max'=>128),
			array('desc', 'length', 'max'=>256),
			array('star_type', 'length', 'max'=>16),
			// The following rule is used by search().
			// @todo Please remove those attributes that should not be searched.
			array('id, userId, dpi, created_time, username_en, username_cn, nick, profession, achievement, equipment, desc, star_type', 'safe', 'on'=>'search'),
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
			'userId' => 'User',
			'dpi' => 'Dpi',
			'created_time' => 'Created Time',
			'username_en' => 'Username En',
			'username_cn' => 'Username Cn',
			'nick' => 'Nick',
			'profession' => 'Profession',
			'achievement' => 'Achievement',
			'equipment' => 'Equipment',
			'desc' => 'Desc',
			'star_type' => 'Star Type',
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
		$criteria->compare('userId',$this->userId);
		$criteria->compare('dpi',$this->dpi);
		$criteria->compare('created_time',$this->created_time);
		$criteria->compare('username_en',$this->username_en,true);
		$criteria->compare('username_cn',$this->username_cn,true);
		$criteria->compare('nick',$this->nick,true);
		$criteria->compare('profession',$this->profession,true);
		$criteria->compare('achievement',$this->achievement,true);
		$criteria->compare('equipment',$this->equipment,true);
		$criteria->compare('desc',$this->desc,true);
		$criteria->compare('star_type',$this->star_type,true);

		return new CActiveDataProvider($this, array(
			'criteria'=>$criteria,
		));
	}

	/**
	 * Returns the static model of the specified AR class.
	 * Please note that you should have this exact method in all your CActiveRecord descendants!
	 * @param string $className active record class name.
	 * @return TdDpi the static model class
	 */
	public static function model($className=__CLASS__)
	{
		return parent::model($className);
	}
}
