<?php

namespace module\digitalLib\page;

use module\digitalLib\model\documentBorrowModel;

class documentBorrowPage extends \lib\core\BasePage
{
  public $layout = 'layout/admin.html';
  protected $dModel;
  public function __construct()
  {
    parent::__construct();
    $this->dModel = new documentBorrowModel();
    \lib\auth\AccessControl::accessCustomEnpoint([SYSTEM_ADMIN, DONVI_ADMIN], 2);
  }

  public function list()
  {
    echo $this->render("digitalLibDocumentBorrowList.php", []);
  }  
}
