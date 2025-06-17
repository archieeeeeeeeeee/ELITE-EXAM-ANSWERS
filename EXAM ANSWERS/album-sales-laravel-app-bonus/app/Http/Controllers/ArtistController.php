<?php

namespace App\Http\Controllers;

use App\Models\Artist;
use Illuminate\Http\Request;

class ArtistController extends Controller
{
    public function index()
    {
        return response()->json(Artist::all());
    }

    public function show($id)
    {
        $artist = Artist::find($id);
        if (!$artist) {
            return response()->json(['error' => 'Artist not found'], 404);
        }
        return response()->json($artist);
    }

    public function store(Request $request)
    {
        $request->validate([
            'code' => 'required|string|unique:artists,code',
            'name' => 'required|string',
        ]);

        $artist = Artist::create($request->all());
        return response()->json($artist, 201);
    }

    public function update(Request $request, $id)
    {
        $artist = Artist::find($id);
        if (!$artist) {
            return response()->json(['error' => 'Artist not found'], 404);
        }

        $request->validate([
            'code' => 'string|unique:artists,code,' . $id,
            'name' => 'string',
        ]);

        $artist->update($request->all());
        return response()->json($artist);
    }

    public function destroy($id)
    {
        $artist = Artist::find($id);
        if (!$artist) {
            return response()->json(['error' => 'Artist not found'], 404);
        }

        $artist->delete();
        return response()->json(null, 204);
    }
}
