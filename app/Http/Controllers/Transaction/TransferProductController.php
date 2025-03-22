<?php

namespace App\Http\Controllers\Transaction;

use App\Http\Controllers\Controller;
use App\Models\Assign;
use App\Models\Branch;
use App\Models\Product;
use App\Models\Transaction;
use App\Models\TransactionProduct;
use App\Models\User;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use Yajra\DataTables\Facades\DataTables;

class TransferProductController extends Controller
{
    /**
     * Display transfer list page
     */
    public function index()
    {
        $data = [
            'title' => 'Pemindahan Barang',
        ];

        return view('pages.report.transferproduct.index', $data);
    }

    /**
     * Get data for DataTables
     */
    public function getData(Request $request)
    {
        $userauth = User::with('roles')->where('id', Auth::id())->first();

        // Base query with common relations
        $query = Transaction::with(['branch', 'tobranch', 'Transactionproduct.product', 'assign'])
            ->where('purpose', 'transfer')
            ->where('type', 'out')
            ->orderByDesc('created_at');

        // Apply role-based
        if (!$userauth->hasRole(['Developer', 'Administrator'])) {
            $query->whereHas('assign', function ($q) {
                $q->where('technitian_id', Auth::id());
            });
        }

        // Apply date filter
        if ($request->filled('created_at')) {
            $query->whereDate('created_at', $request->input('created_at'));
        }

        $data = $query->get();

        return DataTables::of($data)
            ->addIndexColumn()
            ->addColumn('from_branch', function ($row) {
                return $row->branch->name ?? '-';
            })
            ->addColumn('to_branch', function ($row) {
                return $row->tobranch->name ?? '-';
            })
            ->addColumn('products', function ($row) {
                $products = [];
                foreach ($row->Transactionproduct as $tp) {
                    $unit = $tp->product->unit->name ?? '';
                    $products[] = $tp->product->name . ' (' . $tp->quantity . ' ' . $unit . ')';
                }
                return implode('<br>', $products);
            })
            ->addColumn('date', function ($row) {
                return $row->created_at->format('d M Y H:i');
            })
            ->addColumn('action', function ($row) {
                $userauth = User::with('roles')->where('id', Auth::id())->first();

                $button = '';
                if ($userauth->can('read-transfer-product')) {
                    $button .= '<a href="' . route('transfer.details', ['id' => $row->id]) . '"
                      class="btn btn-sm btn-info"
                       data-id="' . $row->id . '"
                       data-type="details"
                       data-toggle="tooltip"
                       data-placement="bottom"
                       title="Details">
                       <i class="fas fa-eye"></i>
                   </a>';
                }
                if ($userauth->can('update-transfer-product')) {
                    $button .= '<a href="' . route('transfer.edit', ['id' => $row->id]) . '"
                      class="btn btn-sm btn-success"
                       data-id="' . $row->id . '"
                       data-type="edit"
                       data-toggle="tooltip"
                       data-placement="bottom"
                       title="Edit Data">
                       <i class="fas fa-pen"></i>
                   </a>';
                }
                if ($userauth->can('delete-transfer-product')) {
                    $button .= ' <button class="btn btn-sm btn-danger action"
                            data-id="' . $row->id . '"
                            data-type="delete"
                            data-route="' . route('transfer.delete', ['id' => $row->id]) . '"
                            data-toggle="tooltip"
                            data-placement="bottom"
                            title="Delete Data">
                        <i class="fas fa-trash-alt"></i>
                    </button>';
                }
                return '<div class="d-flex gap-2">' . $button . '</div>';
            })
            ->rawColumns(['action', 'from_branch', 'to_branch', 'products', 'type', 'date'])
            ->make(true);
    }

    /**
     * Show transfer creation form
     */
    public function create()
    {
        $data = [
            'title' => 'Tambah Pemindahan Barang',
            'branch' => Branch::all(),
            'product' => Product::all(),
            'technitians' => User::with('employee.position', 'roles')
                ->whereHas('employee.position', function ($query) {
                    $query->where('name', 'Technician');  // Filter by position name
                })
                ->orderByDesc('id')
                ->get()
        ];

        return view('pages.report.transferproduct.add', $data);
    }

