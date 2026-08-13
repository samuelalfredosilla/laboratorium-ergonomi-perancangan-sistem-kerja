<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\HomeSlider;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class HomeSliderController extends Controller
{
    public function index(Request $request)
    {
        $sliders = HomeSlider::orderBy('sort_order')->orderBy('id')->get();

        return view('admin.sliders.index', compact('sliders'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'title' => ['nullable', 'string', 'max:255'],
            'image' => ['required', 'image', 'max:4096'],
        ]);

        HomeSlider::create([
            'title' => $validated['title'] ?? null,
            'image_path' => $request->file('image')->store('sliders', 'public'),
            'is_active' => $request->boolean('is_active'),
            'sort_order' => (int) HomeSlider::max('sort_order') + 1,
        ]);

        return redirect()->route('admin.sliders.index')->with('success', 'Slider berhasil ditambahkan.');
    }

    public function update(Request $request, HomeSlider $slider)
    {
        $validated = $request->validate([
            'title' => ['nullable', 'string', 'max:255'],
            'image' => ['nullable', 'image', 'max:4096'],
        ]);

        $data = [
            'title' => $validated['title'] ?? null,
            'is_active' => $request->boolean('is_active'),
        ];

        if ($request->hasFile('image')) {
            Storage::disk('public')->delete($slider->image_path);
            $data['image_path'] = $request->file('image')->store('sliders', 'public');
        }

        $slider->update($data);

        return redirect()->route('admin.sliders.index')->with('success', 'Slider berhasil diperbarui.');
    }

    public function destroy(HomeSlider $slider)
    {
        Storage::disk('public')->delete($slider->image_path);
        $slider->delete();

        return redirect()->route('admin.sliders.index')->with('success', 'Slider berhasil dihapus.');
    }

    public function toggle(HomeSlider $slider)
    {
        $slider->update(['is_active' => ! $slider->is_active]);

        return back()->with('success', $slider->is_active ? 'Slider diaktifkan.' : 'Slider dinonaktifkan.');
    }

    public function reorder(Request $request)
    {
        $validated = $request->validate([
            'order' => ['required', 'array'],
            'order.*' => ['integer', 'exists:home_sliders,id'],
        ]);

        foreach ($validated['order'] as $index => $id) {
            HomeSlider::where('id', $id)->update(['sort_order' => $index + 1]);
        }

        return response()->json(['status' => 'ok']);
    }
}
