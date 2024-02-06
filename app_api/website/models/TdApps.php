<?php

/**
 * This is the model class for table "td_apps".
 *
 * The followings are the available columns in table 'td_apps':
 * @property integer $app_id
 * @property string $app_key
 * @property string $app_name
 * @property integer $limit_days
 * @property integer $user_num
 */
class TdApps extends CActiveRecord
{
	/**
	 * @return string the associated database table name
	 */
	public function tableName()
	{
		return 'td_apps';
	}

	/**
	 * @return array validation rules for model attributes.
	 */
	public function rules()
	{
		// NOTE: you should only define rules for those attributes that
		// will receive user inputs.
		return array(
			array('app_key, app_name, limit_days', 'required'),
			array('limit_days, user_num', 'numerical', 'integerOnly'=>true),
			array('app_key, app_name', 'length', 'max'=>32),
			// The following rule is used by search().
			// @todo Please remove those attributes that should not be searched.
			array('app_id, app_key, app_name, limit_days, user_num', 'safe', 'on'=>'search'),
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
			'app_id' => '应用id',
			'app_key' => '应用标识',
			'app_name' => '应用名称',
			'limit_days' => '允许登录的天数',
			'user_num' => '使用该软件的用户数',
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

		$criteria->compare('app_id',$this->app_id);
		$criteria->compare('app_key',$this->app_key,true);
		$criteria->compare('app_name',$this->app_name,true);
		$criteria->compare('limit_days',$this->limit_days);
		$criteria->compare('user_num',$this->user_num);

		return new CActiveDataProvider($this, array(
			'criteria'=>$criteria,
		));
	}

	/**
	 * Returns the static model of the specified AR class.
	 * Please note that you should have this exact method in all your CActiveRecord descendants!
	 * @param string $className active record class name.
	 * @return TdApps the static model class
	 */
	public static function model($className=__CLASS__)
	{
		return parent::model($className);
	}


	/**
	 * 获取软件信息
	 * @param string $condition
	 * @param string $field
	 * @return array|mixed|null
	 */
	public function getAppInfo($condition = "", $field = "*"){
		$criteria = array(
			'select' => $field,
			'condition' => $condition,
		);
		return $this->find($criteria);
	}
}
