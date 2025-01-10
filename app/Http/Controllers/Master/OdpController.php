<?php

namespace App\Http\Controllers\Master;

use App\Http\Controllers\Controller;
use App\Models\Odp;
use App\Models\ZoneOdp;
use Exception;
use GuzzleHttp\Client;
use GuzzleHttp\Exception\RequestException;
use Illuminate\Http\Request;
use Yajra\DataTables\Facades\DataTables;

class OdpController extends Controller
{
    public function index()
    {
        $data = [
            'title' => 'Odp',
        ];
        return view('pages.master.odp.index', $data);
    }

    public function getData()
    {
        $data = Odp::orderByDesc('id')->get();
        return DataTables::of($data)
            ->addIndexColumn()
            ->editColumn('zone', function ($data) {
                return $data->zone->name;
            })
            ->addColumn('action', function ($data) {
                // $userauth = User::with('roles')->where('id', Auth::id())->first();
                $button = '';
                // $button .= ' <a href="' . route('dashboard') . '" class="btn btn-sm btn-success action mr-1" data-id=' . $data->id . ' data-type="edit" data-toggle="tooltip" data-placement="bottom" title="Edit Data"><i
                //                                             class="fas fa-pen "></i></a>';
    
                $button .= ' <button class="btn btn-sm btn-success" data-id=' . $data->id . ' data-type="edit" data-route="' . route('odp.edit', ['id' => $data->id]) . '" data-proses="' . route('odp.update', ['id' => $data->id]) . '" data-bs-toggle="modal" data-bs-target="#modal8"
                            data-action="edit" data-title="Unit Produk" data-toggle="tooltip" data-placement="bottom" title="Edit Data"><i
                                                        class="fas fa-pen "></i></button>';

                $button .= ' <button class="btn btn-sm btn-danger action" data-id=' . $data->id . ' data-type="delete" data-route="' . route('odp.delete', ['id' => $data->id]) . '" data-toggle="tooltip" data-placement="bottom" title="Delete Data"><i
                                                        class="fas fa-trash "></i></button>';
                return '<div class="d-flex gap-2">' . $button . '</div>';
            })->rawColumns(['action'])->make(true);
    }



    public function SyncData()
    {
        $client = new Client();

        $zones = ZoneOdp::all();

        $subdomain = env('SUBDOMAIN');
        $subdomainidnet = env('SUBDOMAINIDNET');
        $token = env('KEYSMARTOLT');
        $tokenIDNET = env('KEYSMARTOLTIDNET');

        if (!$subdomain || !$subdomainidnet || !$token || !$tokenIDNET) {
            return response()->json([
                'error' => 'API URL or Token not valid. Please check the configuration.'
            ], 500);
        }

        $successCount = 0;
        $errorCount = 0;

        foreach ($zones as $zone) {
            $apiUrl = $zone['type'] == 'csi'
                ? "https://{$subdomain}.smartolt.com/api/system/get_odbs/{$zone['zone_id']}"
                : "https://{$subdomainidnet}.smartolt.com/api/system/get_odbs/{$zone['zone_id']}";

            $apiToken = $zone['type'] == 'csi' ? $token : $tokenIDNET;

            try {
                $response = $this->fetchOdpData($client, $apiUrl, $apiToken);

                if (is_array($response) && empty($response)) {
                    continue;
                }

                foreach ($response as $odp) {
                    Odp::updateOrInsert(
                        ['odp_id' => $odp['id']],
                        [
                            'name' => $odp['name'],
                            'odp_id' => $odp['id'],
                            'latitude' => $odp['latitude'] ?? null,
                            'longitude' => $odp['longitude'] ?? null,
                            'zone_id' => $zone['id'],
                            'updated_at' => now(),
                        ]
                    );
                }
                $successCount++;

            } catch (RequestException $e) {
                if ($e->getCode() == 403) {
                    sleep(10);
                    $response = $this->fetchOdpData($client, $apiUrl, $apiToken);
                    if (!empty($response)) {
                        foreach ($response as $odp) {
                            Odp::updateOrInsert(
                                ['odp_id' => $odp['id']],
                                [
                                    'name' => $odp['name'],
                                    'odp_id' => $odp['id'],
                                    'latitude' => $odp['latitude'] ?? null,
                                    'longitude' => $odp['longitude'] ?? null,
                                    'zone_id' => $zone['id'],
                                    'updated_at' => now(),
                                ]
                            );
                        }
                        $successCount++;
                    } else {
                        $errorCount++;
                    }
                } else {
                    $errorCount++;
                }
            } catch (Exception $e) {
                $errorCount++;
            }
        }

        return response()->json([
            'status' => 'Success',
            'message' => 'Data synced successfully.',
            'success_count' => $successCount,
            'error_count' => $errorCount,
        ]);
    }

    private function fetchOdpData(Client $client, string $apiUrl, string $apiToken)
    {
        $response = $client->get($apiUrl, [
            'headers' => [
                'X-Token' => $apiToken,
                'Accept' => 'application/json',
            ],
        ]);

        $data = json_decode($response->getBody()->getContents(), true);

        return isset($data['response']) ? $data['response'] : [];
    }

}
