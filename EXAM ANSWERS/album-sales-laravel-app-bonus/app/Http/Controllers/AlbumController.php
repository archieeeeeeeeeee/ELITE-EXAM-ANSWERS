<?php

namespace App\Http\Controllers;

use App\Models\Album;
use Illuminate\Http\Request;

class AlbumController extends Controller
{
    public function index()
    {
        return response()->json(Album::with('artist')->get());
    }

    public function show($id)
    {
        $album = Album::with('artist')->find($id);
        if (!$album) {
            return response()->json(['error' => 'Album not found'], 404);
        }
        return response()->json($album);
    }

    public function store(Request $request)
    {
        $request->validate([
            'year' => 'required|integer',
            'name' => 'required|string',
            'sales' => 'required|integer',
            'album_cover_path' => 'nullable|string',
            'artist_id' => 'required|exists:artists,id',
        ]);

        $album = Album::create($request->all());
        return response()->json($album, 201);
    }

    public function update(Request $request, $id)
    {
        $album = Album::find($id);
        if (!$album) {
            return response()->json(['error' => 'Album not found'], 404);
        }

        $request->validate([
            'year' => 'integer',
            'name' => 'string',
            'sales' => 'integer',
            'album_cover_path' => 'string|nullable',
            'artist_id' => 'exists:artists,id',
        ]);

        $album->update($request->all());
        return response()->json($album);
    }

    public function destroy($id)
    {
        $album = Album::find($id);
        if (!$album) {
            return response()->json(['error' => 'Album not found'], 404);
        }

        $album->delete();
        return response()->json(null, 204);
    }
}
