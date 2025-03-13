<?php

namespace App\Http\Controllers\Master;

use App\Http\Controllers\Controller;
use App\Models\Company;
use App\Models\User;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Yajra\DataTables\DataTables;

class CompanyController extends Controller
{
    public function index()
    {
        $data = [
            'title' => 'Perusahaan',
        ];
        return view('pages.master.company.index', $data);
    }

    public function getData()
    {
        $data = Company::orderByDesc('id')->get();
        return DataTables::of($data)->addIndexColumn()->addColumn('action', function ($data) {
            $userauth = User::with('roles')->where('id', Auth::id())->first();
            $button = '';

            if ($userauth->can('update-company')) {
                $button .= ' <button class="btn btn-sm btn-success" data-id=' . $data->id . ' data-type="edit" data-route="' . route('company.edit', ['id' => $data->id]) . '" data-proses="' . route('company.update', ['id' => $data->id]) . '" data-bs-toggle="modal" data-bs-target="#modal8"
                            data-action="edit" data-title="Perusahaan" data-toggle="tooltip" data-placement="bottom" title="Edit Data"><i
                                                        class="fas fa-pen "></i></button>';
            }
            if ($userauth->can('delete-company')) {
                $button .= ' <button class="btn btn-sm btn-danger action" data-id=' . $data->id . ' data-type="delete" data-route="' . route('company.delete', ['id' => $data->id]) . '" data-toggle="tooltip" data-placement="bottom" title="Delete Data"><i
                                                        class="fas fa-trash "></i></button>';
            }
            return '<div class="d-flex gap-2">' . $button . '</div>';
        })->rawColumns(['action'])->make(true);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'address' => 'required|string|max:255',
        ], [
            'name.required' => 'Nama Perusahan harus diisi.',
            'address.required' => 'Alamat Perusahaan harus diisi.',
        ]);

        try {
            $company = new Company();
            $company->name = $request->name;
            $company->address = $request->address;
            $company->save();

            activity()
                ->causedBy(Auth::user())
                ->event('created')
                ->withProperties(['new'=>$company->toArray()])
                ->log("Data Perusahaan berhasil dibuat " .$company->name);

            return response()->json([
                'success' => true,
                'status' => "Berhasil",
                'message' => 'Perusahaan Berhasil dibuat.'
            ]);
        } catch (Exception $e) {
            return response()->json([
                'success' => false,
                'status' => "Gagal",
                'message' => 'An error occurred: ' . $e->getMessage()
            ]);
        }
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        $company = Company::findOrFail($id);
        return response()->json([
            'company' => $company,
        ], 200);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'address' => 'required|string|max:255',
        ], [
            'name.required' => 'Nama Perusahaan harus diisi.',
            'address.required' => 'Alamat harus diisi.',
        ]);

        try {
            $company = Company::findOrFail($id);
            $oldBranch = $company->toArray();
            $company->name = $request->name;
            $company->address = $request->address;
            $company->save();

            activity()
                ->causedBy(Auth::user())
                ->event('updated')
                ->withProperties([
                    'old' => $oldBranch,
                    'new' => $company->toArray()
                ])
                ->log("Data Perusahaan berhasil diperbarui.");

            return response()->json([
                'success' => true,
                'status' => "Berhasil",
                'message' => 'Perusahaan Berhasil diupdate.'
            ]);
        } catch (Exception $e) {
            return response()->json([
                'success' => false,
                'status' => "Gagal",
                'message' => 'An error occurred: ' . $e->getMessage()
            ]);
        }
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy($id)
    {
        try {
            $company = Company::findOrFail($id);

            activity()
                ->causedBy(Auth::user())
                ->event('deleted')
                ->withProperties($company->toArray())
                ->log("Data Perusahaan berhasil dihapus.");

            $company->delete();

            return response()->json([
                'status' => 'success',
                'success' => true,
                'message' => 'Data Perusahaan Berhasil Dihapus!.',
            ]);
        } catch (Exception $e) {
            return response()->json([
                'message' => 'Gagal Menghapus Data Cabang!',
                'trace' => $e->getTrace()
            ]);
        }
    }
}
