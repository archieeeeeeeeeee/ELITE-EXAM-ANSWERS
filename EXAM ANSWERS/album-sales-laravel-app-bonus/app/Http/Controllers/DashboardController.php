<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Artist;
use App\Models\Album;
use Illuminate\Support\Facades\DB;

class DashboardController extends Controller
{
    public function totalAlbumsSoldPerArtist()
    {
        $data = Artist::withCount(['albums as total_albums_sold' => function ($query) {
            $query->select(DB::raw("SUM(sales)"));
        }])->get();

        return response()->json($data);
    }

    public function combinedAlbumSalesPerArtist()
    {
        $data = Artist::withSum('albums', 'sales')->get();

        return response()->json($data);
    }

    public function topArtistByCombinedSales()
    {
        $data = Artist::withSum('albums', 'sales')
            ->orderByDesc('albums_sum_sales')
            ->first();

        return response()->json($data);
    }

    public function albumsBySearchedArtist(Request $request)
    {
        $artistName = $request->query('artist_name');

        if (!$artistName) {
            return response()->json(['error' => 'artist_name query parameter is required'], 400);
        }

        $artist = Artist::where('name', 'like', '%' . $artistName . '%')->first();

        if (!$artist) {
            return response()->json(['error' => 'Artist not found'], 404);
        }

        $albums = $artist->albums;

        return response()->json($albums);
    }
}
