<?php
/**
 * Extension CrudUnit was created by Evan Frohlich evan.frohlich@controlgroup.com
 */

interface iCrudBase
{
  public function testGetRecord();
  public function testDelete();
  public function testCreate();
  public function testupdate();
  public function testRequiredAttr();
  public function getModel($fixture, $fixtureKey);
  public function getFixture();
  public function deleteItem($fixture, $fixtureKey);
  public function checkSaveOkay($fixture, $fixtureKey, $newID);
  public function getFixtureKey($id=1);
}