    /**
     * Store new transfer
     */
    public function store(Request $request)
    {
        $request->validate([
            'from_branch' => 'required|exists:branches,id',
            'to_branch' => 'required|exists:branches,id|different:from_branch',
            'products' => 'required|array',
            'products.*.id' => 'required|exists:products,id',
            'products.*.quantity' => 'required|integer|min:1',
            'technitian_id' => 'required|exists:users,id', // Validasi technitian_id
        ]);

        try {
            DB::beginTransaction();

            // Create transfer transaction (out)
            $transfer = Transaction::create([
                'branch_id' => $request->from_branch,
                'to_branch' => $request->to_branch,
                'type' => 'out',
                'user_id' => Auth::user()->id,
                'purpose' => 'transfer'
            ]);

            // Add products to transfer
            foreach ($request->products as $product) {
                TransactionProduct::create([
                    'transaction_id' => $transfer->id,
                    'product_id' => $product['id'],
                    'quantity' => $product['quantity']
                ]);
            }

            // Save owner signature
            $ownerSignaturePath = $this->saveSignature($request->owner_signature, 'owner');

            // Save technitian signature
            $technitianSignaturePath = $this->saveSignature($request->technitian_signature, 'technitian');

            // Create Assign
            $assign = Assign::create([
                'owner_id' => Auth::user()->id,
                'technitian_id' => $request->technitian_id,
                'owner_signature' => $ownerSignaturePath,
                'technitian_signature' => $technitianSignaturePath,
            ]);

            // Hubungkan Assign dengan Transaction
            $transfer->update(['assign_id' => $assign->id]);

            $fromBranch = Branch::find($request->from_branch);
            $toBranch = Branch::find($request->to_branch);

            activity()
                ->causedBy(Auth::user())
                ->performedOn($transfer)
                ->event('created')
                ->withProperties($transfer->toArray())
                ->log("Pemindahan Barang berhasil dari {$fromBranch->name} ke {$toBranch->name}.");

            DB::commit();

            return redirect()->route('transfer')->with([
                'status' => 'Success!',
                'message' => 'Berhasil Menambahkan Pemindahan Barang!'
            ]);
        } catch (Exception $e) {
            DB::rollBack();
            Log::error($e->getMessage());
            return redirect()->route('transfer')->with(['status' => 'Error!', 'message' => 'Gagal Menambahkan Pemindahan Barang!']);
        }
    }

    /**
     * Show transfer details
     */
    public function details($id)
    {
        $transfer = Transaction::with(['branch', 'tobranch', 'Transactionproduct.product', 'userTransaction', 'assign'])
            ->where('id', $id)
            ->where('purpose', 'transfer')
            ->where('type', 'out')
            ->firstOrFail();

        $data = [
            'title' => 'Pemindahan Barang',
            'transfer' => $transfer
        ];

        return view('pages.report.transferproduct.details', $data);
    }

    /**
     * Show edit transfer form
     */
    public function show($id)
    {
        $transfer = Transaction::with(['branch', 'tobranch', 'Transactionproduct.product', 'assign'])
            ->where('id', $id)
            ->where('purpose', 'transfer')
            ->where('type', 'out')
            ->firstOrFail();

        $data = [
            'title' => 'Edit Pemindahan Barang',
            'transfer' => $transfer,
            'branch' => Branch::all(),
            'product' => Product::all(),
            'technitians' => User::with('employee.position', 'roles')
                ->whereHas('employee.position', function ($query) {
                    $query->where('name', 'Technician');  // Filter by position name
                })
                ->orderByDesc('id')
                ->get()
        ];

        return view('pages.report.transferproduct.edit', $data);
    }

