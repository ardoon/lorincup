<?php

namespace App\Http\Controllers;

use App\Models\Table;
use App\Models\Tournament;
use App\TableGenerator\Singles\Single;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Response;
use Illuminate\Support\Facades\Route;

class TableController extends Controller
{
    private $single;

    private $table;

    private $tournament;

    public function __construct()
    {
        $table_id = Route::current()->parameter('table');

        list($this->table, $this->tournament) = $this->getTournamentAndTable($table_id);

        if($this->tournament !== null) {
            $participants = json_decode($this->tournament->participants);

            $this->single = new Single($participants);
        }

//        $this->middleware('auth');
    }

    public function store(Request $request)
    {

        $safeData = $request->validate([
            'tournament_id' => 'required',
            'tournament_type' => 'required',
        ]);

        $tournament = Tournament::where('id', $request->tournament_id)->first();

        $table = $tournament->tables()->create([
            'type' => $request->tournament_type
        ]);

        return $table->id;

    }

    public function preview()
    {
        if ($this->table->status == 'started') {
            $this->tournament->status = 'started';
        } else {
            $this->tournament->status = 'raw';
        }

        $preview_array = $this->single->makePreview();

        return Response::json($preview_array);
    }

    public function storeTableSchema($table_id)
    {
        DB::beginTransaction();
        try {
            $this->tournament->tables()->where('id', '!=', $this->table->id)->delete();
            $this->tournament->tables()->where('id', '!=', $this->table->id)->delete();

            $this->table->update(['status' => 'started']);

            $first_round = $this->single->makePreview();
            $schema = $this->single->storeSingleSchema($table_id, $first_round);

            $this->table->update([
                'schema' => json_encode($schema),
            ]);

            DB::commit();
        } catch (\Exception $exception) {
            DB::rollBack();
            return $exception;
        }

        return ['table' => $this->table->id];
    }

    public function getSchema()
    {
        return $this->table->schema;
    }

    public function win(Request $request, $id)
    {
        $schema = json_decode($this->table->schema, true);

        $schema[++$request->round][$request->game] = $request->title;

        $schema = json_encode($schema);

        $this->table->schema = $schema;

        $this->table->save();

        return $this->table;
    }

    private function getTournamentAndTable($id): array
    {
        $table = Table::where('id', $id)->first();
        if($table) {
            $tournament = $table->tournament()->first();
            return array($table, $tournament);
        } else {
            return [null, null];
        }
    }
}
