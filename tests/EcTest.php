<?php
/* 
 * Basic very incomplete testing for Ec.
 */
class EcTest extends PHPUnit_Framework_TestCase
{

  /**
   * @expectedException ErrorException
   * @expectedExceptionCode E_WARNING
   */
  public function testWarning() {
    fopen("PretySureThisFileDNE", "r");
  }

  /**
   * @expectedException ErrorException
   * @expectedExceptionCode E_USER_WARNING
   * @expectedExceptionMessage This is a warning
   */
  public function testUserWarning() {
    trigger_error("This is a warning", E_USER_WARNING);
  }
  
  /**
   * @expectedException PHPUnit_Framework_Error_Notice
   */
  public function testUserNotice() {
    $this->markTestSkipped("PHPUnit breaks this.");
    trigger_error("This is a notice", E_USER_NOTICE);
  }
}
