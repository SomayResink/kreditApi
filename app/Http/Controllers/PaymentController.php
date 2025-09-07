<?php

namespace App\Http\Controllers;

use App\Models\Payment;
use App\Models\Credit;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class PaymentController extends Controller
{
    // Tampilkan semua payment milik user login
    public function index(Request $request)
    {
        $payments = Payment::with('credit')
            ->whereHas('credit', function ($q) use ($request) {
                $q->where('user_id', $request->user()->id);
            })
            ->get();

        return response()->json([
            'status' => 'success',
            'data' => $payments
        ], 200);
    }

    // Simpan payment baru
  public function store(Request $request)
{
    $validator = Validator::make($request->all(), [
        'kode_kredit' => 'required|exists:credits,kode_kredit',
        'metode'      => 'required|in:cash,transfer',
        'bukti_url'   => 'nullable|string'
    ]);

    if ($validator->fails()) {
        return response()->json([
            'status' => 'error',
            'errors' => $validator->errors()
        ], 422);
    }

    // 🔍 Cari credit berdasarkan kode_kredit & user login
    $credit = Credit::where('kode_kredit', $request->kode_kredit)
        ->where('user_id', $request->user()->id)
        ->first();

    if (!$credit) {
        return response()->json([
            'status' => 'error',
            'message' => 'Kode kredit tidak valid untuk user ini'
        ], 404);
    }

    // Hitung tenor keberapa
    $tenorKe = $credit->payments()->count() + 1;

    // Simpan payment
    $payment = $credit->payments()->create([
        'tanggal_bayar' => now(),
        'jumlah_bayar'  => $credit->cicilan_per_bulan,
        'tenor_ke'      => $tenorKe,
        'metode'        => $request->metode,
        'bukti_url'     => $request->bukti_url,
        'status'        => 'paid',
    ]);

    // 🔄 Update kredit
    $totalDibayar = $credit->payments()->sum('jumlah_bayar');
    $credit->remaining_amount = max(0, $credit->total_bayar - $totalDibayar);
    $credit->remaining_tenor  = max(0, $credit->tenor - $credit->payments()->count());

    if ($credit->remaining_amount <= 0 || $credit->remaining_tenor <= 0) {
        $credit->status = 'paid';
    }

    $credit->save();

    return response()->json([
        'status' => 'success',
        'data'   => [
            'id'           => $payment->id,
            'tanggal_bayar'=> $payment->tanggal_bayar,
            'jumlah_bayar' => $payment->jumlah_bayar,
            'tenor_ke'     => $payment->tenor_ke,
            'metode'       => $payment->metode,
            'status'       => $payment->status,
            'kode_kredit'  => $credit->kode_kredit, // ✅ pakai kode_kredit, bukan credit_id
        ]
    ], 201);
}

    // Tampilkan payment tertentu milik user
    public function show(Request $request, $id)
    {
        $payment = Payment::with('credit')
            ->whereHas('credit', function ($q) use ($request) {
                $q->where('user_id', $request->user()->id);
            })
            ->find($id);

        if (!$payment) {
            return response()->json([
                'status' => 'error',
                'message' => 'Payment not found'
            ], 404);
        }

        return response()->json([
            'status' => 'success',
            'data' => $payment
        ], 200);
    }

    public function history(Request $request, $kodeKredit)
{
    $credit = Credit::where('kode_kredit', $kodeKredit)
        ->where('user_id', $request->user()->id)
        ->with('payments')
        ->first();

    if (!$credit) {
        return response()->json(['status' => 'error', 'message' => 'Kredit tidak ditemukan'], 404);
    }

    return response()->json([
        'status' => 'success',
        'data'   => [
            'kode_kredit' => $credit->kode_kredit,
            'vehicle_id'  => $credit->vehicle_id,
            'payments'    => $credit->payments
        ]
    ], 200);
}

}
