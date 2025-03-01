<?php

namespace App\Http\Controllers\Report;

use App\Http\Controllers\Controller;
use App\Models\FreeReport;
use App\Models\User;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Str;
use Yajra\DataTables\Facades\DataTables;

class FreeReportController extends Controller
{
    public function index()
    {
        $data = [
            'title' => 'Laporan Aktivitas',
        ];

        return view('pages.report.activity.index', $data);
    }

    public function getData(Request $request)
    {
        $currentUser = Auth::user();
        $userRole = $currentUser->role;

        // Base query
        $query = FreeReport::query();

        // Apply role-based filtering
        if ($userRole == 'Employee') {
            $query->where('user_id', $currentUser->id);
        }

        // Apply any additional filters if needed
        // For example, if you have a date filter:
        // if ($request->filled('created_at')) {
        //     $query->whereDate('created_at', $request->input('created_at'));
        // }

        $data = $query->with('user')->get();

        return DataTables::of($data)
            ->addIndexColumn()
            ->addColumn('action', function ($freeReport) {
                $userauth = User::with('roles')->where('id', Auth::id())->first();
                $button = '';

                $stripped = strip_tags($freeReport->report_activity);

                if (strlen($stripped) > 50) {
                    $button .= ' <button class="btn btn-sm btn-info show-full-activity" 
                        data-bs-toggle="modal" 
                        data-bs-target="#viewActivityReportModal" 
                        data-report_activity="' . htmlspecialchars($freeReport->report_activity, ENT_QUOTES) . '" 
                        data-toggle="tooltip" 
                        data-placement="bottom" 
                        title="View Activity">
                        <i class="fas fa-eye"></i>
                    </button>';
                }


                if ($userauth->can(abilities: 'update-activity-report')) {
                    $button .= '<a href="' . route('activityreport.edit', ['id' => $freeReport->id]) . '"
                              class="btn btn-sm btn-success"
                              data-id="' . $freeReport->id . '"
                              data-type="edit"
                              data-toggle="tooltip"
                              data-placement="bottom"
                              title="Edit Data">
                              <i class="fas fa-pen"></i>
                          </a>';
                }

                if ($userauth->can('delete-activity-report')) {
                    $button .= ' <button class="btn btn-sm btn-danger action"
                                      data-id="' . $freeReport->id . '"
                                      data-type="delete"
                                      data-route="' . route('activityreport.delete', ['id' => $freeReport->id]) . '"
                                      data-toggle="tooltip"
                                      data-placement="bottom"
                                      title="Delete Data">
                                  <i class="fas fa-trash-alt"></i>
                              </button>';
                }

                return '<div class="d-flex gap-2">' . $button . '</div>';
            })->editColumn('report_activity', function ($data) {
                $stripped = strip_tags($data->report_activity);
                return Str::limit($stripped, limit: 100);
            })->editColumn('created_at', function ($data) {
                return formatDate($data->created_at);
            })->addColumn('user_name', function ($freeReport) use ($userRole) {
                return $userRole != 'Employee' ? $freeReport->user->name : '';
            })
            ->rawColumns(['action', 'user_name'])
            ->make(true);
    }

    public function create()
    {
        $data = [
            'title' => 'Laporan Aktivitas',
        ];

        return view('pages.report.activity.add', $data);
    }

    public function store(Request $request)
    {
        // Validate the incoming request
        $request->validate([
            'report_activity' => 'required',
        ], [
            'report_activity.required' => 'Laporan aktivitas harus diisi.',
        ]);

        try {
            $freeReport = new FreeReport();
            $authUser = Auth::user();

            $freeReport->fill([
                'report_activity' => $request->report_activity,
                'user_id' => $authUser->id,
            ]);

            $freeReport->save();

            return redirect()->route('activityreport')->with([
                'status' => 'Success!',
                'message' => 'Berhasil Menambahkan Laporan Aktivitas!'
            ]);
        } catch (Exception $e) {
            Log::error($e->getMessage());
            return redirect()->route('activityreport')->with(['status' => 'Error!', 'message' => 'Gagal Menambahkan Laporan Aktivitas!']);
        }
    }

    public function edit($id)
    {
        $currentUser = Auth::user();
        $userRole = $currentUser->role;

        if ($userRole == 'Employee') {
            $freeReport = FreeReport::where('user_id', $currentUser->id)->find($id);
        } else {
            $freeReport = FreeReport::find($id);
        }

        $data = [
            'title' => 'Laporan Aktivitas',
            'freeReport' => $freeReport,
        ];

        return view('pages.report.activity.edit', $data);
    }

    public function update(Request $request, $id)
    {
        $request->validate([
            'report_activity' => 'required',
        ], [
            'report_activity.required' => 'Laporan aktivitas harus diisi.',
        ]);

        try {
            $freeReport = FreeReport::find($id);
            $freeReport->report_activity = $request->report_activity;
            $freeReport->save();

            return redirect()->route('activityreport')->with([
                'status' => 'Success!',
                'message' => 'Berhasil Mengubah Laporan Aktivitas!'
            ]);
        } catch (Exception $e) {
            Log::error($e->getMessage());
            return redirect()->route('activityreport')->with(['status' => 'Error!', 'message' => 'Gagal Mengubah Laporan Aktivitas!']);
        }
    }

    public function destroy($id)
    {
        try {
            $freeReport = FreeReport::find($id);
            $freeReport->delete();

            return redirect()->route('activityreport')->with([
                'status' => 'Success!',
                'message' => 'Berhasil Menghapus Laporan Aktivitas!'
            ]);
        } catch (Exception $e) {
            Log::error($e->getMessage());
            return redirect()->route('activityreport')->with(['status' => 'Error!', 'message' => 'Gagal Menghapus Laporan Aktivitas!']);
        }
    }
}
