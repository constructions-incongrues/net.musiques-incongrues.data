<?php

/**
 * Link filter form base class.
 *
 * @package    vanilla-miner
 * @subpackage filter
 * @author     Constructions Incongrues
 * @version    SVN: $Id: sfDoctrineFormFilterGeneratedTemplate.php 24171 2009-11-19 16:37:50Z Kris.Wallsmith $
 */
abstract class BaseLinkFormFilter extends BaseFormFilterDoctrine
{
  public function setup()
  {
    $this->setWidgets(array(
      'url'              => new sfWidgetFormFilterInput(array('with_empty' => false)),
      'domain_parent'    => new sfWidgetFormFilterInput(array('with_empty' => false)),
      'domain_fqdn'      => new sfWidgetFormFilterInput(array('with_empty' => false)),
      'mime_type'        => new sfWidgetFormFilterInput(),
      'contributed_at'   => new sfWidgetFormFilterInput(),
      'contributor_id'   => new sfWidgetFormFilterInput(),
      'contributor_name' => new sfWidgetFormFilterInput(),
      'comment_id'       => new sfWidgetFormFilterInput(),
      'discussion_id'    => new sfWidgetFormFilterInput(),
      'discussion_name'  => new sfWidgetFormFilterInput(),
      'availability'     => new sfWidgetFormFilterInput(),
      'expanded_at'      => new sfWidgetFormFilterDate(array('from_date' => new sfWidgetFormDate(), 'to_date' => new sfWidgetFormDate())),
      'created_at'       => new sfWidgetFormFilterDate(array('from_date' => new sfWidgetFormDate(), 'to_date' => new sfWidgetFormDate(), 'with_empty' => false)),
      'updated_at'       => new sfWidgetFormFilterDate(array('from_date' => new sfWidgetFormDate(), 'to_date' => new sfWidgetFormDate(), 'with_empty' => false)),
    ));

    $this->setValidators(array(
      'url'              => new sfValidatorPass(array('required' => false)),
      'domain_parent'    => new sfValidatorPass(array('required' => false)),
      'domain_fqdn'      => new sfValidatorPass(array('required' => false)),
      'mime_type'        => new sfValidatorPass(array('required' => false)),
      'contributed_at'   => new sfValidatorPass(array('required' => false)),
      'contributor_id'   => new sfValidatorSchemaFilter('text', new sfValidatorInteger(array('required' => false))),
      'contributor_name' => new sfValidatorPass(array('required' => false)),
      'comment_id'       => new sfValidatorSchemaFilter('text', new sfValidatorInteger(array('required' => false))),
      'discussion_id'    => new sfValidatorSchemaFilter('text', new sfValidatorInteger(array('required' => false))),
      'discussion_name'  => new sfValidatorPass(array('required' => false)),
      'availability'     => new sfValidatorPass(array('required' => false)),
      'expanded_at'      => new sfValidatorDateRange(array('required' => false, 'from_date' => new sfValidatorDateTime(array('required' => false, 'datetime_output' => 'Y-m-d 00:00:00')), 'to_date' => new sfValidatorDateTime(array('required' => false, 'datetime_output' => 'Y-m-d 23:59:59')))),
      'created_at'       => new sfValidatorDateRange(array('required' => false, 'from_date' => new sfValidatorDateTime(array('required' => false, 'datetime_output' => 'Y-m-d 00:00:00')), 'to_date' => new sfValidatorDateTime(array('required' => false, 'datetime_output' => 'Y-m-d 23:59:59')))),
      'updated_at'       => new sfValidatorDateRange(array('required' => false, 'from_date' => new sfValidatorDateTime(array('required' => false, 'datetime_output' => 'Y-m-d 00:00:00')), 'to_date' => new sfValidatorDateTime(array('required' => false, 'datetime_output' => 'Y-m-d 23:59:59')))),
    ));

    $this->widgetSchema->setNameFormat('link_filters[%s]');

    $this->errorSchema = new sfValidatorErrorSchema($this->validatorSchema);

    $this->setupInheritance();

    parent::setup();
  }

  public function getModelName()
  {
    return 'Link';
  }

  public function getFields()
  {
    return array(
      'id'               => 'Number',
      'url'              => 'Text',
      'domain_parent'    => 'Text',
      'domain_fqdn'      => 'Text',
      'mime_type'        => 'Text',
      'contributed_at'   => 'Text',
      'contributor_id'   => 'Number',
      'contributor_name' => 'Text',
      'comment_id'       => 'Number',
      'discussion_id'    => 'Number',
      'discussion_name'  => 'Text',
      'availability'     => 'Text',
      'expanded_at'      => 'Date',
      'created_at'       => 'Date',
      'updated_at'       => 'Date',
    );
  }
}
