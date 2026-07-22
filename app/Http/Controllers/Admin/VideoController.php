<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Video;
use Illuminate\Http\Request;

class VideoController extends Controller
{
    public function index()
    {
        $videos = Video::orderBy('sort_order')->orderBy('id')->get();

        return view('admin.videos.index', compact('videos'));
    }

    public function create()
    {
        return view('admin.videos.create');
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'title' => 'required|string|max:255',
            'description' => 'nullable|string|max:1000',
            'youtube_url' => 'required|string|max:500',
            'is_active' => 'boolean',
        ]);

        $youtubeId = Video::extractYoutubeId($validated['youtube_url']);

        if (!$youtubeId) {
            return back()->withInput()->withErrors([
                'youtube_url' => 'Nu am putut recunoaște un link YouTube valid în adresa introdusă.',
            ]);
        }

        Video::create([
            'title' => $validated['title'],
            'description' => $validated['description'] ?? null,
            'youtube_id' => $youtubeId,
            'sort_order' => (Video::max('sort_order') ?? 0) + 1,
            'is_active' => $request->boolean('is_active'),
        ]);

        return redirect()->route('admin.videos.index')->with('success', 'Videoclipul a fost adăugat cu succes.');
    }

    public function edit($id)
    {
        $video = Video::findOrFail($id);

        return view('admin.videos.edit', compact('video'));
    }

    public function update(Request $request, $id)
    {
        $video = Video::findOrFail($id);

        $validated = $request->validate([
            'title' => 'required|string|max:255',
            'description' => 'nullable|string|max:1000',
            'youtube_url' => 'required|string|max:500',
            'is_active' => 'boolean',
        ]);

        $youtubeId = Video::extractYoutubeId($validated['youtube_url']);

        if (!$youtubeId) {
            return back()->withInput()->withErrors([
                'youtube_url' => 'Nu am putut recunoaște un link YouTube valid în adresa introdusă.',
            ]);
        }

        $video->update([
            'title' => $validated['title'],
            'description' => $validated['description'] ?? null,
            'youtube_id' => $youtubeId,
            'is_active' => $request->boolean('is_active'),
        ]);

        return redirect()->route('admin.videos.index')->with('success', 'Videoclipul a fost actualizat cu succes.');
    }

    public function destroy($id)
    {
        Video::findOrFail($id)->delete();

        return back()->with('success', 'Videoclipul a fost șters.');
    }

    public function toggleStatus($id)
    {
        $video = Video::findOrFail($id);
        $video->update(['is_active' => !$video->is_active]);

        return back()->with('success', $video->is_active ? 'Videoclipul e acum activ pe pagina principală.' : 'Videoclipul a fost dezactivat.');
    }

    public function moveUp($id)
    {
        $this->swapOrder($id, 'up');

        return back();
    }

    public function moveDown($id)
    {
        $this->swapOrder($id, 'down');

        return back();
    }

    private function swapOrder($id, string $direction): void
    {
        $video = Video::findOrFail($id);

        $neighbor = $direction === 'up'
            ? Video::where('sort_order', '<', $video->sort_order)->orderByDesc('sort_order')->first()
            : Video::where('sort_order', '>', $video->sort_order)->orderBy('sort_order')->first();

        if ($neighbor) {
            $videoOrder = $video->sort_order;
            $video->update(['sort_order' => $neighbor->sort_order]);
            $neighbor->update(['sort_order' => $videoOrder]);
        }
    }
}
