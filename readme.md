## Hướng dẫn sử dụng Sudo Sync Link ##

**Giới thiệu:** Đây là package dùng để quản lý Link đồng bộ của SudoCms.

Mặc định package sẽ tạo ra giao diện quản lý cho toàn bộ Link đồng bộ được đặt tại `/{admin_dir}/sync_links`, trong đó admin_dir là đường dẫn admin được đặt tại `config('app.admin_dir')`

### Cài đặt để sử dụng ###

- Package cần phải có base `sudo/core` để có thể hoạt động không gây ra lỗi
- Để có thể sử dụng Package cần require theo lệnh `composer require sudo/sync-links`
- Chạy `php artisan migrate` để tạo các bảng phục vụ cho package

### Cấu hình tại Menu ###

	[
    	'type' 		=> 'single',
		'name' 		=> 'Link đồng bộ',
		'icon' 		=> 'fas fa-link',
		'route' 	=> 'admin.sync_links.index',
		'role'		=> 'sync_links_index'
	],
 
- Vị trí cấu hình được đặt tại `config/SudoMenu.php`
- Để có thể hiển thị tại menu, chúng ta có thể đặt đoạn cấu hình trên tại `config('SudoMenu.menu')`

### Cấu hình tại Module ###
	
	'sync_links' => [
		'name' 			=> 'Link đồng bộ',
		'permision' 	=> [
			[ 'type' => 'index', 'name' => 'Truy cập' ],
			[ 'type' => 'create', 'name' => 'Thêm' ],
			[ 'type' => 'edit', 'name' => 'Sửa' ],
			[ 'type' => 'restore', 'name' => 'Lấy lại' ],
			[ 'type' => 'delete', 'name' => 'Xóa' ],
		],
	],

- Vị trí cấu hình được đặt tại `config/SudoModule.php`
- Để có thể phân quyền, chúng ta có thể đặt đoạn cấu hình trên tại `config('SudoModule.modules')`
 
### Sử dụng ###

Tính năng này dùng điều hướng link cũ sang link mới với các dạng điều hướng 301 và 302 phục vụ cho SEO. 

Nhúng đoạn mã dưới đây tại hàm `render` của `app/Exceptions/Handler.php` để thực hiện Check và điều hướng

	$check_syncs = \DB::table('sync_links')->where('old', $_SERVER['REQUEST_URI'])->where('status', 1)->first();
    if(!empty($check_syncs) && !empty($check_syncs->new)){
        return redirect($check_syncs->new ?? '/', $check_syncs->redirect);
    }
