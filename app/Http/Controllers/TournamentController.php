<?php

namespace App\Http\Controllers;

use App\Models\Tournament;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class TournamentController extends Controller
{

    public function __construct()
    {
        $this->middleware('auth');
    }

    public function index()
    {
        $user = auth::user();
        $tournaments = $user->tournaments()->get();

        foreach ($tournaments as $tournament) {
            if ($tournament->participants != null) {
                $tournament->participants_count = count(json_decode($tournament->participants));
            } else {
                $tournament->participants_count = 0;
            }
        }

        return view('tournaments.index', ['active_menu' => 'tournament', 'tournaments' => $tournaments]);
    }

    public function create()
    {
        return view('tournaments.create', ['active_menu' => 'tournament']);
    }

    public function store(Request $request)
    {
        $safeData = $request->validate([
            'tournament_title' => 'required',
        ]);

        $user = auth::user();

        $tournament = $user->tournaments()->create([
            'title' => $request->tournament_title,
            'field' => $request->tournament_field,
            'description' => $request->tournament_desc
        ]);

        return redirect()->route('tournaments.show', ['tournament' => $tournament]);

    }

    public function show($id)
    {
        $tournament = Tournament::where('id', $id)->firstOrFail();
        $participants = json_decode($tournament->participants);
        $tables = $tournament->tables()->get();

        $tournament->status = 'raw';

        foreach ($tables as $table) {
            if ($table->status == 'started') {
                $tournament->status = 'started';
            } elseif($table->status == 'raw') {
                $tournament->status = 'raw';
            } else {
                $tournament->status = 'finished';
            }
        }

        return view('tournaments.show', ['active_menu' => 'tournament', 'tournament' => $tournament, 'participants' => $participants, 'tables' => $tables]);
    }

    public function update(Request $request, $id)
    {

        $tournament = Tournament::where('id', $id)->first();

        $tournament->update([
            'title' => $request->tournament_title,
            'field' => $request->tournament_field,
            'description' => $request->tournament_desc
        ]);

        return redirect()->route('tournaments.show', [$tournament->id]);
    }

    public function destroy($id)
    {
        $tournament = Tournament::where('id', $id)->first();
        $tables = $tournament->tables()->delete();
        DB::table('tournament_user')->where('tournament_id', '=', $tournament->id)->delete();
        $tournament->delete();

        return redirect()->route('tournaments.index');
    }
}
