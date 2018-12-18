<?php

final class PholioImage extends PholioDAO
  implements
    PhabricatorPolicyInterface {

  protected $mockID;
  protected $filePHID;
  protected $name;
  protected $description;
  protected $sequence;
  protected $isObsolete;
  protected $replacesImagePHID = null;

  private $inlineComments = self::ATTACHABLE;
  private $file = self::ATTACHABLE;
  private $mock = self::ATTACHABLE;

  public static function initializeNewImage() {
    return id(new self())
      ->setName('')
      ->setDescription('')
      ->setIsObsolete(0);
  }

  protected function getConfiguration() {
    return array(
      self::CONFIG_AUX_PHID => true,
      self::CONFIG_COLUMN_SCHEMA => array(
        'mockID' => 'id?',
        'name' => 'text128',
        'description' => 'text',
        'sequence' => 'uint32',
        'isObsolete' => 'bool',
        'replacesImagePHID' => 'phid?',
      ),
      self::CONFIG_KEY_SCHEMA => array(
        'key_phid' => null,
        'keyPHID' => array(
          'columns' => array('phid'),
          'unique' => true,
        ),
        'mockID' => array(
          'columns' => array('mockID', 'isObsolete', 'sequence'),
        ),
      ),
    ) + parent::getConfiguration();
  }

  public function getPHIDType() {
    return PholioImagePHIDType::TYPECONST;
  }

  public function attachFile(PhabricatorFile $file) {
    $this->file = $file;
    return $this;
  }

  public function getFile() {
    $this->assertAttached($this->file);
    return $this->file;
  }

  public function attachMock(PholioMock $mock) {
    $this->mock = $mock;
    return $this;
  }

  public function getMock() {
    $this->assertAttached($this->mock);
    return $this->mock;
  }

  public function attachInlineComments(array $inline_comments) {
    assert_instances_of($inline_comments, 'PholioTransactionComment');
    $this->inlineComments = $inline_comments;
    return $this;
  }

  public function getInlineComments() {
    $this->assertAttached($this->inlineComments);
    return $this->inlineComments;
  }


/* -(  PhabricatorPolicyInterface Implementation  )-------------------------- */


  public function getCapabilities() {
    return $this->getMock()->getCapabilities();
  }

  public function getPolicy($capability) {
    return $this->getMock()->getPolicy($capability);
  }

  // really the *mock* controls who can see an image
  public function hasAutomaticCapability($capability, PhabricatorUser $viewer) {
    return $this->getMock()->hasAutomaticCapability($capability, $viewer);
  }

}
