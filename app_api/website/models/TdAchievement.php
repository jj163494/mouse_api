<?php

/**
 * This is the model class for table "td_achievement".
 *
 * The followings are the available columns in table 'td_achievement':
 * @property integer $id
 * @property integer $achieve_type
 * @property string $achieve_name
 * @property integer $achieve_level
 * @property integer $level_value
 * @property integer $achieve_score
 */
class TdAchievement extends CActiveRecord
{
	/**
	 * @return string the associated database table name
	 */
	public function tableName()
	{
		return 'td_achievement';
	}

	/**
	 * @return array validation rules for model attributes.
	 */
	public function rules()
	{
		// NOTE: you should only define rules for those attributes that
		// will receive user inputs.
		return array(
			array('achieve_type, achieve_name, achieve_level, level_value, achieve_score', 'required'),
			array('achieve_type, achieve_level, level_value, achieve_score', 'numerical', 'integerOnly'=>true),
			array('achieve_name', 'length', 'max'=>64),
			// The following rule is used by search().
			// @todo Please remove those attributes that should not be searched.
			array('id, achieve_type, achieve_name, achieve_level, level_value, achieve_score', 'safe', 'on'=>'search'),
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
			'achieve_type' => '成就类型',
			'achieve_name' => '成就名称',
			'achieve_level' => '成就级别',
			'level_value' => '级别值',
			'achieve_score' => '成就奖励',
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
		$criteria->compare('achieve_type',$this->achieve_type);
		$criteria->compare('achieve_name',$this->achieve_name,true);
		$criteria->compare('achieve_level',$this->achieve_level);
		$criteria->compare('level_value',$this->level_value);
		$criteria->compare('achieve_score',$this->achieve_score);

		return new CActiveDataProvider($this, array(
			'criteria'=>$criteria,
		));
	}

	/**
	 * Returns the static model of the specified AR class.
	 * Please note that you should have this exact method in all your CActiveRecord descendants!
	 * @param string $className active record class name.
	 * @return TdAchievement the static model class
	 */
	public static function model($className=__CLASS__)
	{
		return parent::model($className);
	}
}
