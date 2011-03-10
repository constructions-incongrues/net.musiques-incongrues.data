<?php

/**
 * ExtractionLog form base class.
 *
 * @method ExtractionLog getObject() Returns the current form's model object
 *
 * @package    vanilla-miner
 * @subpackage form
 * @author     Constructions Incongrues
 * @version    SVN: $Id: sfDoctrineFormGeneratedTemplate.php 24171 2009-11-19 16:37:50Z Kris.Wallsmith $
 */
abstract class BaseExtractionLogForm extends BaseFormDoctrine
{
  public function setup()
  {
    $this->setWidgets(array(
      'id'                => new sfWidgetFormInputHidden(),
      'extraction_driver' => new sfWidgetFormTextarea(),
      'started_on'        => new sfWidgetFormDateTime(),
      'finished_on'       => new sfWidgetFormDateTime(),
      'resources_parsed'  => new sfWidgetFormInputText(),
      'urls_extracted'    => new sfWidgetFormInputText(),
      'created_at'        => new sfWidgetFormDateTime(),
      'updated_at'        => new sfWidgetFormDateTime(),
    ));

    $this->setValidators(array(
      'id'                => new sfValidatorDoctrineChoice(array('model' => $this->getModelName(), 'column' => 'id', 'required' => false)),
      'extraction_driver' => new sfValidatorString(array('required' => false)),
      'started_on'        => new sfValidatorDateTime(array('required' => false)),
      'finished_on'       => new sfValidatorDateTime(array('required' => false)),
      'resources_parsed'  => new sfValidatorPass(array('required' => false)),
      'urls_extracted'    => new sfValidatorPass(array('required' => false)),
      'created_at'        => new sfValidatorDateTime(),
      'updated_at'        => new sfValidatorDateTime(),
    ));

    $this->widgetSchema->setNameFormat('extraction_log[%s]');

    $this->errorSchema = new sfValidatorErrorSchema($this->validatorSchema);

    $this->setupInheritance();

    parent::setup();
  }

  public function getModelName()
  {
    return 'ExtractionLog';
  }

}
