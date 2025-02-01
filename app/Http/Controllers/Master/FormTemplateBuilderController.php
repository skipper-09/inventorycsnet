<?php

namespace App\Http\Controllers\Master;

use App\Http\Controllers\Controller;
use App\Models\FormTemplate;
use App\Models\Product;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Laravel\Prompts\FormBuilder;
use Spatie\Permission\Models\Role;
use Yajra\DataTables\DataTables;

class FormTemplateBuilderController extends Controller
{
    public function index()
    {
        $data = [
            'title' => 'Form Template Builder',
        ];
        return view('pages.master.formtemplate.index', $data);
    }



    public function getData()
    {
        $data = FormTemplate::orderByDesc('id')->get();
        return DataTables::of($data)->addIndexColumn()->addColumn('action', function ($data) {
            // $userauth = User::with('roles')->where('id', Auth::id())->first();
                        // $button .= ' <a href="' . route('dashboard') . '" class="btn btn-sm btn-success action mr-1" data-id=' . $data->id . ' data-type="edit" data-toggle="tooltip" data-placement="bottom" title="Edit Data"><i
            //                                             class="fas fa-pen "></i></a>';
            $button = '';
            $button .= ' <a href="' . route('formbuilder.edit', ['id' => $data->id]) . '" class="btn btn-sm btn-success action mr-1" data-id=' . $data->id . ' data-type="edit" data-toggle="tooltip" data-placement="bottom" title="Edit Data"><i class="fas fa-pencil-alt"></i></a>';
            $button .= ' <a href="' . route('formbuilder.detail', ['id' => $data->id]) . '" class="btn btn-sm btn-info action mr-1" data-id=' . $data->id . ' data-type="edit" data-toggle="tooltip" data-placement="bottom" title="Detail Data"><i class="fas fa-eye"></i></a>';
            $button .= ' <button class="btn btn-sm btn-danger action" data-id=' . $data->id . ' data-type="delete" data-route="' . route('customer.delete', ['id' => $data->id]) . '" data-toggle="tooltip" data-placement="bottom" title="Delete Data"><i
                                                        class="fas fa-trash "></i></button>';            
            return '<div class="d-flex gap-2">' . $button . '</div>';
        })->rawColumns(['action'])->make(true);
    }


    public function create()
    {
        $data = [
            'title' => 'Form Template Builder',
            'role' => Role::whereNotIn('name', ['Developer', 'Administrator'])
                ->orderByDesc('id')
                ->get(),
            'product' => Product::all(),
        ];
        return view('pages.master.formtemplate.add', $data);
    }


    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'role_id' => 'required',
            'form' => 'required|json',
        ]);
        try {
            $form = new FormTemplate();
            $form->name = $validated['name'];
            $form->role_id = $validated['role_id'];
            $form->content = json_encode($validated['form']);
            $form->save();

            return response()->json(['success' => true, 'message' => 'Berhasil Membuat Template Form']);
        } catch (\Exception $e) {
            return response()->json(['success' => false, 'error' => $e->getMessage()]);
        }
    }



    public function FormView($id)
    {
        $formData = FormTemplate::findOrFail($id);
        $formTemplate = json_decode($formData->content, true);
        // dd($formTemplate);
        $data = [
            'title' => 'Form ' . $formData->name,
            'formTemplate' => $formTemplate,
        ];

        return view('pages.master.formtemplate.showtemplate', $data);
    }




    public function show($id)
    {
        $form = FormTemplate::findOrFail($id);
        $data = [
            'title' => 'Form Template Builder',
            'role' => Role::whereNotIn('name', ['Developer', 'Administrator'])
                ->orderByDesc('id')
                ->get(),
            'product' => Product::all(),
            'formbuilder' => $form,
            'contentform' => json_decode($form->content,true)
        ];
        return view('pages.master.formtemplate.edit', $data);
    }


    public function update(Request $request,$id)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'role_id' => 'required',
            'form' => 'required|json',
        ]);
        try {
            $form = FormTemplate::findOrFail($id);
            $form->name = $validated['name'];
            $form->role_id = $validated['role_id'];
            $form->content = json_encode($validated['form']);
            $form->save();

            return response()->json(['success' => true, 'message' => 'Berhasil Membuat Template Form']);
        } catch (\Exception $e) {
            return response()->json(['success' => false, 'error' => $e->getMessage()]);
        }
    }



}
