<?php
App::booted(function() {
	$namespace = 'Sudo\SyncLink\Http\Controllers';
	
	Route::namespace($namespace)->name('admin.')->prefix(config('app.admin_dir'))->middleware(['web', 'auth-admin'])->group(function() {
		// Bài viết
		Route::resource('sync_links', 'SyncLinkController');
	});
});