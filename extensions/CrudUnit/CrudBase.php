<?php
/**
 * Extension CrudUnit was created by Evan Frohlich evan.frohlich@controlgroup.com
 */
Class CrudBase extends CDbTestCase implements iCrudBase
{ 
	//public $controller; 
  public $fixtures = array();
  
  public $ignorAttr = array('crt_dtm'=>true, 'lud_dtm'=>true, 'id'=>true);
  
  public function setup()
  {
    parent::setup();
  }
  public function testModel()
  { 
  	$model = new $this->modelName;
  	$this->assertTrue($model instanceof $this->modelName);
  }
  
  public function testGetRecord()
  {
    $fixture = $this->getFixture();
    $results = $this->getModel($fixture, 1);
    foreach($fixture[($this->getFixtureKey(1))] as $attr=>$value)
      $this->assertTrue($results->$attr == $value, "Attribute $attr: " . $results->$attr . ' is not equal to ' . $value);
  }

  public function testDelete()
  {
    $fixture = $this->getFixture();
    $this->assertTrue($this->deleteItem($fixture, 1), "Item could not be deleted");
    $model = $this->getModel($fixture, 1);
    $this->assertNull($model, "Model " . CJSON::encode($model) . " is not Equal to null and thus could not be deleted");
  }

  public function testCreate()
  {
    $fixture = $this->getFixture();
    $this->assertTrue($this->deleteItem($fixture, 1), "Item was not deleted");
    $model = $this->getModel($fixture, 1);
    $this->assertNull($model, "Model " . CJSON::encode($model) . " is not Equal to null and thus could not be deleted");
    $model = new $this->modelName;
    foreach($fixture[($this->getFixtureKey(1))] as $attr=>$value)
      $model->$attr = $value;
    $this->assertTrue($model->save(), '$model->save() returned false and thus could not be saved. Data: ' .  CJSON::encode($model));
    
    $this->checkSaveOkay($fixture, 1, $this->getPkValues($model));
  }
  
  public function getPkValues($model)
  {
  	$pk = $model->tableSchema->primaryKey;
  	if(is_array($pk))
  	{
  		$PKValue = array();
  		foreach($pk as $attr=>$value)
  		{
  			$PKValue[$value] = $model->$value;
  		}
  		return $PKValue;
  	}
  	return $model->$pk;
  }

  public function testUpdate()
  {
    $fixture = $this->getFixture();
    $model = $this->getModel($fixture, 1);
    $this->assertTrue($this->deleteItem($fixture, 2), "Item was not deleted");
     foreach($fixture[($this->getFixtureKey(2))] as $attr=>$value)
       if ($attr != 'id') $model->$attr = $value;
     $this->assertTrue($model->save(), '$model->save() returned false and thus could not be saved. Data: ' .  CJSON::encode($model));
     //$pk = $model->tableSchema->primaryKey;
     
     $this->checkSaveOkay($fixture, 2, $this->getPkValues($model));
  }

  public function testRequiredAttr()
  {
    $fixture = $this->getFixture();
    $model = $this->getModel($fixture, 1);
    $requiredAtr = array();
    foreach($fixture[($this->getFixtureKey(1))] as $attr=>$value)
    {
      if($model->isAttributeRequired($attr))
        $requiredAtr[] = $attr;
    }
    for($i=0;$i<count($requiredAtr);$i++)
    {
      $atr = $requiredAtr[$i];
      $orgValue = $model->$atr;
      $model->$atr = '';
      $this->assertFalse($model->save(), "model allowed save when required field '$atr' was Empty");
      $model->$atr = $orgValue;
    }
  }

  public function getModel($fixture, $fixtureKey=1)
  {
    $model = new $this->modelName;
    $pk = $model->tableSchema->primaryKey;
  
    if(is_array($pk))
    {
    	$pkList = array();
    	foreach($pk as $value)
    	{
    		$pkList[$value] = $fixture[($this->getFixtureKey($fixtureKey))][$value];
    	}
    	 return $model->findByPk($pkList);
    }
   
    return $model->findByPk($fixture[($this->getFixtureKey($fixtureKey))][$pk]);
  }
  
  public function getFixture()
  {
   return $this->{$this->fixtureRef}; 
  }

  public function deleteItem($fixture, $fixtireKey)
  {
    $model = $this->getModel($fixture, $fixtireKey);

    if($model->delete())
      return true;
    else
      return false;
  }	

  public function checkSaveOkay($fixture, $fixtureKey, $newID)
  {
    if(!$this->check_data_consistency_after_save)
      return; 
    $model = new $this->modelName;
    $model = $model->findByPk($newID);
    foreach($fixture[($this->getFixtureKey($fixtureKey))] as $attr=>$value)
    {
    	if(!isset($this->ignorAttr[$attr]))
      	$this->assertTrue($model->$attr == $value, "Attribute $attr: " . $model->$attr . ' is not equal to ' . $value);
     }

  }
  
  public function getFixtureKey($id=1)
  {
    if(!isset($this->fixtureKeyPrefix))
      return $this->fixtureKeyPrefix = $this->modelName . "_" . $id;
    return $this->fixtureKeyPrefix . $id;
  }
  
}

