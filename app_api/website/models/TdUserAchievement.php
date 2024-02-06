<?php

/**
 * This is the model class for table "td_user_achievement".
 *
 * The followings are the available columns in table 'td_user_achievement':
 * @property integer $id
 * @property integer $userid
 * @property integer $achieve_type
 * @property integer $achieve_name
 * @property integer $achieve_level
 * @property integer $user_value
 */
class TdUserAchievement extends CActiveRecord
{
	/**
	 * @return string the associated database table name
	 */
	public function tableName()
	{
		return 'td_user_achievement';
	}

	/**
	 * @return array validation rules for model attributes.
	 */
	public function rules()
	{
		// NOTE: you should only define rules for those attributes that
		// will receive user inputs.
		return array(
			array('userid, achieve_type, achieve_name, achieve_level, user_value', 'required'),
			array('userid, achieve_type, achieve_name, achieve_level, user_value', 'numerical', 'integerOnly'=>true),
			// The following rule is used by search().
			// @todo Please remove those attributes that should not be searched.
			array('id, userid, achieve_type, achieve_name, achieve_level, user_value', 'safe', 'on'=>'search'),
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
			'achieve_type' => '成就类型',
			'achieve_name' => '成就名称',
			'achieve_level' => '成就级别',
			'user_value' => '用户值',
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
		$criteria->compare('achieve_type',$this->achieve_type);
		$criteria->compare('achieve_name',$this->achieve_name);
		$criteria->compare('achieve_level',$this->achieve_level);
		$criteria->compare('user_value',$this->user_value);

		return new CActiveDataProvider($this, array(
			'criteria'=>$criteria,
		));
	}

	/**
	 * Returns the static model of the specified AR class.
	 * Please note that you should have this exact method in all your CActiveRecord descendants!
	 * @param string $className active record class name.
	 * @return TdUserAchievement the static model class
	 */
	public static function model($className=__CLASS__)
	{
		return parent::model($className);
	}
}
