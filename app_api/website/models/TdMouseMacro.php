<?php

/**
 * This is the model class for table "td_mouse_macro".
 *
 * The followings are the available columns in table 'td_mouse_macro':
 * @property integer $id
 * @property integer $userid
 * @property string $key_content
 * @property string $macro_name
 * @property string $macro_content
 * @property integer $created_time
 */
class TdMouseMacro extends CActiveRecord
{
	/**
	 * @return string the associated database table name
	 */
	public function tableName()
	{
		return 'td_mouse_macro';
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
			array('macro_name', 'length', 'max'=>32),
			array('key_content, macro_content', 'safe'),
			// The following rule is used by search().
			// @todo Please remove those attributes that should not be searched.
			array('id, userid, key_content, macro_name, macro_content, created_time', 'safe', 'on'=>'search'),
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
			'key_content' => 'Key Content',
			'macro_name' => 'Macro Name',
			'macro_content' => 'Macro Content',
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
		$criteria->compare('key_content',$this->key_content,true);
		$criteria->compare('macro_name',$this->macro_name,true);
		$criteria->compare('macro_content',$this->macro_content,true);
		$criteria->compare('created_time',$this->created_time);

		return new CActiveDataProvider($this, array(
			'criteria'=>$criteria,
		));
	}

	/**
	 * Returns the static model of the specified AR class.
	 * Please note that you should have this exact method in all your CActiveRecord descendants!
	 * @param string $className active record class name.
	 * @return TdMouseMacro the static model class
	 */
	public static function model($className=__CLASS__)
	{
		return parent::model($className);
	}
}
