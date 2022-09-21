<?php
$this->setViewParam('pageTitle', 'Mượn sách');
$this->registerJsFile("/assets/module/digitalLib/js/digitalLibDocumentBorrowList.js");
?>
<div id="borrowBox" class="portlet light" v-cloak>
  <div class="portlet-title">
    <div class="caption font-green-sharp">
      <i class="fa fa-user font-green-sharp"></i>
      <span class="caption-subject">QUẢN LÝ MƯỢN SÁCH</span>
    </div>
    <div class="actions"> 
      <div class="text-right tool-datagrid" v-if="checkedIds.length" >
        <a class="btn btn-sm btn-default btn-grid btn-grid-active-active" title="Huy duyet trang thai cua nguoi yeu cau" @click="changeBorrowStatus('1')"><i v-bind:class="'icon-lock-open'"></i> Chờ duyệt</a>&nbsp;
        <a class="btn btn-sm btn-success btn-grid btn-grid-active-active" title="Duyet trang thai cua nguoi yeu cau" v-on:click="changeBorrowStatus('2')"><i v-bind:class="'icon-lock-open'"></i> Duyệt</a>&nbsp;
        <a class="btn btn-danger btn-grid btn-grid-deletes" title="Xac nhan nguoi yeu cau da tra" v-on:click="changeBorrowStatus('3')" style="height: 28px; padding-top: 2pt;">Đã trả</a>
        <a class="btn btn-danger btn-grid btn-grid-deletes" title="Tu choi yeu cau muon" v-on:click="changeBorrowStatus('4')" style="height: 28px; padding-top: 2pt;">Từ chối yêu cầu</a>
      </div>
      
      <div class="btn-group" v-else>
        <a class="btn green-haze btn-outline btn-sm" href="javascript:;" aria-expanded="true" v-on:click="showBorrowForm()"> <i class="fa fa-plus"></i> Thêm mới
        </a>
      </div>
    </div>
  </div>
  <div class="portlet-body">
    <div class="row">
      <div class="form-group col-md-3">
        <label class="control-label text-bold">Tìm kiếm nguời mượn sách</label>
        <input class="form-control" @keyup.enter="getBorrowList(1)" placeholder="Nhập tên người yêu cầu và enter" name="key" v-model="mFilter.key_search" type="text">
      </div>
      <div class="form-group col-md-2">
        <label class="control-label text-bold">Trạng thái</label>
        <treeselect v-model="mFilter.status" id="status" @input="getBorrowList(1)" :options="statusList" placeholder="[ Lựa chọn ]" no-results-text="Không có kết quả nào phù hợp!" />
      </div>
      <div class="col-md-1 form-group">
        <label class="control-label text-bold">Hủy lọc</label>
        <div class="input-group add-on" v-on:click="getBorrowList(1,1)">
          <div title="xóa bộ lọc - refresh dữ liệu" class="input-group-btn"><button type="button" class="btn btn-default" style="border: 1px solid rgba(128, 144, 158, 0.36); border-radius: 4px;"><i class="fa fa-refresh"></i></button></div>
        </div>
      </div>
    </div>
    <div>
      <table class="table table-bordered table-striped table-condensed flip-content" id="datatable_ajax_store">
        <thead>
          <th width="2.4%" class="text-center" >
            <label class="mt-checkbox mt-checkbox-single mt-checkbox-outline"><input name="btSelectAll" @click="clickSelectedAll" v-model="checkAll" type="checkbox"><span></span></label>
          </th>
          <!-- <th></th> -->
          <th width="3%" class="text-center">Stt</th>
          <th width="15%" class="text-center">Người yêu cầu</th>
          <th width="20%"class="text-center">Tên tài liệu</th>
          <th width="15%"class="text-center">Ngày yêu cầu</th>
          <th width="15%"class="text-center">Hạn trả</th>
          <th width="20%"class="text-center">Ngày trả</th>
          <th width="7%" class="text-center">Trạng thái</th>
          <th width="5%" class="text-center">Thao tác</th>
          
        </thead>
        <tbody>
          <tr v-if="loading">
            <td colspan="9" class="text-center">
              <div class="col-md-12">
                <div class="alert alert-warning margin-bottom-0"><i class="fa fa-spin fa-spinner"></i> Đang tải dữ liệu ...</div>
              </div>
            </td>
          </tr>
          <tr v-if="loaded && pager.rows.length === 0">
            <td colspan="12" class="text-center">
              <div class="col-md-12">
                <div class="alert alert-warning margin-bottom-0">Không tìm thấy danh sách mượn sách nào</div>
              </div>
            </td>
          </tr>
          <tr v-if="!loading && loaded" v-for="(row,index) in pager.rows">
            <td class="text-center">
              <label class="mt-checkbox mt-checkbox-single mt-checkbox-outline">
                <input v-if ="row.status != 4 && row.status !=3" type="checkbox" v-model="checkedIds" :value="row.id" @click="clickSelected">
                <span></span>
              </label>
            </td>
            <td class="text-center">{{index+1}}</td>
            <td>
              {{row.created_user}}
            </td>
            <td>
              {{row.document_id}}
            </td>
            <td>
              {{row.created_date}}
            </td>
            <td v-if = "row.status == 2 || row.status == 3">
            {{row.expired_date}}
            </td>
            <td v-else>
            
            </td>
            <td>
              <div v-if = "row.status == 3">
            {{row.back_date}}<br>   
            
            <div v-if = "row.back_date > row.expired_date">      
            <p> <strong style="color: red;" > QUÁ HẠN</strong></p>
            </div>  
            </div>
            </td>
            <td>
              <span class="btn-inline-hover btn btn-xs" :class="row.status == 2 ? 'btn-success' : row.status == 1 ? 'btn-default' : row.status == 3? 'btn-danger' : 'btn-danger'" style="width: 120px;" v-on:click="changeBorrowStatus(row.status == 1? '2' : row.status == 2? '3': row.status == 4 ? '4' : '3', row)">{{row.status == 2 ? 'Đã duyệt' : row.status == 1 ? 'Chờ duyệt' : row.status == 3 ? 'Đã trả' : 'Đã từ chối yêu cầu'}}</span>
            </td>
            <td class="text-center">
              <button v-on:click="showBorrowForm(row)" class="btn btn-xs btn-info">
                <i class='fa fa-pencil-square-o' aria-hidden='true'></i>
              </button>
            </td>
          </tr>
        </tbody>
      </table>
      <div class="text-center" v-if="pager.total_page > 1 && !loading">
        <ul class="pagination">
          <li class="page-item" :class="{disabled:pager.current_page <= 1}">
            <a class="page-link" v-if="pager.current_page <=1">Trang trước</a>
            <a class="page-link" v-if="pager.current_page > 1" v-on:click="getBorrowList(pager.current_page-1)">Trang trước</a>
          </li>
          <li v-for="i in pager.total_page" v-if="i >= (pager.current_page-3) && i <= (pager.current_page+3)" v-bind:class="{ active: i == pager.current_page }"><a v-on:click="getBorrowList(i)">{{i}}</a></li>
          <li class="page-item" :class="{disabled:pager.current_page >= pager.total_page}">
            <a v-if="pager.current_page >= pager.total_page" class="page-link">Trang sau</a>
            <a href="#" class="page-link" v-if="pager.current_page < pager.total_page" v-on:click="getBorrowList(pager.current_page+1)">Trang sau</a>
          </li>
        </ul>
      </div>
    </div>
  </div>
</div>
<?php include("tpl/_digitalLibDocumentBorrowForm.php"); ?>
