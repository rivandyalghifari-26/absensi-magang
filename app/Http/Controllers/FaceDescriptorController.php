<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Intern;
use Illuminate\Http\JsonResponse;

class FaceDescriptorController extends Controller
{
    public function store(Request $request, Intern $intern): JsonResponse {
        $request->validate([
            'descriptor' => 'required|array|size:128',

        ],[
            'descriptor.required' => 'Data wajah diperlukan',
        ]);
       $intern->update([
        'face_descriptor' => json_encode($request->descriptor)
       ]);
       return response()->json([
        'message' => 'Data wajah tersimpan'
       ]);
    }
    public function destroy(Intern $intern): JsonResponse {
        $intern->update([
            'face_descriptor' => null
        ]);
        return response()->json([
            'message' => 'Data wajah berhasil dihapus'
        ]);
    }
}
