<?php

namespace App\Http\Controllers\Master;

use App\Http\Controllers\Controller;
use App\Models\ZoneOdp;
use Exception;
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

            // $button .= ' <button class="btn btn-sm btn-success" data-id=' . $data->id . ' data-type="edit" data-route="' . route('unitproduk.edit', ['id' => $data->id]) . '" data-proses="' . route('unitproduk.update', ['id' => $data->id]) . '" data-bs-toggle="modal" data-bs-target="#modal8"
            //                 data-action="edit" data-title="Unit Produk" data-toggle="tooltip" data-placement="bottom" title="Edit Data"><i
            //                                             class="fas fa-pen "></i></button>';

            $button .= ' <button class="btn btn-sm btn-danger action" data-id=' . $data->id . ' data-type="delete" data-route="' . route('zone.delete', ['id' => $data->id]) . '" data-toggle="tooltip" data-placement="bottom" title="Delete Data"><i
                                                        class="fas fa-trash "></i></button>';
            return '<div class="d-flex gap-2">' . $button . '</div>';
        })->rawColumns(['action'])->make(true);
    }


    public function SyncData()
    {
        $client = new Client();

        $subdomain = env('SUBDOMAIN');
        $subdomainidnet = env('SUBDOMAINIDNET');
        $token = env('KEYSMARTOLT');
        $tokenIDNET = env('KEYSMARTOLTIDNET');

        if (!$subdomain || !$token) {
            return response()->json([
                'error' => 'API URL or Token not falid.'
            ], 500);
        }

        if (!$subdomainidnet || !$token) {
            return response()->json([
                'error' => 'API URL or Token not falid.'
            ], 500);
        }

        try {
            $csi = "https://{$subdomain}.smartolt.com/api/system/get_zones";
            $response = $client->get($csi, [
                'headers' => [
                    'X-Token' => $token,
                    'Accept' => 'application/json',
                ],
            ]);
            $idnet = "https://{$subdomainidnet}.smartolt.com/api/system/get_zones";
            $responseIDNET = $client->get($idnet, [
                'headers' => [
                    'X-Token' => $tokenIDNET,
                    'Accept' => 'application/json',
                ],
            ]);

            $data = json_decode($response->getBody()->getContents(), true);
            $dataIDNET = json_decode($responseIDNET->getBody()->getContents(), true);
            foreach ($data['response'] as &$item) {
                $item['type'] = 'csi';
            }

            foreach ($dataIDNET['response'] as &$item) {
                $item['type'] = 'idnet';
            }

            if (isset($data['response']) && isset($dataIDNET['response'])) {
                $mergeData = array_merge($data['response'], $dataIDNET['response']);
            } else {
                return response()->json([
                    'error' => 'Invalid data structure from API response.',
                ], 500);
            }


            foreach ($mergeData as $zone) {
                ZoneOdp::updateOrInsert(
                    ['zone_id' => $zone['id']],
                    [
                        'name' => $zone['name'] ?? null,
                        'zone_id' => $zone['id'] ?? null,
                        'type' => $zone['type'],
                        'created_at' => now(),
                    ]
                );
            }

            return response()->json([
                'status' => 'Success',
                'message' => 'Data synced successfully',
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'error' => 'Unable to fetch data from Smart OLT API.',
                'message' => $e->getMessage()
            ], 500);
        }
    }



      //destroy data
      public function destroy($id)
      {
          try {
              $zone = ZoneOdp::findOrFail($id);
              $zone->delete();
              //return response
              return response()->json([
                  'status' => 'success',
                  'success' => true,
                  'message' => 'Data Zone Odp Berhasil Dihapus!.',
              ]);
          } catch (Exception $e) {
              return response()->json([
                  'message' => 'Gagal Menghapus Data Zone Odp!',
                  'trace' => $e->getTrace()
              ]);
          }
      }
}
