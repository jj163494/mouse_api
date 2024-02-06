<?php

/**
 * This is the model class for table "td_recommended_dpi".
 *
 * The followings are the available columns in table 'td_recommended_dpi':
 * @property integer $id
 * @property integer $screen_type
 * @property integer $mouse_pad_type
 * @property integer $recommended_dpi
 */
class TdRecommendedDpi extends CActiveRecord
{
	/**
	 * @return string the associated database table name
	 */
	public function tableName()
	{
		return 'td_recommended_dpi';
	}

	/**
	 * @return array validation rules for model attributes.
	 */
	public function rules()
	{
		// NOTE: you should only define rules for those attributes that
		// will receive user inputs.
		return array(
			array('id, screen_type, mouse_pad_type, recommended_dpi', 'required'),
			array('id, screen_type, mouse_pad_type, recommended_dpi', 'numerical', 'integerOnly'=>true),
			// The following rule is used by search().
			// @todo Please remove those attributes that should not be searched.
			array('id, screen_type, mouse_pad_type, recommended_dpi', 'safe', 'on'=>'search'),
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
			'screen_type' => 'Screen Type',
			'mouse_pad_type' => 'Mouse Pad Type',
			'recommended_dpi' => 'Recommended Dpi',
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
		$criteria->compare('screen_type',$this->screen_type);
		$criteria->compare('mouse_pad_type',$this->mouse_pad_type);
		$criteria->compare('recommended_dpi',$this->recommended_dpi);

		return new CActiveDataProvider($this, array(
			'criteria'=>$criteria,
		));
	}

	/**
	 * Returns the static model of the specified AR class.
	 * Please note that you should have this exact method in all your CActiveRecord descendants!
	 * @param string $className active record class name.
	 * @return TdRecommendedDpi the static model class
	 */
	public static function model($className=__CLASS__)
	{
		return parent::model($className);
	}
}
