<?php

namespace App\Http\Controllers\Settings;

use App\Http\Controllers\Controller;
use App\Models\User;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;
use Yajra\DataTables\Facades\DataTables;

class RoleController extends Controller
{
    public function index()
    {
        $data = [
            'title' => 'Role',
            'role' => Role::all(),
        ];

        return view('pages.settings.role.index', $data);
    }

    public function getData()
    {
        $role = Role::whereNotIn('name', ['Developer'])
            ->orderByDesc('id')
            ->get();

        return DataTables::of($role)
            ->addIndexColumn()
            ->addColumn('action', function ($role) {
                $userauth = User::with('roles')->where('id', Auth::id())->first();

                $button = '';
                if ($userauth->can('update-role')) {
                    $button .= '<a href="' . route('role.edit', ['id' => $role->id]) . '"
                          class="btn btn-sm btn-success" 
                          data-id="' . $role->id . '" 
                          data-type="edit" 
                          data-toggle="tooltip" 
                          data-placement="bottom" 
                          title="Edit Data">
                           <i class="fas fa-pen"></i>
                       </a>';
                }
                if ($userauth->can('delete-role')) {
                    $button .= ' <button class="btn btn-sm btn-danger action" 
                               data-id="' . $role->id . '" 
                               data-type="delete" 
                               data-route="' . route('role.delete', ['id' => $role->id]) . '" 
                               data-toggle="tooltip" 
                               data-placement="bottom" 
                               title="Delete Data">
                            <i class="fas fa-trash-alt"></i>
                        </button>';
                }

                return '<div class="d-flex gap-2">' . $button . '</div>';
            })
            ->rawColumns(['action'])
            ->make(true);
    }

    public function create()
    {
        $data = [
            'permission' => Permission::all(),
            'title' => 'Role',
        ];
        return view('pages.settings.role.add', $data);
    }

    public function store(Request $request)
    {
        $request->validate(['name' => 'required'], [
            'name.required' => 'Nama wajib diisi.',
        ]);

        try {
            $role = Role::create($request->only(['name']));
            $role->givePermissionTo($request->permissions);

            return redirect()->route('role')->with(['status' => 'Success!', 'message' => 'Berhasil Menambahkan Role!']);
        } catch (Exception $e) {
            return redirect()->route('role')->with(['status' => 'Error!', 'message' => 'Gagal Menambahkan Role!']);
        }
    }

    public function show($id)
    {
        $data = [
            'permission' => Permission::all(),
            'title' => 'Role',
            'role' => Role::with('permissions')->findOrFail($id)
        ];
        return view('pages.settings.role.edit', $data);
    }

    public function update(Request $request, $id)
    {
        $request->validate(['name' => 'required'], [
            'name.required' => 'Nama wajib diisi.',
        ]);

        try {
            $role = Role::find($id);

            $role->syncPermissions($request->permissions);
            $role->update($request->only(['name']));

            return redirect()->route('role')->with(['status' => 'Success!', 'message' => 'Berhasil Mengubah Role!']);
        } catch (Exception $e) {
            return redirect()->route('role')->with(['status' => 'Error!', 'message' => 'Gagal Mengubah Role!']);
        }
    }

    public function destroy($id)
    {
        try {
            $role = Role::findOrFail($id);
            $role->delete();
            return response()->json([
                'status' => 'success',
                'success' => true,
                'message' => 'Role Berhasil Dihapus!.',
            ]);
        } catch (Exception $e) {
            return response()->json([
                'message' => 'Role Gagal dihapus!',
                'trace' => $e->getTrace()
            ]);
        }
    }
}
