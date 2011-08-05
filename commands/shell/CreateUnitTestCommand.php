<?php
class CreateUnitTestCommand extends CConsoleCommand
{
  public function getHelp()
	{
		return <<<EOD
USAGE
  createunittest

DESCRIPTION
  This command generates unit tests for CRUD operations on a given model class

EOD;
	}

	
	/**
	 * Execute the action.
	 * @param array command line parameters specific for this command
	 */
  public function run($args)
  {
    echo "Please enter the name of the Model you want to test: ";
    while(!isset($modelName))
    {
      $modelName = trim(fgets(STDIN));
      if(!$modelName)
        echo "Model name can not be NULL";
      
    }
    echo "Please enter the name of your fixture file without the .php extension: ";
    while(!isset($fixtureName))
    {
      $fixtureName = trim(fgets(STDIN));
      if(!$fixtureName)
        echo "fixture name can not be NULL";
    }
    
    echo "Please enter the fixture key prefix used in your fixture file. \nExample `user_` as in ('user_1'=>array(...), user_2=>array(...), ...).\nThe default is `ModelName_`: ";
    $fixtureKeyPrefix = trim(fgets(STDIN));
    if($fixtureKeyPrefix == "")
      $fixtureKeyPrefix = $modelName . '_';

    echo "Are there attributes of '$modelName' that are contained in fixture '$fixtureName' modified by beforeSave or other action(s)? [Yes|No]: ";
    if(!strncasecmp(trim(fgets(STDIN)),'n',1))
      $check_data_consistency_after_save = 'true';
    else
      $check_data_consistency_after_save = 'false';
        
    $unitCodePath = Yii::getPathOfAlias('application.tests.unit');
    $unitCodeFileName = $modelName . 'UnitTest.php';
    if(!file_exists("$unitCodePath/$unitCodeFileName"))
    {
      file_put_contents("$unitCodePath/$unitCodeFileName", $this->renderUnitCode($modelName, $fixtureName, $fixtureKeyPrefix, $check_data_consistency_after_save));
      echo "Unit test created @ $unitCodePath/$unitCodeFileName";
    }
    else
      echo "A unit test for this model already exists, no test created.";
  }

  private function renderUnitCode($modelName, $fixtureName, $fixtureKeyPrefix, $check_data_consistency_after_save)
  {
    return '<?php
      /**
       * Created by Evan Frohlich evan.frohlich@controlgroup.com
       */ 
      Class  ' . ucfirst($modelName) . 'UnitTest extends CrudBase
      { 
        public $fixtures = array(' . "'" . $fixtureName . "'" . '=>' . "'" . $modelName . "'" .  ');

        public $modelName = '  . "'" .  $modelName  . "'" . ';         //Reffers to your Model name
        public $fixtureRef = '  . "'" . $fixtureName  . "'" . ';       //Refers to the name of your fixture file
        public $fixtureKeyPrefix = ' . "'" . $fixtureKeyPrefix . "'" . '; //How your fixture items are keyed Sample_1, Sample_2, Etc...
        public $check_data_consistency_after_save = ' . $check_data_consistency_after_save . ';

        public function testGetRecord()
        {
          parent::testGetRecord();
        }
  
        public function testDelete()
        {
          parent::testDelete();
        }

        public function testCreate()
        {
          parent::testCreate();
        }

        public function testUpdate()
        {
          parent::testUpdate();
        }

        public function testRequiredAttr()
        {
          parent::testRequiredAttr();
        }

    }
  ';
  }
}
?>