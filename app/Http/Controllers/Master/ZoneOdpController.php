<?php

namespace App\Http\Controllers\Master;

use App\Http\Controllers\Controller;
use App\Models\ZoneOdp;
use GuzzleHttp\Client;
use Illuminate\Http\Request;
use Yajra\DataTables\DataTables;

class ZoneOdpController extends Controller
{
    public function index()
    {
        $data = [
            'title' => 'Jalur',
        ];
        return view('pages.master.zoneodp.index', $data);
    }

    //getdata
    public function getData()
    {
        $data = ZoneOdp::orderByDesc('id')->get();
        return DataTables::of($data)->addIndexColumn()->addColumn('action', function ($data) {
            // $userauth = User::with('roles')->where('id', Auth::id())->first();
            $button = '';
            // $button .= ' <a href="' . route('dashboard') . '" class="btn btn-sm btn-success action mr-1" data-id=' . $data->id . ' data-type="edit" data-toggle="tooltip" data-placement="bottom" title="Edit Data"><i
            //                                             class="fas fa-pen "></i></a>';

            $button .= ' <button class="btn btn-sm btn-success" data-id=' . $data->id . ' data-type="edit" data-route="' . route('unitproduk.edit', ['id' => $data->id]) . '" data-proses="' . route('unitproduk.update', ['id' => $data->id]) . '" data-bs-toggle="modal" data-bs-target="#modal8"
                            data-action="edit" data-title="Unit Produk" data-toggle="tooltip" data-placement="bottom" title="Edit Data"><i
                                                        class="fas fa-pen "></i></button>';

            $button .= ' <button class="btn btn-sm btn-danger action" data-id=' . $data->id . ' data-type="delete" data-route="' . route('unitproduk.delete', ['id' => $data->id]) . '" data-toggle="tooltip" data-placement="bottom" title="Delete Data"><i
                                                        class="fas fa-trash "></i></button>';
            return '<div class="d-flex gap-2">' . $button . '</div>';
        })->rawColumns(['action'])->make(true);
    }


    public function SyncData()
    {
        $client = new Client();

        $csi = env('SUBDOMAIN') . 'system/get_zones';
        $idnet = env('SUBDOMAINIDNET') . 'system/get_zones';
        $token = env('KEYSMARTOLT');
        $tokenIDNET = env('KEYSMARTOLTIDNET');

        if (!$csi || !$token) {
            return response()->json([
                'error' => 'API URL or Token not falid.'
            ], 500);
        }

        if (!$idnet || !$token) {
            return response()->json([
                'error' => 'API URL or Token not falid.'
            ], 500);
        }

        try {
        
            $response = $client->get($csi, [
                'headers' => [
                    'X-Token' => $token,
                    'Accept' => 'application/json',
                ],
            ]);

            $responseIDNET = $client->get($idnet, [
                'headers' => [
                    'X-Token' => $tokenIDNET,
                    'Accept' => 'application/json',
                ],
            ]);

            $data = json_decode($response->getBody()->getContents(), true);
            $dataIDNET = json_decode($responseIDNET->getBody()->getContents(), true);
            $mergeData = array_merge($data['response'],$dataIDNET['response']);

            if (is_array($mergeData) && isset($mergeData)) {
                 // Misalnya, zona terletak di dalam key 'zones'

                foreach ($mergeData as $zone) {
                    ZoneOdp::updateOrInsert(
                        ['zone_id' => $zone['id']], // Cek berdasarkan zone_id
                        [
                            'name' => $zone['name'] ?? null,
                            'zone_id' => $zone['id'] ?? null,
                            'created_at' => now(),
                        ]
                    );
                }

                return response()->json([
                    'status' =>'Success',
                    'message' => 'Data synced successfully',
                ]);
            }

            return response()->json([
                'error' => 'Invalid data structure from API response.',
            ], 500);

        } catch (\Exception $e) {
            // Menangani error jika terjadi masalah dengan API request
            return response()->json([
                'error' => 'Unable to fetch data from Smart OLT API.',
                'message' => $e->getMessage()
            ], 500);
        }
    }
}
