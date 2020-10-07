<div class="modal fade" id="add-sync-links">
	<div class="modal-dialog">
		{{-- {{ route('admin.ajax.sync_links.quick_create') }} --}}
		<form action="" method="POST">
			<div class="modal-content">
				<div class="modal-header">
					<h4 class="modal-title">@lang('Translate::table.create') @lang($module_name)</h4>
					<button type="button" class="close" data-dismiss="modal" aria-label="Close">
					<span aria-hidden="true">×</span>
				</button>
				</div>
				<div class="modal-body">
					@include('Form::base.text', [
				    	'name'				=> 'old',
						'value' 			=> '',
						'required' 			=> 0,
						'label' 			=> 'Link cũ',
						'has_full' 			=> true,
				    ])
				    @include('Form::base.text', [
				    	'name'				=> 'new',
						'value' 			=> '',
						'required' 			=> 0,
						'label' 			=> 'Link mới',
						'has_full' 			=> true,
				    ])
				    @include('Form::base.select', [
				    	'name'				=> 'redirect',
						'value' 			=> '',
						'required' 			=> 0,
						'label' 			=> 'Điều hướng',
						'options'			=> $redirect ?? [],
						'has_full' 			=> true,
				    ])
				</div>
				<div class="modal-footer justify-content-between">
					<button type="button" class="btn btn-default btn-sm" data-dismiss="modal">@lang('Đóng')</button>
					<button type="submit" class="btn btn-primary btn-sm" data-add_sync_links>@lang('Translate::table.create')</button>
				</div>
			</div>
		</form>
	</div>
</div>
<script>
	$(document).ready(function() {
		$('body').on('click', '*[href="{{ route('admin.sync_links.create') }}"]', function(e) {
			e.preventDefault();
			$('#add-sync-links').modal();
		})
		$('body').on('click', '*[data-add_sync_links]', function(e) {
			e.preventDefault();
			form = $(this).closest('form');
			sync_link_old = form.find('#old').val();
			sync_link_new = form.find('#new').val();
			sync_link_redirect = form.find('#redirect').val();
			data = {
				old: sync_link_old,
				new: sync_link_new,
				redirect: sync_link_redirect,
				status: 1,
			};
			loadAjaxPost('{{ route('admin.sync_links.store') }}', data, {
		        beforeSend: function(){},
		        success:function(result){
		        	if (result.status == 1) {
			        	$('#add-sync-links .close').click();
			        	loadData('no_animate');
		        		alertText(result.message);
		        	} else {
		        		alertText(result.message, 'error');
		        	}
		        },
		        error: function (error) {}
		    }, 'progress');
		})
	})
</script>