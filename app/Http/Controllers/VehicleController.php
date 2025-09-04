<?php

namespace App\Http\Controllers;

use App\Models\Vehicle;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class VehicleController extends Controller
{
    public function index()
    {
        $vehicles = Vehicle::all();
        return response()->json([
            'status' => 'success',
            'data' => $vehicles
        ], 200);
    }

    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'merk' => 'required|string|max:50',
            'model' => 'required|string|max:50',
            'tahun' => 'required|digits:4|integer|min:1900|max:' . (date('Y') + 1),
            'harga' => 'required|numeric|min:0',
            'deskripsi' => 'nullable|string',
            'stok' => 'required|integer|min:0',
            'gambar_url' => 'nullable|string'
        ]);

        if ($validator->fails()) {
            return response()->json([
                'status' => 'error',
                'errors' => $validator->errors()
            ], 422);
        }

        $vehicle = Vehicle::create($request->all());
        return response()->json([
            'status' => 'success',
            'data' => $vehicle
        ], 201);
    }

    public function show($id)
    {
        $vehicle = Vehicle::find($id);

        if (!$vehicle) {
            return response()->json([
                'status' => 'error',
                'message' => 'Vehicle not found'
            ], 404);
        }

        return response()->json([
            'status' => 'success',
            'data' => $vehicle
        ], 200);
    }

    public function update(Request $request, $id)
    {
        $vehicle = Vehicle::find($id);

        if (!$vehicle) {
            return response()->json([
                'status' => 'error',
                'message' => 'Vehicle not found'
            ], 404);
        }

        $validator = Validator::make($request->all(), [
            'merk' => 'sometimes|required|string|max:50',
            'model' => 'sometimes|required|string|max:50',
            'tahun' => 'sometimes|required|digits:4|integer|min:1900|max:' . (date('Y') + 1),
            'harga' => 'sometimes|required|numeric|min:0',
            'deskripsi' => 'nullable|string',
            'stok' => 'sometimes|required|integer|min:0',
            'gambar_url' => 'nullable|string'
        ]);

        if ($validator->fails()) {
            return response()->json([
                'status' => 'error',
                'errors' => $validator->errors()
            ], 422);
        }

        $vehicle->update($request->all());
        return response()->json([
            'status' => 'success',
            'data' => $vehicle
        ], 200);
    }

    public function destroy($id)
    {
        $vehicle = Vehicle::find($id);

        if (!$vehicle) {
            return response()->json([
                'status' => 'error',
                'message' => 'Vehicle not found'
            ], 404);
        }

        $vehicle->delete();
        return response()->json([
            'status' => 'success',
            'message' => 'Vehicle deleted successfully'
        ], 200);
    }
}
