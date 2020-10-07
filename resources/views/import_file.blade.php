<div class="modal fade" id="import-sync-links">
	<div class="modal-dialog">
		{{-- {{ route('admin.ajax.sync_links.quick_create') }} --}}
		<form action="" method="POST">
			<div class="modal-content">
				<div class="modal-header">
					<h4 class="modal-title">@lang('Import') @lang($module_name)</h4>
					<button type="button" class="close" data-dismiss="modal" aria-label="Close">
					<span aria-hidden="true">×</span>
				</button>
				</div>
				<div class="modal-body">
					<div class="form-group">
						<input type="file" name="file_sync_links" class="form-control" style="padding: 3px; margin-bottom: 5px;">
						<span class="helper">@lang('Sử dụng file Excel (xlsx, xls) để Import dữ liệu Link đồng bộ')</span>
						<span class="helper">@lang('Cột 1 (Cột A): Điền link cũ')</span>
						<span class="helper">@lang('Cột 2 (Cột B): Điền link mới')</span>
						<span class="helper">@lang('Cột 3 (Cột C): Điền điều hướng (301 | 302) (Không nhập mặc định là 301)')</span>
					</div>
				</div>
				<div class="modal-footer justify-content-between">
					<button type="button" class="btn btn-default btn-sm" data-dismiss="modal">@lang('Đóng')</button>
					<button type="submit" class="btn btn-primary btn-sm" data-import_sync_links>@lang('Translate::table.create')</button>
				</div>
			</div>
		</form>
	</div>
</div>
<script>
	$(document).ready(function() {
		$('body').on('click', '*[formaction="#import-sync-link"]', function(e) {
			e.preventDefault();
			$('#import-sync-links').modal();
		})
		$('body').on('click', '*[data-import_sync_links]', function(e) {
			e.preventDefault();
			file = $('#import-sync-links input[name=file_sync_links]').prop("files");
			if (file.length == 0) {
				alertText('@lang('Vui lòng chọn File.')', 'warning');
			} else {
				
				var form_data = new FormData();
				form_data.append('files', file[0]);
				url = '{{ route('admin.ajax.sync_links.import') }}';
				$.ajax({
			        headers: {
			            'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
			        },
			        type: 'POST',
			        url: url,
			        data: form_data,
			        enctype: 'multipart/form-data',
			        processData: false,
			        contentType: false,
			        beforeSend: function(){
			            activeProgress(0);
			        },
			        success:function(result){
			            activeProgress(99, 'close');
			            if (result.status == 1) {
			            	$('#import-sync-links input[name=file_sync_links]').val('');
				        	$('#import-sync-links .close').click();
				        	loadData('no_animate');
			        		alertText(result.message);
			        	} else {
			        		alertText(result.message, 'error');
			        	}
			        },
			        error: function (error) {
			            activeProgress(99, 'close');
			            alertText('@lang('Translate::admin.ajax_fail')', 'error')
			        }
			    })
			}
		})
	})
</script>