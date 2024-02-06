<?php

/**
 * This is the model class for table "td_app_users".
 *
 * The followings are the available columns in table 'td_app_users':
 * @property integer $au_id
 * @property integer $app_id
 * @property integer $member_id
 * @property string $open_id
 * @property integer $login_time
 * @property integer $invalid_time
 */
class TdAppUsers extends CActiveRecord
{
	/**
	 * @return string the associated database table name
	 */
	public function tableName()
	{
		return 'td_app_users';
	}

	/**
	 * @return array validation rules for model attributes.
	 */
	public function rules()
	{
		// NOTE: you should only define rules for those attributes that
		// will receive user inputs.
		return array(
			array('app_id, member_id, open_id, login_time, invalid_time', 'required'),
			array('app_id, member_id, login_time, invalid_time', 'numerical', 'integerOnly'=>true),
			array('open_id', 'length', 'max'=>32),
			// The following rule is used by search().
			// @todo Please remove those attributes that should not be searched.
			array('au_id, app_id, member_id, open_id, login_time, invalid_time', 'safe', 'on'=>'search'),
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
			'au_id' => '主键id',
			'app_id' => '软件id',
			'member_id' => '用户id',
			'open_id' => '登录标识',
			'login_time' => '登录时间',
			'invalid_time' => '登录失效时间',
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

		$criteria->compare('au_id',$this->au_id);
		$criteria->compare('app_id',$this->app_id);
		$criteria->compare('member_id',$this->member_id);
		$criteria->compare('open_id',$this->open_id,true);
		$criteria->compare('login_time',$this->login_time);
		$criteria->compare('invalid_time',$this->invalid_time);

		return new CActiveDataProvider($this, array(
			'criteria'=>$criteria,
		));
	}

	/**
	 * Returns the static model of the specified AR class.
	 * Please note that you should have this exact method in all your CActiveRecord descendants!
	 * @param string $className active record class name.
	 * @return TdAppUsers the static model class
	 */
	public static function model($className=__CLASS__)
	{
		return parent::model($className);
	}

	
	/**
	 * 获取用户应用信息
	 * @param string $condition
	 * @param string $field
	 * @return array|mixed|null
	 */
	public function getAppUserInfo($condition = "", $field = "*"){
		$criteria = array(
			'select' => $field,
			'condition' => $condition,
		);
		return $this->find($criteria);
	}
}
