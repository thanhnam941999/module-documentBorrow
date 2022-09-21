Vue.component('treeselect', VueTreeselect.Treeselect);
var documentBorrowApp = new Vue({
  el: "#borrowBox",
  data: {
    pager: {
      current_page: 1,
      total_page: 0,
      per_page: 20,
      offset: 0,
      rows: []
    },
    mFilter: {
      key_search: "",
      status: null
    },
    statusList: [{
      
      id: 1,
      label: 'Chờ duyệt',
      label_cf: '<strong>Chờ duyệt</strong>'
        
      },
      {
        id: 2,
        label: 'Đã duyệt',
        label_cf: '<strong>Duyệt</strong>'
      },
      {
        id: 3,
        label: 'Đã trả',
        label_cf: '<strong>Đã trả</strong>'
      },
      {
        id: 4,
        label: 'Đã từ chối yêu cầu',
        label_cf: '<strong>Đã từ chối yêu cầu</strong>'
      }

    ],
    loading: true,
    loaded: false,
    checkAll: false,
    checkedIds: [],
    AppCfg: AppCfg,
  },
  created: function () {
    var me = this; 
    me.getBorrowList();
  },
  methods: {
    moment: function (date, format) {
      return moment(date, format);
    },
    getBorrowList: function (page = 1, reset = 2) {
      var me = this; 
      me.pager.page = page
      if (reset == 1) {
        me.mFilter = {
          key_search: "",
          status: null
        }
        me.pager.page = 1
      }
      me.loading = true;
      me.loaded = false;
      $.post(`/module/digitalLib/service/documentBorrow/getBorrowList?page=${me.pager.page}`, {
        filters: me.mFilter
      }, function (res) {
        if (res.success) {
          me.pager = res.data
        }
        me.loading = false;
        me.loaded = true;
      })
    },
    clickSelectedAll: function () {
      var me = this;
      me.checkAll = !me.checkAll
      if (me.checkAll) {
        me.checkedIds = me.pager.rows.map(item => item.id)
      } else {
        me.checkedIds = []
      }
    },
    clickSelected: function () {
      var me = this;
      me.$nextTick(function () {
        _.isEqual(_.sortBy(me.checkedIds))
      })
    },
    changeBorrowStatus: function (status = null, row = null) {
      var me = this
      let dataPost = {
        ids: me.checkedIds,
        status: status,
        ref_table: 'document_has_user_borrowing'
      }
      if (row) {
        dataPost.ids = [row.id];
        if (row.status == 1) { 

          dataPost.status == 1; 
        }
      }
      if (row) {
        dataPost.ids = [row.id];
        if (row.status == 2) { 
          dataPost.status == 3; 
        }
      }
      if (row) {
        if (row.status == 4){
        dataPost.ids = null;
        // if (row.status == 3) { 
        //   App.notification("Thông báo", res.data.msg);
          dataPost.status == null; 
        }
      }
      if (row) {
        dataPost.ids = [row.id];
        if (row.status == 4) { 
          dataPost.status == 4; 
        }
      }
      
      if (dataPost.ids.length === 0 ) {
        return;
      }
      let selectedStatus = (me.statusList.filter(item => item.id == status))[0]; 
      
      App.showConfirm(`Bạn có chắc chắn sẽ thay đổi trạng thái thành ${selectedStatus.label_cf} tại các dữ liệu đã được chọn?`, function () {
        axios.post(`/module/digitalLib/service/common/changeStatusDigitalLibTableByIds`, dataPost).then(res => {
          if (res.status == 200 && res.data.success) {
            me.checkedIds = [];
            me.checkAll = false;
            me.getBorrowList(1)
            App.notification("Thông báo", res.data.msg);
     
          } else {
            App.showMessageWarning(res.data.msg);
          }
        }).catch(function (err) {
          console.log('--> changeBorrowStatus catch:', error)

        });
      })
    }
    ,
    showBorrowForm: function(row = null) {
      let me  = this
      me.checkedIds = [];
      borrowFormApp.showBorrowForm(row);
    }
  }
})

var borrowFormApp = new Vue({
  el: "#borrowFormBox",
  data: {
    borrow: {
      id: "",
      parent_id: "",
      user_id: "",
      content: "",
      admin_note: "",
      site_id: "",
      donvi_id: "",
      created_date: "",
      updated_date: "",
      created_user: "",
      status: 1
    },
    formElm: '',
    loading: false,
  },
  created: function () {
    var me = this;

    this.$nextTick(function () {
      window.addEventListener('message', function (e) {
        if (e.data.type == 'cdn_uploaded') {
          me.borrow.file_id = e.data.value;
        }
      });
    });
  },
  methods: {
    showBorrowForm: function (row = null) {
      var me = this
      if (!row) {
        me.resetForm();
      } else {
        me.borrow = row
        me.getBorrowById();
      }
      me.formElm = $('#borrowFormBox');
      me.formElm.modal('show');
    },
    getBorrowById: function () {
      var me = this;
      $.post("/module/digitalLib/service/documentBorrow/getBorrowDetailById", {
        borrowId: me.borrow.id
      }, function (res) {
        if (res.success) {
          me.borrow = res.data.row
          CKEDITOR.instances['description'].setData(me.borrow.description);
        } else {
          App.showMessageWarning(res.msg);
        }
      });
    },
    saveBorrow: function () {
      var me = this;
      me.loading = true;
      let msgCtr = me.borrow.id ? '<strong> CẬP NHẬT </strong>':'<strong> TẠO MỚI </strong>';
      App.showConfirm(`Bạn có chắc chắn sẽ ${msgCtr} thể  loại này?`, function () {
        $.post("/module/digitalLib/service/documentBorrow/saveBorrow", {
          formData: me.genre
        }, function (res) {
          if (res.success) {
            me.formElm.modal('hide');
            me.loading = false;
            borrowApp.getBorrowList(1);
            if (res.message === 'Lưu cấu hình thành công') {
              App.notification("Thông báo", $.getContent("Bạn đã lưu thành công thể loại !", res));
            } else {
              App.notification("Thông báo", $.getContent("Bạn đã cập nhật thành công thể loại !", res));
            }
          } else {
            App.showMessageWarning(res.msg);
            me.loading = false;
          }
        })
      });
    },
    resetForm: function () {
      var me = this;
      me.borrow = {
        id: "",
        name: "",
        address: "",
        job: "",
        pseudonym: "",
        relative: "",
        description: "",
        birthday: "",
        date_of_death: "",
        cover_id: "",
        status: 2
      }
      
    },
    http_build_query: function (obj) {
      var str = "";
      for (var key in obj) {
        if (str != "") {
          str += "&";
        }
        str += key + "=" + encodeURIComponent(obj[key]);
      }
      return str;
    },
  },
  mounted: function(){
    setTimeout(function(){
      $('#borrowFormBox .datetimepicker').datetimepicker({format: 'DD/MM/YYYY'});
    }, 1000);
  }
})