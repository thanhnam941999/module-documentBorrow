<div id="borrowFormBox" class="modal" role="dialog" v-cloak>
	<div class="modal-dialog modal-lg" role="borrow">
		<div class="modal-content">
			<form role="form" action="">
				<div class="modal-header">
					<h5 class="modal-title" v-if="!borrow.id">Thêm nguoi muon sach moi</h5>
					<h5 class="modal-title" v-else>Cập nhật yeu cau muon</h5>
					<button type="button" class="close" data-dismiss="modal" aria-label="Close">
						<span aria-hidden="true">&times;</span>
					</button>
				</div>
					<div class="modal-body">
						<input type="hidden" :value="borrow.created_user" />
						<div class="form-group row">
						<label class="control-label col-md-2">Tên nguoi yeu cau<span class="required">*</span></label>
						<div class="col-md-10">
						<input required type="text" class="form-control" v-model="borrow.created_user" placeholder="Nhập tên nguoi yeu cau muon" />
						</div>
					</div>
					<div class="form-group row">
						<label class="control-label col-md-2">ten sach can muon </label>
						<div class="col-md-10">
							<textarea style="height: 120px;" class="form-control" v-model="borrow.content"></textarea>
						</div>
					</div>
					<div class="row">
						<div class="form-group col-md-2">
							<label class="control-label text-bold">Trạng thái</label>
							<treeselect v-model="statusList" id="status" @input="getBorrowList(1)" :options="statusList" placeholder="[ Lựa chọn ]" no-results-text="Không có kết quả nào phù hợp!" />
						</div>	
					</div>
				</div>
				<div class="modal-footer">
					<div class="form-actions">
						<button type="button" class="btn btn-default pull-left" data-dismiss="modal">Đóng</button>
						<button type="button" v-on:click="saveBorrow($event)"  class="btn btn-primary pull-right btn-submit">Lưu lại</button>
					</div>
				</div>
			</form>
		</div>
	</div>
</div>