<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Office;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class OfficeController extends Controller
{
    public function setting()
    {
        $office = Office::firstOrCreate([], [
            'name' => 'PT Monica Sejahtera',
            'address' => 'Jl. Danau Tamblingan, Sanur, Denpasar Selatan, Kota Denpasar, Bali',
            'phone' => '0361 289092',
        ]);

        return view('admin.offices.setting', compact('office'));
    }

    public function update(Request $request)
    {
        $data = $request->validate([
            'name' => 'required|string|max:255',
            'address' => 'required|string|max:500',
            'phone' => 'required|string|max:50',
            'cdd_threshold' => 'nullable|numeric|min:0',
            'logo' => 'nullable|image|max:2048',
        ]);

        $office = Office::firstOrCreate([]);

        if ($request->hasFile('logo')) {
            if ($office->logo) {
                Storage::disk('public')->delete($office->logo);
            }
            $data['logo'] = $request->file('logo')->store('office', 'public');
        }

        $office->update($data);

        return redirect()->route('admin.office.setting')->with('success', 'Data kantor berhasil disimpan.');
    }
}
