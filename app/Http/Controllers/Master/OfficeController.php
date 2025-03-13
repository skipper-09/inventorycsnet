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
            'companies' => Company::all(),
        ];

        return view('pages.master.office.index', $data);
    }

    public function getData()
    {
        $data = Office::with('company')->orderByDesc('id')->get();
        return DataTables::of($data)
            ->addIndexColumn()
            ->addColumn('company_name', function ($row) {
                return $row->company->name ?? '-';
            })
            ->addColumn('action', function ($data) {
                $userauth = User::with('roles')->where('id', Auth::id())->first();
                $button = '';
                if ($userauth->can('update-office')) {
                    $button .= ' <button class="btn btn-sm btn-success" data-id=' . $data->id . ' data-type="edit" data-route="' . route('office.edit', ['id' => $data->id]) . '" data-proses="' . route('office.update', ['id' => $data->id]) . '" data-bs-toggle="modal" data-bs-target="#modal8"
                            data-action="edit" data-title="Deduksi" data-toggle="tooltip" data-placement="bottom" title="Edit Data"><i
                                                        class="fas fa-pen "></i></button>';
                }
                if ($userauth->can('delete-office')) {
                    $button .= ' <button class="btn btn-sm btn-danger action" data-id=' . $data->id . ' data-type="delete" data-route="' . route('office.delete', ['id' => $data->id]) . '" data-toggle="tooltip" data-placement="bottom" title="Delete Data"><i
                                                        class="fas fa-trash "></i></button>';
                }
                return '<div class="d-flex gap-2">' . $button . '</div>';
            })->rawColumns(['action', 'company_name'])->make(true);
    }

    public function store(Request $request)
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
            $office = new Office();
            $office->company_id = $request->company_id;
            $office->name = $request->name;
            $office->lat = $request->lat;
            $office->long = $request->long;
            $office->radius = $request->radius;
            $office->address = $request->address;

            $office->save();

            activity()
                ->causedBy(Auth::user())
                ->event('created')
                ->withProperties($office->toArray())
                ->log("Data Kantor berhasil dibuat.");

            return response()->json([
                'success' => true,
                'status' => "Berhasil",
                'message' => 'Data Kantor Berhasil dibuat.'
            ]);
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
        return response()->json([
            'office' => $office,
        ], 200);
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
            $oldOffice = $office->toArray();
            $office->company_id = $request->company_id;
            $office->name = $request->name;
            $office->lat = $request->lat;
            $office->long = $request->long;
            $office->radius = $request->radius;
            $office->address = $request->address;

            $office->save();

            activity()
                ->causedBy(Auth::user())
                ->event('updated')
                ->withProperties([
                    'old' => $oldOffice,
                    'new' => $office->toArray()
                ])
                ->log("Data Kantor berhasil diperbarui.");

            return response()->json([
                'success' => true,
                'status' => "Berhasil",
                'message' => 'Data Kantor Berhasil diupdate.'
            ]);
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
                ->withProperties($office->toArray())
                ->log("Data Kantor berhasil dihapus.");

            $office->delete();

            return response()->json([
                'status' => 'success',
                'success' => true,
                'message' => 'Data Kantor Berhasil Dihapus!.',
            ]);
        } catch (Exception $e) {
            return response()->json([
                'message' => 'Gagal Menghapus Data Kantor!',
                'trace' => $e->getTrace()
            ]);
        }
    }
}
