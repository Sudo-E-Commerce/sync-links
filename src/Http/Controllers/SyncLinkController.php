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
    }
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Request $requests) {
        $listdata = new ListData($requests, $this->models, 'SyncLink::table');
        // Build Form tìm kiếm
        $listdata->search('old', 'Link cũ', 'string');
        $listdata->search('new', 'Link mới', 'string');
        $listdata->search('status', 'Trạng thái', 'array', config('app.status'));
        // Build các button hành động
        $listdata->btnAction('status', 1, __('Table::table.active'), 'primary', 'fas fa-edit');
        $listdata->btnAction('status', 0, __('Table::table.no_active'), 'warning', 'fas fa-edit');
        $listdata->btnAction('delete', -1, __('Table::table.trash'), 'danger', 'fas fa-trash');
        // Build bảng
        $listdata->add('old', 'Link cũ', 1);
        $listdata->add('new', 'Link mới', 1);
        $listdata->add('status', 'Trạng thái', 1, 'status');
        $listdata->add('', 'Sửa', 0, 'edit');
        $listdata->add('', 'Xóa', 0, 'delete');

        return $listdata->render();
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
        $compact = compact('old','new','status');
        $id = $this->models->createRecord($requests, $compact, $this->has_seo, true);
        // Điều hướng
        return redirect(route('admin.'.$this->table_name.'.'.$redirect, $id))->with([
            'type' => 'success',
            'message' => __('Core::admin.create_success')
        ]);
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
        $compact = compact('old','new','status');
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

}
