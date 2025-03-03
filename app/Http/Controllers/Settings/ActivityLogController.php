<?php

namespace App\Http\Controllers\Settings;

use App\Http\Controllers\Controller;
use App\Models\User;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Spatie\Activitylog\Models\Activity;
use Yajra\DataTables\DataTables;

class ActivityLogController extends Controller
{
    public function index()
    {
        $data = [
            'title' => 'Log Aktivitas',
        ];
        return view('pages.settings.activitylog.index', $data);
    }

    //getdata
    public function getData()
    {
        $data = Activity::orderByDesc('id')->get();

        return DataTables::of($data)
            ->addIndexColumn()
            ->editColumn('log', function ($data) {
                $causerName = $data->causer ? $data->causer->name : 'Unknown User';
                $logMessage = "<strong>{$causerName}</strong> - <i>{$data->event}</i><br><small><i>" . $data->created_at->format('d M Y H:i') . "</i></small><br>" . $data->description;

                $properties = $data->properties ? json_decode($data->properties, true) : [];
                if ($properties) {
                    $logMessage .= "<br><strong>Changes:</strong><br>";

                    if (isset($properties['old'])) {
                        $logMessage .= "<strong>Old Values:</strong><br>";
                        foreach ($properties['old'] as $key => $value) {
                            $logMessage .= "<i>{$key}</i>: {$value}<br>";
                        }
                    }

                    if (isset($properties['new'])) {
                        $logMessage .= "<strong>New Values:</strong><br>";
                        foreach ($properties['new'] as $key => $value) {
                            $logMessage .= "<i>{$key}</i>: {$value}<br>";
                        }
                    }
                }

                return $logMessage;
            })
            ->rawColumns(['log'])
            ->make(true);
    }

    public function cleanlog()
    {
        try {
            Activity::truncate();
            return response()->json([
                'status' => 'success',
                'success' => true,
                'message' => 'Log Berhasil Dihapus!.',
            ]);
        } catch (Exception $e) {
            return response()->json([
                'message' => $e->getMessage(),
                'trace' => $e->getTrace()
            ]);
        }
    }
}
