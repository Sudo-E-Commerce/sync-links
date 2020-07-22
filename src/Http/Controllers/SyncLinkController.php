<?php

namespace Sudo\SyncLink\Http\Controllers;
use Sudo\Base\Http\Controllers\AdminController;

use Illuminate\Http\Request;
use ListData;
use Form;
use ListCategory;

class SyncLinkController extends AdminController
{
    function __construct() {
        $this->models = new \Sudo\SyncLink\Models\SyncLink;
        $this->table_name = $this->models->getTable();
        $this->module_name = 'Link đồng bộ';
        $this->has_seo = false;
        $this->has_locale = false;
        parent::__construct();

        $this->redirect = [
            '301' => '301',
            '302' => '302',
        ];
    }
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Request $requests) {
        $listdata = new ListData($requests, $this->models, 'SyncLink::table');
        $redirect = $this->redirect;
        // Build Form tìm kiếm
        $listdata->search('old', 'Link cũ', 'string');
        $listdata->search('new', 'Link mới', 'string');
        $listdata->search('redirect', 'Điều hướng', 'array', $redirect);
        $listdata->search('status', 'Trạng thái', 'array', config('app.status'));
        $listdata->searchBtn('Export', route('admin.ajax.sync_links.export'), 'primary', 'fas fa-file-excel');
        $listdata->searchBtn('Import', '#import-sync-link', 'info', 'fas fa-file-excel');
        // Build các button hành động
        $listdata->btnAction('status', 1, __('Table::table.active'), 'success', 'fas fa-edit');
        $listdata->btnAction('status', 0, __('Table::table.no_active'), 'info', 'fas fa-window-close');
        $listdata->btnAction('delete', -1, __('Table::table.trash'), 'danger', 'fas fa-trash');
        // Build bảng
        $listdata->add('old', 'Link cũ', 1);
        $listdata->add('new', 'Link mới', 1);
        $listdata->add('redirect', 'Điều hướng', 1, 'status', $redirect);
        $listdata->add('status', 'Trạng thái', 1, 'status');
        $listdata->add('', 'Xóa', 0, 'delete');

        $include_view_bottom = [
            'SyncLink::add' => [],
            'SyncLink::import_file' => [],
        ];

        return $listdata->render(compact('include_view_bottom', 'redirect'));
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {   
        // Khởi tạo form
        $form = new Form;
        $form->text('old', '', 0, 'Link cũ');
        $form->text('new', '', 0, 'Link mới');
        $form->select('redirect', '', 0, 'Điều hướng', $this->redirect, 0);
        $form->checkbox('status', 1, 1, 'Trạng thái');   
        $form->action('add');
        // Hiển thị form tại view
        return $form->render('create');
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $requests)
    {
        // Các giá trị mặc định
        $status = 0;
        // Đưa mảng về các biến có tên là các key của mảng
        extract($requests->all(), EXTR_OVERWRITE);
        // Chuẩn hóa lại dữ liệu
        // Thêm vào DB
        $created_at = $updated_at = date('Y-m-d H:i:s');
        $compact = compact('old','new','redirect','status');
        $id = $this->models->createRecord($requests, $compact, $this->has_seo, true);
        if ($requests->ajax()) {
            return [
                'status' => 1,
                'message' => __('Core::admin.create_success')
            ];
        } else {
            // Điều hướng
            return redirect(route('admin.'.$this->table_name.'.'.$redirect, $id))->with([
                'type' => 'success',
                'message' => __('Core::admin.create_success')
            ]);
        }
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id) {
        // Dẽ liệu bản ghi hiện tại
        $data_edit = $this->models->where('id', $id)->first();
        // Khởi tạo form
        $form = new Form;
        $form->text('old', $data_edit->old, 0, 'Link cũ');
        $form->text('new', $data_edit->new, 0, 'Link mới');
        $form->select('redirect', $data_edit->redirect, 0, 'Điều hướng', $this->redirect, 0);
        $form->checkbox('status', $data_edit->status, 1, 'Trạng thái');
        $form->action('edit');
        // Hiển thị form tại view
        return $form->render('edit', compact('id'));
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $requests
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $requests, $id) {
        // Lấy bản ghi
        $data_edit = $this->models->where('id', $id)->first();
        // Các giá trị mặc định
        $status = 0;
        // Đưa mảng về các biến có tên là các key của mảng
        extract($requests->all(), EXTR_OVERWRITE);
        // Chuẩn hóa lại dữ liệu
        // Các giá trị thay đổi
        $compact = compact('old','new','redirect','status');
        // Cập nhật tại database
        $this->models->updateRecord($requests, $id, $compact);
        // Điều hướng
        return redirect(route('admin.'.$this->table_name.'.'.$redirect, $id))->with([
            'type' => 'success',
            'message' => __('Core::admin.update_success')
        ]);
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        //
    }

    /**
     * Thêm dữ liệu từ excel
     */
    public function import(Request $requests) {
        if ($requests->hasFile('files')) {
            $file = $requests->file('files');
            // Lấy thông tin file
            $file_info = pathinfo($file->getClientOriginalName());
            // Phần mở rộng
            $file_extension = $file_info['extension'];
            $allow_extension = ['xlsx','xls'];
            if (in_array($file_extension, $allow_extension)) {
                try {
                    \Excel::import(new \Sudo\SyncLink\Imports\SyncLinkImport, $file);
                    return [
                        'status' => 1,
                        'message' => __('Core::admin.create_success')
                    ];
                } catch (\Exception $e) {
                    \Log::error($e);
                    return [
                        'status' => 2,
                        'message' => __('Có lỗi trong quá trình phân tích dữ liệu, vui lòng kiểm tra lại cấu trúc File.')
                    ];
                }
            } else {
                return [
                    'status' => 2,
                    'message' => __('Định dạng file không chính xác, chỉ chấp nhận file có đuổi xlsx và xls.')
                ];
            }
        } else {
            return [
                'status' => 2,
                'message' => __('Core::admin.ajax_error_edit')
            ];
        }
    }

    /**
     * Xuất dữ liệu excel với điều kiện bộ lọc
     */
    public function export(Request $requests) {
        // Đưa mảng về các biến có tên là các key của mảng
        extract($requests->all(), EXTR_OVERWRITE);
        // Lấy dữ liệu được bắt theo bộ lọc
        $data_exports = $this->models::query();
        
        // Link cũ
        if (isset($old) && $old != '') {
            $data_exports = $data_exports->where('subject', 'LIKE', '%'.$old.'%');
        }
        // Link mới
        if (isset($new) && $new != '') {
            $data_exports = $data_exports->where('new', 'LIKE', '%'.$new.'%');
        }
        // lọc trạng thái
        if (isset($redirect) && $redirect != '') {
            $data_query = $data_query->where('redirect', $redirect);
        }
        // lọc trạng thái
        if (isset($status) && $status != '') {
            $data_query = $data_query->where('status', $status);
        }
        $data_exports = $data_exports->where('status', '<>', -1)->get();

        // Mảng export
        $data = [
            'file_name' => 'sync-links-'.time(),
            'data' => [
                // 
            ]
        ];
        // Foreach lấy mảng data
        foreach ($data_exports as $key => $value) {
            $data['data'][] = [
                $value->old,
                $value->new,
                $value->redirect,
            ];
        }
        return \Excel::download(new \Sudo\Base\Export\GeneralExports($data), $data['file_name'].'.xlsx');
    }

}