    /**
     * Update transfer
     */
    public function update(Request $request, $id)
    {
        $request->validate([
            'from_branch' => 'required|exists:branches,id',
            'to_branch' => 'required|exists:branches,id|different:from_branch',
            'products' => 'required|array',
            'products.*.id' => 'required|exists:products,id',
            'products.*.quantity' => 'required|integer|min:1',
            'technitian_id' => 'required|exists:users,id',
        ]);

        try {
            DB::beginTransaction();

            // Find the outgoing transfer
            $transfer = Transaction::where('id', $id)
                ->where('purpose', 'transfer')
                ->where('type', 'out')
                ->firstOrFail();

            $oldTransfer = $transfer->toArray();

            // Get branch names for logging
            $fromBranch = Branch::find($request->from_branch);
            $toBranch = Branch::find($request->to_branch);

            // Update transfer details
            $transfer->update([
                'branch_id' => $request->from_branch,
                'to_branch' => $request->to_branch
            ]);

            // Delete old products
            TransactionProduct::where('transaction_id', $transfer->id)->delete();

            // Add new products
            foreach ($request->products as $product) {
                TransactionProduct::create([
                    'transaction_id' => $transfer->id,
                    'product_id' => $product['id'],
                    'quantity' => $product['quantity']
                ]);
            }

            // Handle signatures
            if ($transfer->assign) {
                // Save new owner signature if provided
                if ($request->owner_signature) {
                    $ownerSignaturePath = $this->saveSignature($request->owner_signature, 'owner');
                    // Delete old signature file if it exists
                    if ($transfer->assign->owner_signature) {
                        Storage::disk('public')->delete($transfer->assign->owner_signature);
                    }
                    $transfer->assign->owner_signature = $ownerSignaturePath;
                }

                // Save new technitian signature if provided
                if ($request->technitian_signature) {
                    $technitianSignaturePath = $this->saveSignature($request->technitian_signature, 'technitian');
                    // Delete old signature file if it exists
                    if ($transfer->assign->technitian_signature) {
                        Storage::disk('public')->delete($transfer->assign->technitian_signature);
                    }
                    $transfer->assign->technitian_signature = $technitianSignaturePath;
                }

                // Update technitian_id
                $transfer->assign->technitian_id = $request->technitian_id;
                $transfer->assign->save();
            } else {
                // Create new Assign if it doesn't exist
                $ownerSignaturePath = $this->saveSignature($request->owner_signature, 'owner');
                $technitianSignaturePath = $this->saveSignature($request->technitian_signature, 'technitian');

                $assign = Assign::create([
                    'owner_id' => Auth::user()->id,
                    'technitian_id' => $request->technitian_id,
                    'owner_signature' => $ownerSignaturePath,
                    'technitian_signature' => $technitianSignaturePath,
                ]);

                $transfer->update(['assign_id' => $assign->id]);
            }

            // Reload the transfer to get the updated data
            $transfer = $transfer->fresh();

            activity()
                ->causedBy(Auth::user())
                ->performedOn($transfer)
                ->event('updated')
                ->withProperties([
                    'old_from_branch' => Branch::find($oldTransfer['branch_id'])->name ?? 'Unknown',
                    'old_to_branch' => Branch::find($oldTransfer['to_branch'])->name ?? 'Unknown',
                    'new_from_branch' => $fromBranch->name,
                    'new_to_branch' => $toBranch->name,
                    'transfer_id' => $transfer->id
                ])
                ->log("Pemindahan Barang berhasil diupdate dari {$fromBranch->name} ke {$toBranch->name}.");

            DB::commit();

            return redirect()->route('transfer')->with([
                'status' => 'Success!',
                'message' => 'Berhasil Mengupdate Pemindahan Barang!'
            ]);
        } catch (Exception $e) {
            DB::rollBack();
            Log::error($e->getMessage());
            return redirect()->route('transfer')->with([
                'status' => 'Error!',
                'message' => 'Gagal Mengupdate Pemindahan Barang!'
            ]);
        }
    }

    public function destroy($id)
    {
        try {
            DB::beginTransaction();

            // Find the outgoing transfer with its relationships
            $transfer = Transaction::with(['Transactionproduct', 'assign'])
                ->where('id', $id)
                ->where('purpose', 'transfer')
                ->where('type', 'out')
                ->firstOrFail();

            // Delete signature files if they exist
            if ($transfer->assign) {
                // Delete owner signature
                if ($transfer->assign->owner_signature) {
                    Storage::disk('public')->delete($transfer->assign->owner_signature);
                }

                // Delete technitian signature
                if ($transfer->assign->technitian_signature) {
                    Storage::disk('public')->delete($transfer->assign->technitian_signature);
                }

                // Delete the assign record
                $transfer->assign->delete();
            }

            // Delete all related transaction products
            TransactionProduct::where('transaction_id', $id)->delete();

            // Delete the outgoing transfer
            $transfer->delete();

            activity()
                ->causedBy(Auth::user())
                ->performedOn($transfer)
                ->event('deleted')
                ->withProperties($transfer->toArray())
                ->log("Pemindahan Barang berhasil dihapus.");

            DB::commit();
            return response()->json([
                'status' => 'success',
                'success' => true,
                'message' => 'Pemindahan Barang Berhasil Dihapus!'
            ]);
        } catch (Exception $e) {
            DB::rollBack();
            Log::error('Error deleting transfer: ' . $e->getMessage());
            return response()->json([
                'status' => 'error',
                'success' => false,
                'message' => 'Pemindahan Barang Gagal dihapus!',
                'error' => $e->getMessage()
            ], 500);
        }
    }
    private function saveSignature($base64Signature, $type)
    {
        try {
            // Check if the signature is empty or invalid
            if (empty($base64Signature)) {
                throw new Exception('Tanda tangan tidak boleh kosong');
            }

            // Check if it's a valid base64 image string
            if (!preg_match('/^data:image\/(\w+);base64,/', $base64Signature)) {
                throw new Exception('Format tanda tangan tidak valid');
            }

            // Get image data and decode base64
            $imageData = base64_decode(preg_replace('/^data:image\/\w+;base64,/', '', $base64Signature));

            if (!$imageData) {
                throw new Exception('Gagal decode data tanda tangan');
            }

            // Generate filename
            $filename = $type . '_signature_' . time() . '_' . Str::random(10) . '.png';
            $path = 'signatures/' . $filename;

            // Save image directly from binary data
            if (!Storage::disk('public')->put($path, $imageData)) {
                throw new Exception('Gagal menyimpan file tanda tangan');
            }

            return $path;
        } catch (Exception $e) {
            throw new Exception('Gagal menyimpan tanda tangan: ' . $e->getMessage());
        }
    }
}
