<?php

/**
 * Link form base class.
 *
 * @method Link getObject() Returns the current form's model object
 *
 * @package    vanilla-miner
 * @subpackage form
 * @author     Constructions Incongrues
 * @version    SVN: $Id: sfDoctrineFormGeneratedTemplate.php 24171 2009-11-19 16:37:50Z Kris.Wallsmith $
 */
abstract class BaseLinkForm extends BaseFormDoctrine
{
  public function setup()
  {
    $this->setWidgets(array(
      'id'               => new sfWidgetFormInputHidden(),
      'url'              => new sfWidgetFormTextarea(),
      'domain_parent'    => new sfWidgetFormTextarea(),
      'domain_fqdn'      => new sfWidgetFormTextarea(),
      'mime_type'        => new sfWidgetFormTextarea(),
      'contributed_at'   => new sfWidgetFormTextarea(),
      'contributor_id'   => new sfWidgetFormInputText(),
      'contributor_name' => new sfWidgetFormTextarea(),
      'comment_id'       => new sfWidgetFormInputText(),
      'discussion_id'    => new sfWidgetFormInputText(),
      'discussion_name'  => new sfWidgetFormTextarea(),
      'availability'     => new sfWidgetFormTextarea(),
      'expanded_at'      => new sfWidgetFormDateTime(),
      'created_at'       => new sfWidgetFormDateTime(),
      'updated_at'       => new sfWidgetFormDateTime(),
    ));

    $this->setValidators(array(
      'id'               => new sfValidatorDoctrineChoice(array('model' => $this->getModelName(), 'column' => 'id', 'required' => false)),
      'url'              => new sfValidatorString(),
      'domain_parent'    => new sfValidatorString(),
      'domain_fqdn'      => new sfValidatorString(),
      'mime_type'        => new sfValidatorString(array('required' => false)),
      'contributed_at'   => new sfValidatorString(array('required' => false)),
      'contributor_id'   => new sfValidatorInteger(array('required' => false)),
      'contributor_name' => new sfValidatorString(array('required' => false)),
      'comment_id'       => new sfValidatorInteger(array('required' => false)),
      'discussion_id'    => new sfValidatorInteger(array('required' => false)),
      'discussion_name'  => new sfValidatorString(array('required' => false)),
      'availability'     => new sfValidatorString(array('required' => false)),
      'expanded_at'      => new sfValidatorDateTime(array('required' => false)),
      'created_at'       => new sfValidatorDateTime(),
      'updated_at'       => new sfValidatorDateTime(),
    ));

    $this->validatorSchema->setPostValidator(
      new sfValidatorDoctrineUnique(array('model' => 'Link', 'column' => array('url')))
    );

    $this->widgetSchema->setNameFormat('link[%s]');

    $this->errorSchema = new sfValidatorErrorSchema($this->validatorSchema);

    $this->setupInheritance();

    parent::setup();
  }

  public function getModelName()
  {
    return 'Link';
  }

}
