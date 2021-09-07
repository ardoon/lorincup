<?php

namespace App\Http\Controllers;

use App\Models\Table;
use App\Models\Tournament;
use App\TableGenerator\Singles\Single;
use Illuminate\Http\Request;

class TableController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
    }

    public function store(Request $request)
    {

        $safeData = $request->validate([
            'tournament_id' => 'required',
            'tournament_type' => 'required',
        ]);

        $tournament = Tournament::where('id', $request->tournament_id)->first();
        $table = $tournament->tables()->create([
            'type' => $request->tournament_type,
        ]);

        return $table->id;

    }

    public function show($id)
    {
        $table = Table::where('id', $id)->firstOrfail();
        $tournament = $table->tournament()->first();

        if ($table->status == 'started') {
            $tournament->status = 'started';
        } else {
            $tournament->status = 'raw';
        }

        $participants = json_decode($tournament->participants);

        $single = new Single($participants);
        $preview_array = $single->makePreview();
    }
}
