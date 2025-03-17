<?php

namespace App\Http\Controllers\Master;

use App\Http\Controllers\Controller;
use App\Models\Company;
use App\Models\Office;
use App\Models\User;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Yajra\DataTables\Facades\DataTables;

class OfficeController extends Controller
{
    public function index()
    {
        $data = [
            'title' => 'Kantor',
        ];
        return view('pages.master.office.index', $data);
    }


    public function getData()
    {
        $data = Office::with(['Company'])->orderByDesc('id')->get();
        return DataTables::of($data)->addIndexColumn()->addColumn('action', function ($data) {
            $userauth = User::with('roles')->where('id', Auth::id())->first();
            $button = '';

            if ($userauth->can('update-office')) {
                $button .= ' <a href="' . route('office.edit', ['id' => $data->id]) . '" class="btn btn-sm btn-success action mr-1" data-id=' . $data->id . ' data-type="edit" data-toggle="tooltip" data-placement="bottom" title="Edit Data"><i class="fas fa-pencil-alt"></i></a>';
            }
            if ($userauth->can('delete-office')) {
                $button .= ' <button class="btn btn-sm btn-danger action" data-id=' . $data->id . ' data-type="delete" data-route="' . route('office.delete', ['id' => $data->id]) . '" data-toggle="tooltip" data-placement="bottom" title="Delete Data"><i
                                                        class="fas fa-trash "></i></button>';
            }
            return '<div class="d-flex gap-2">' . $button . '</div>';
        })->editColumn('company', function ($data) {
            return $data->Company->name;
        })->editColumn('radius', function ($data) {
            return $data->radius . ' Meter';
        })->editColumn('location', function ($data) {
            if ($data->lat && $data->long) {
                $locationLink = 'https://www.google.com/maps?q=' . $data->lat . ',' . $data->long;
                return '<a href="' . $locationLink . '" target="_blank">Lihat Lokasi</a>';
            } else {
                return 'Lokasi tidak tersedia';
            }
        })->rawColumns(['action', 'company', 'location', 'radius'])->make(true);
    }


    public function create()
    {
        $data = [
            'title' => 'Kantor',
            'company' => Company::all(),
        ];

        return view('pages.master.office.add', $data);
    }


    public function store(Request $request)
    {

        $request->validate([
            'company_id' => 'required',
            'name' => 'required|string',
            'lat' => 'required',
            'long' => 'required',
            'radius' => 'required|integer',
            'address' => 'required'
        ]);

        try {
            Office::create([
                'company_id' => $request->company_id,
                'name' => $request->name,
                'lat' => $request->lat,
                'long' => $request->long,
                'radius' => $request->radius,
                'address' => $request->address,

            ]);
            return redirect()->route('office')->with('success', 'Data berhasil disimpan.');
        } catch (Exception $e) {
            return response()->json([
                'success' => false,
                'status' => "Gagal",
                'message' => 'An error occurred: ' . $e->getMessage()
            ]);
        }
    }

    public function show($id)
    {
        $office = Office::findOrFail($id);
        $data = [
            'title' => 'Kantor',
            'company' => Company::all(),
            'office' => $office,
        ];
        return view('pages.master.office.edit', $data);
    }

    public function update(Request $request, $id)
    {
        $request->validate([
            'company_id' => 'required|exists:companies,id',
            'name' => 'required|string',
            'lat' => 'required|string',
            'long' => 'required|string',
            'radius' => 'required|numeric|min:1',
            'address' => 'required|string'
        ], [
            'company_id.required' => 'Perusahaan harus dipilih.',
            'name.required' => 'Nama cabang harus diisi.',
            'lat.required' => 'Latitude harus diisi.',
            'long.required' => 'Longitude harus diisi',
            'radius.required' => 'Radius harus diisi.',
            'address' => 'Alamat harus diisi'
        ]);

        try {
            $office = Office::findOrFail($id);
            $office->update([
                'company_id' => $request->company_id,
                'name' => $request->name,
                'lat' => $request->lat,
                'long' => $request->long,
                'radius' => $request->radius,
                'address' => $request->address,

            ]);
            return redirect()->route('office')->with('success', 'Data berhasil diupdate.');
        } catch (Exception $e) {
            return response()->json([
                'success' => false,
                'status' => "Gagal",
                'message' => 'An error occurred: ' . $e->getMessage()
            ]);
        }
    }





    public function destroy($id)
    {
        try {
            $office = Office::findOrFail($id);

            activity()
                ->causedBy(Auth::user())
                ->event('deleted')
                ->withProperties(['new' => $office->toArray()])
                ->log("Data Kantor berhasil dihapus.");

            $office->delete();

            return response()->json([
                'status' => 'success',
                'success' => true,
                'message' => 'Data Kantor Berhasil Dihapus!.',
            ]);
        } catch (Exception $e) {
            return response()->json([
                'message' => 'Gagal Menghapus Data Cabang!',
                'trace' => $e->getTrace()
            ]);
        }
    }
}
