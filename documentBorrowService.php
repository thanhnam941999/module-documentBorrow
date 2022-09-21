<?php

/**
 * author: NguyenThanhNam
 */

namespace module\digitalLib\service;

use module\digitalLib\model\documentBorrowModel;

class documentBorrowService extends \lib\core\BaseService
{
    protected $dModel;
    public function __construct()
    {
        parent::__construct();
        $this->dModel = new documentBorrowModel();
    }

    /**
     * get list documentBorrow by site
     */
    public function getBorrowList()
    {
        $page = self::getIntRequest('page');
        $filters = self::getArrRequest('filters');
        $data = $this->dModel->getBorrowList($filters, $page);
        $this->success('Thành công lấy ra danh sách tài liệu mượn', $data);
    }

    /**
     * save documentBorrow
     */
    public function saveBorrow()
	{
		$formData = self::getArrRequest('formData');
		if (empty($formData)) {
			$this->exception("Dữ liệu trống");
		}

		if (empty(trim($formData["created_user"]))) {
			$this->error("Tên thể loại không được bỏ trống");
		}

		if (!$formData['id']) {
			// tạo bản ghi nháp của genre
			$id = $this->geModel->createNewBorrow($formData);
			$this->success('Lưu cấu hình thành công', ['id' => $id]);
		} else {
			// cập nhật dữ liệu nếu có
			$id = $this->geModel->updateBorrowById($formData);
			$this->success('Cập nhật cấu hình thành công', ['id' => $id]);
		}
	}

	/**
	 * get detail borrow
	 */
	public function getBorrowDetailById()
	{
		$id = self::getStrRequest('borrowId');
		$data = $this->dModel->getBorrowDetailById($id);
		$this->success('Thành công', ['row' => $data]);
	}
}