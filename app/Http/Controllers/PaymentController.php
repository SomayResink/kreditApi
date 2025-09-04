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
        'metode' => 'required|in:cash,transfer',
        'bukti_url' => 'nullable|string'
    ]);

    if ($validator->fails()) {
        return response()->json([
            'status' => 'error',
            'errors' => $validator->errors()
        ], 422);
    }

    // Cari kredit aktif milik user login
    $credit = Credit::where('user_id', $request->user()->id)
        ->whereIn('status', ['approved']) // hanya ambil kredit yg masih jalan
        ->first();

    if (!$credit) {
        return response()->json([
            'status' => 'error',
            'message' => 'Tidak ada kredit aktif untuk user ini'
        ], 404);
    }

    // Tentukan tenor_ke
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

    // Update kredit
    $totalDibayar = $credit->payments()->sum('jumlah_bayar');
    $credit->remaining_amount = max(0, $credit->jumlah_pinjaman - $credit->dp - $totalDibayar);
    $credit->remaining_tenor  = max(0, $credit->tenor - $credit->payments()->count());

    if ($credit->remaining_amount <= 0 || $credit->remaining_tenor <= 0) {
        $credit->status = 'paid';
    }

    $credit->save();

    return response()->json([
        'status' => 'success',
        'data'   => $payment->load('credit')
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
}
