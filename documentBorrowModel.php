<?php

namespace module\digitalLib\model;

use lib\core\BaseModel;

class documentBorrowModel extends BaseModel
{
  private $limit = 10;
  private $offset = 0;
  private $mTable = 'tvs_document_has_user_borrowing';
  public function __construct()
  {
    parent::__construct();
  }
  /**
   * content: create new a documentBorrow
   * author: NguyenThanhNam
   * ====================================
   * return string new_id
   */


    //chua lam den phan them moi nguoi muon tai lieu nen de lai ham nay
    public function createNewDocumentBorrow($data = [])
    {
    if (empty(trim($data["parent_id"]))) {
        self::error("Tên nguoi yeu cau không để trống");
      }
      // kiểm tra bản ghi nháp của 1 ngôn ngữ, nếu bản ghi nháp chưa tác động gì có thể sử dụng lại
      $dataSave = [
        'name' => $data['name'],
        'icon' => $data['icon'],
        'status' => $data['status'] && $data['status'] == 1 ? 1 : 2,
        'type' => $data['type'],
        'is_bilingual' => $data['is_bilingual'] == 1 ? 1 : 0,
        'description' => $data['description'],
      ];
      $rowId = self::DB()->insert($this->mTable, self::insertSetup($dataSave));
      if ($rowId && is_string($rowId)) {
        return $rowId;
      }
    }  
    /**
   * content: update a documentBorrow
   * author: NguyenThanhNam
   * ====================================
   * return string id
   */

  
  public function updateBorrow($dataSave = null)
  {
    $id = $dataSave['id'];
    $dataUpdate = [
      'id' => $id,
      'document_id' => $dataSave['document_id'],
      'user_id' => $dataSave['user_id'],
      'status' => $dataSave['status'] && $dataSave['status'] == 1 ? 1 : 2,
      'content' => $dataSave['content'],
      'admin_note' => $dataSave['admin_note'],
      'donvi_id' => $dataSave['donvi_id'],
      'site_id' => $dataSave['site_id'],
      'parent_id' => $dataSave['parent_id'],

    ];
    $dataUpdate['birthday'] = isset($dataSave['birthday']) ? date("Y/m/d", strtotime(str_replace('/', '-', $dataSave['birthday']))) : null;
    $dataUpdate['date_of_death'] = isset($dataSave['date_of_death']) ? date("Y/m/d", strtotime(str_replace('/', '-', $dataSave['date_of_death']))) : null;
    if (!$id || !$dataUpdate || empty($dataUpdate)) {
      return null;
    }

    self::DB()->update($this->mTable, self::updateSetup($dataUpdate), 'id=:id', ['id' => $id]);
    return $id;
  }

  /**
   * content: get records list documentBorrow by conditions
   * author: NguyenThanhNam
   * ====================================
   * @conds: array | conditions select where
   * return array data
   */
  public function getBorrowList($conds = [], $page = 1, $opts = [])
  {
    $res = [
      'current_page' => (int) $page,
      'per_page' => $this->limit,
      'total_page' => 0,
      'rows' => []
    ];
    $page = $page && $page > 0 ? $page : 1;

    $this->offset = ($page - 1) * $this->limit;
    $nameLike = isset($conds['key_search']) ? "%" . $conds['key_search'] . "%" : "%%";
    $sWhere = '';
    $sParams = [];

    if (!empty($conds['key_search'])) {
      $sWhere .= ' and au.parent_id LIKE :parent_id';
      $sParams['parent_id'] = $nameLike;
    }

    if (!empty($conds['status'])) {
      $sWhere .= ' AND au.status = :status';
      $sParams['status'] = $conds['status'];
    }

    // query data
    $res['rows'] = self::DB()->select('au.*')
      ->from($this->mTable, 'au')
      ->where($sWhere)
      ->limit($this->offset, $this->limit)
      ->getRows($sParams);

    if ($res['rows'] && !empty($res['rows'])) {
      $totalRows = self::DB()->select('count(au.id)')
        ->from($this->mTable, 'au')
        ->where($sWhere)
        ->getCount($sParams);
      $res['total_page'] = ceil($totalRows / $this->limit);
    }
    return $res;
  }


  /**
   * content: get record detail by conditions
   * author: NguyenThanhNam
   * ====================================
   * return int res
   */
  public function getBorrowDetailById($borrowId = null)
  {
    $res = null;
    $res = self::DB()->select('m.*')
      ->from($this->mTable, 'm')
      ->where('m.id=:id')
      ->getRow(['id' => $borrowId]);
    return $res;
  } 
}
