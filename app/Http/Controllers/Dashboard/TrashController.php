<?php

namespace App\Http\Controllers\Dashboard;

use App\Http\Controllers\Controller;

class TrashController extends Controller
{
    private static array $relations = [
        'Admin' => ['roles' => ['id', 'name_ar', 'name_en']],
    ];

    // public function index($modelName = 'Admin')
    // {
    //     // $this->authorize('view_recycle_bin');

    //     // 🔒 حماية من أي model غير مسموح
    //     abort_unless(array_key_exists($modelName, self::$relations), 404);

    //     if (request()->ajax()) {

    //         $model = app('App\\Models\\' . $modelName);

    //         $data = getModelData(
    //             model: $model,
    //             relations: self::$relations[$modelName],
    //             onlyTrashed: true
    //         );

    //         return response()->json($data);
    //     }

    //     return view('dashboard.admin.settings.trash', compact('modelName'));
    // }
    public function index($modelName = 'Admin')
    {
        //$this->authorize('view_recycle_bin');

        if (request()->ajax()) {

            $model = app('App\\Models\\' . $modelName);
            $data  = getModelData(model: $model, relations: TrashController::$relations[$modelName], onlyTrashed: true);

            return response()->json($data);
        }

        return view('dashboard.trash');
    }


    // public function forceDelete($modelName, $id)
    // {
    //     $this->authorize('delete_recycle_bin');

    //     abort_unless(array_key_exists($modelName, self::$relations), 404);

    //     $model  = app('App\\Models\\' . $modelName);
    //     $record = $model->onlyTrashed()->findOrFail($id);

    //     $record->forceDelete();

    //     return response()->json([
    //         'status'  => true,
    //         'message' => __('Deleted permanently'),
    //     ]);
    // }

    public function forceDelete($modelName, $id)
    {
        //$this->authorize('delete_recycle_bin');

        $model = app('App\\Models\\' . $modelName);
        $model->onlyTrashed()->find($id)->forceDelete();
    }

    // public function restore($modelName, $id)
    // {
    //     // dd($modelName);
    //     // $this->authorize('restore_recycle_bin');

    //     abort_unless(array_key_exists($modelName, self::$relations), 404);

    //     $model  = app('App\\Models\\' . $modelName);
    //     $record = $model->onlyTrashed()->find($id);

    //     if (!$record) {
    //         return response()->json([
    //             'status'  => false,
    //             'message' => __("Record not found or not trashed"),
    //         ], 404);
    //     }

    //     $record->restore();

    //     return response()->json([
    //         'status'  => true,
    //         'message' => __("Record restored successfully"),
    //     ]);
    // }
    public function restore($modelName, $id)
    {
        //$this->authorize('restore_recycle_bin');

        $model = app('App\\Models\\' . $modelName);
        $resultRestore = $model->onlyTrashed()->find($id)->restore();

        // if ($modelName == "Admin" && $resultRestore) {

        //     return redirect()->route('dashboard.admins.index');
        // } else if ($modelName == "Observer" && $resultRestore)
        //     return redirect()->route('dashboard.observers.index');
    }
}
