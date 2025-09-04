<?php

namespace App\Http\Controllers;

use App\Models\Credit;
use App\Models\Vehicle;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class CreditController extends Controller
{
    // Ambil semua credit milik user yang login
    public function index(Request $request)
    {
        $credits = Credit::with(['motor'])
            ->where('user_id', $request->user()->id)
            ->get();

        return response()->json([
            'status' => 'success',
            'data' => $credits
        ], 200);
    }


public function store(Request $request)
{
    $validator = Validator::make($request->all(), [
        'vehicle_id' => 'required|exists:vehicles,id',
        'dp' => 'required|numeric|min:0',
        'tenor' => 'required|integer|min:1'
    ]);

    if ($validator->fails()) {
        return response()->json([
            'status' => 'error',
            'errors' => $validator->errors()
        ], 422);
    }

    $vehicle = Vehicle::find($request->vehicle_id);
    $jumlahPinjaman = $vehicle->harga;
    $dp = $request->dp;
    $tenor = $request->tenor;

    // Tentukan bunga berdasarkan tenor
    if ($tenor <= 6) {
        $bungaPersen = 5;
    } elseif ($tenor <= 12) {
        $bungaPersen = 10;
    } else {
        $bungaPersen = 15;
    }

    // Hitung sisa pinjaman + bunga
    $sisaPinjaman = $jumlahPinjaman - $dp;
    $bunga = ($sisaPinjaman * $bungaPersen) / 100;
    $totalBayar = $dp + $sisaPinjaman + $bunga;
    $cicilanPerBulan = ($sisaPinjaman + $bunga) / $tenor;

    $data = [
        'kode_kredit' => Credit::generateKode(),
        'user_id' => $request->user()->id,
        'vehicle_id' => $request->vehicle_id,
        'jumlah_pinjaman' => $jumlahPinjaman,
        'dp' => $dp,
        'tenor' => $tenor,
        'bunga_persen' => $bungaPersen,
        'cicilan_per_bulan' => $cicilanPerBulan,
        'total_bayar' => $totalBayar,
        'remaining_amount' => $sisaPinjaman + $bunga,
        'remaining_tenor' => $tenor,
        'tanggal_pengajuan' => now(),
        'status' => 'pending'
    ];

    $credit = Credit::create($data);

    // Kurangi stok motor
    $vehicle->stok -= 1;
    $vehicle->save();

    $credit->load(['motor']);

    return response()->json([
        'status' => 'success',
        'data' => $credit
    ], 201);
}


    // Update credit
    public function update(Request $request, $id)
    {
        $credit = Credit::find($id);

        if (!$credit) {
            return response()->json([
                'status' => 'error',
                'message' => 'Credit not found'
            ], 404);
        }

        if ($credit->user_id !== $request->user()->id) {
            return response()->json([
                'status' => 'error',
                'message' => 'Unauthorized'
            ], 403);
        }

        $validator = Validator::make($request->all(), [
            'dp' => 'sometimes|required|numeric|min:0',
            'tenor' => 'sometimes|required|integer|min:1',
            'status' => 'sometimes|in:pending,approved,paid'
        ]);

        if ($validator->fails()) {
            return response()->json([
                'status' => 'error',
                'errors' => $validator->errors()
            ], 422);
        }

        $dp = $request->dp ?? $credit->dp;
        $tenor = $request->tenor ?? $credit->tenor;
        $jumlahPinjaman = $credit->jumlah_pinjaman; // tetap dari harga kendaraan

        $sisaPinjaman = $jumlahPinjaman - $dp;
        $cicilanPerBulan = $sisaPinjaman / $tenor;
        $totalBayar = $dp + $sisaPinjaman;

        $credit->update([
            'dp' => $dp,
            'tenor' => $tenor,
            'cicilan_per_bulan' => $cicilanPerBulan,
            'total_bayar' => $totalBayar,
            'remaining_amount' => $sisaPinjaman,
            'remaining_tenor' => $tenor,
            'status' => $request->status ?? $credit->status
        ]);

        $credit->load(['motor']);

        return response()->json([
            'status' => 'success',
            'data' => $credit
        ], 200);
    }
}
