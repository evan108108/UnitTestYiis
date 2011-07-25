<?php
/**
 * Extension CrudUnit was created by Evan Frohlich evan.frohlich@controlgroup.com
 */
Class CrudBase extends CDbTestCase implements iCrudBase
{ 
	//public $controller; 
  public $fixtures = array();

  	
  public function testGetRecord()
  {
    $fixture = $this->getFixture();
    $results = $this->getModel($fixture, 1);
    foreach($fixture[($this->getFixtureKey(1))] as $attr=>$value)
      $this->assertTrue($results->$attr == $value);
  }

  public function testDelete()
  {
    $fixture = $this->getFixture();
    $this->assertTrue($this->deleteItem($fixture, 1));
    $model = $this->getModel($fixture, 1);
    $this->assertNull($model);
  }

  public function testCreate()
  {
    $fixture = $this->getFixture();
    $this->assertTrue($this->deleteItem($fixture, 1));
    $model = $this->getModel($fixture, 1);
    $this->assertNull($model);
    $model = new $this->modelName;
    foreach($fixture[($this->getFixtureKey(1))] as $attr=>$value)
      $model->$attr = $value;
    $this->assertTrue($model->save());
    $pk = $model->tableSchema->primaryKey;
    $this->checkSaveOkay($fixture, 1, $model->$pk);
  }

  public function testUpdate()
  {
    $fixture = $this->getFixture();
    $model = $this->getModel($fixture, 1);
    $this->assertTrue($this->deleteItem($fixture, 2));
     foreach($fixture[($this->getFixtureKey(2))] as $attr=>$value)
      $model->$attr = $value;
     $this->assertTrue($model->save());
     $pk = $model->tableSchema->primaryKey;
     $this->checkSaveOkay($fixture, 2, $model->$pk);
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
      $this->assertFalse($model->save());
      $model->$atr = $orgValue;
    }
  }

  public function getModel($fixture, $fixtureKey=1)
  {
    $model = new $this->modelName;
    return $model->findByPk($fixture[($this->getFixtureKey($fixtureKey))][$model->tableSchema->primaryKey]);
  }
  
  public function getFixture()
  {
    $fixname = $this->fixtureRef;
    return $this->$fixname;
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
    $model = new $this->modelName;
    $model = $model->findByPk($newID);
    foreach($fixture[($this->getFixtureKey($fixtureKey))] as $attr=>$value)
       $this->assertTrue($model->$attr == $value);
  }

  public function getFixtureKey($id=1)
  {
    if(!isset($this->fixtureKeyPrefix))
      return $this->fixtureKeyPrefix = $this->modelName . "_" . $id;
    return $this->fixtureKeyPrefix . $id;
  }
}
