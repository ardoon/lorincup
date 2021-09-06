<?php

namespace App\Http\Controllers;

use App\Models\Table;
use App\Models\Tournament;
use Illuminate\Http\Request;
use Illuminate\Support\Arr;
use Illuminate\Support\Facades\Auth;

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

        switch ($table->type) {
            case 'single':
                $data = $this->single_preview(json_decode($tournament->participants));
                return view('tables.show-single', ['tournament' => $tournament, 'active_menu' => 'tournament', 'table' => $table, 'row1' => $data['row1'], 'bar' => $data['bar']]);
                break;
            case 'double':
                return view('tables.show-double', ['tournament' => $tournament, 'active_menu' => 'tournament', 'table' => $table, 'participants' => $participants]);
                break;
            case 'round':
                $data = $this->round_preview(json_decode($tournament->participants));
                return view('tables.show-round', ['tournament' => $tournament, 'active_menu' => 'tournament', 'table' => $table, 'participants' => $participants, 'groups' => $data['groups'], 'rounds' => $data['rounds'], 'groups_count' => $data['groups_count'], 'group_member_count' => $data['group_member_count']]);
                break;
        }

    }

    public function progress($id)
    {
        $table = Table::where('id', $id)->firstOrfail();
        $tournament = $table->tournament()->first();

        if ($table->status == 'started') {
            $tournament->status = 'started';
        } elseif ($table->status == 'raw') {
            $tournament->status = 'raw';
        } else {
            $tournament->status = 'finished';
        }

        switch ($table->type) {
            case 'single':
                $data = $this->single_get_schema($tournament->id);
                return view('tables.progress-single', ['table' => $table, 'tournament' => $tournament, 'rounds_count' => $data['rounds_count'], 'participants_count' => $data['participants_count'], 'nodes' => $data['nodes'], 'row_count' => $data['row_count']]);
                break;
            case 'double':
                return view('tables.progress-double', ['table' => $table, 'tournament' => $tournament]);
                break;
        }

    }

    public function end($id)
    {
        $table = Table::where('id', $id)->firstOrfail();
        $table->update([
            'status' => 'finished'
        ]);

        $tournament = $table->tournament()->first();

        $tournament->status = 'finished';

        switch ($table->type) {
            case 'single':
                $data = $this->single_get_schema($tournament->id);
                return view('tables.progress-single', ['table' => $table, 'tournament' => $tournament, 'table' => $table, 'rounds_count' => $data['rounds_count'], 'participants_count' => $data['participants_count'], 'nodes' => $data['nodes'], 'row_count' => $data['row_count']]);
                break;
            case 'double':
                return view('tables.progress-double', ['table' => $table, 'tournament' => $tournament]);
                break;
        }

    }

    public function open($id)
    {
        $table = Table::where('id', $id)->firstOrfail();
        $table->update([
            'status' => 'started'
        ]);

        $tournament = $table->tournament()->first();

        $tournament->status = 'started';

        switch ($table->type) {
            case 'single':
                $data = $this->single_get_schema($tournament->id);
                return view('tables.progress', ['table' => $table, 'tournament' => $tournament, 'table' => $table, 'rounds_count' => $data['rounds_count'], 'participants_count' => $data['participants_count'], 'nodes' => $data['nodes'], 'row_count' => $data['row_count']]);
                break;
            case 'double':
                return view('tables.progress-double', ['table' => $table, 'tournament' => $tournament]);
                break;
        }

    }

    public function single_get_schema($id)
    {

        $Tournament = Tournament::where('id', $id)->firstOrFail();

        $Tournament_id = $Tournament->id;
        $nodes = (array)json_decode($Tournament->tables->first()->schema);
        for ($ni = 1; $ni <= count($nodes); $ni++) {
            $nodes[$ni] = (array)$nodes[$ni];
        }

        $participants = json_decode($Tournament->participants);
        $count = count($participants);
        $row_count = count($nodes) - 1;
        $rounds_count = $row_count;
        $participants_count = count($nodes[1]);

        return ['rounds_count' => $rounds_count, 'participants_count' => $participants_count, 'nodes' => $nodes, 'row_count' => $row_count];

    }

    public function win(Request $request, $id)
    {

        $table = Table::where('id', $id)->first();

        $schema = json_decode($table->schema, true);

        $schema[++$request->round][$request->game] = $request->title;

        $schema = json_encode($schema);

        $table->schema = $schema;

        $table->save();

    }

    public function winDouble(Request $request, $id)
    {

        $table = Table::where('id', $id)->firstOrFail();

        $schema = json_decode($table->schema, true);

        $winner = $request->winner;
        $winnerPosition = $request->winnerPosition;
        $schema[$winnerPosition] = $winner;

        if ($request->looser != '') {
            $looser = $request->looser;
            $looserPosition = $request->looserPosition;
            $schema[$looserPosition] = $looser;
        }

        $schema = json_encode($schema);

        $table->schema = $schema;

        $table->save();

    }

    public function start($id)
    {
        $table = Table::where('id', $id)->firstOrfail();
        $tournament = $table->tournament()->first();
        $tournament->tables()->where('id', '!=', $table->id)->delete();

        $participants = json_decode($tournament->participants);

        $table->update(['status' => 'started']);

        if ($table->type == 'single') {
            $row1 = $this->row1generator($participants);
            $schema = $this->single_schema_first_time($table, $row1);

        } else if ($table->type == 'double') {
            $Tournament = $table->tournament()->firstOrFail();
            $schema = $this->double_schema_first_time($table, $Tournament);
        } else if ($table->type == 'round') {
            $Tournament = $table->tournament()->firstOrFail();
            $schema = $this->round_schema_first_time($table, $Tournament);
        }

        return redirect()->route('tables.progress', ['table' => $table->id]);

    }

    public function single_schema_first_time($table, $req_schema)
    {

        $participants = Arr::flatten($req_schema);
        // basic information that are needed
        $count = count($participants); // this is needed: count

        $i = 1;
        do {
            $squar = $i;
            $pow = pow(2, $i);
            $i++;
        } while ($pow < $count);

        $row_count = $squar; // this is needed: row count

        // make array start from 1 instead of 0
        $temp_participants = [];

        for ($start = 1; $start <= $pow; $start++) {
            $temp_start = $start - 1;
            $temp_participants[$start] = $participants[$temp_start];
        }

        $participants = $temp_participants;

        unset($temp_participants);

        $schema = [];

        $row_member_count = $count;

        for ($j = 1; $j <= $row_count + 1; $j++) {

            for ($k = 1; $k <= $row_member_count; $k++) {
                $schema[$j][$k] = '';
            }

            $row_member_count = $row_member_count / 2;
        }

        for ($member = 1; $member <= $count; $member++) {
            $schema[1][$member] = $participants[$member];
        }

        $table->update([
            'schema' => json_encode($schema),
        ]);

        return $schema;

    }

    public function double_schema_first_time($table, $tournament)
    {
        $participants = json_decode($tournament->participants);
        $count_participants = count($participants);
        $schema = [];

        if ($count_participants == 4 && !($count_participants > 4)) {
            $schema = [
                '1' => $participants[0],
                '2' => $participants[3],
                '3' => $participants[2],
                '4' => $participants[1],
            ];
            for ($i4 = 5; $i4 <= 13; $i4++) {
                $schema[$i4] = '';
            }
        } elseif ($count_participants > 4 && $count_participants < 9) {
            if ($count_participants != 8) {
                $diff = 8 - $count_participants;
                for ($L = 1; $L <= $diff; $L++) {
                    $participants[] = "استراحت";
                }
            }
            $schema = [
                '1' => $participants[0],
                '2' => $participants[7],
                '3' => $participants[3],
                '4' => $participants[4],
                '5' => $participants[5],
                '6' => $participants[2],
                '7' => $participants[6],
                '8' => $participants[1],
            ];
            for ($i8 = 9; $i8 <= 29; $i8++) {
                $schema[$i8] = '';
            }
        } elseif ($count_participants > 8 && $count_participants < 17) {
            if ($count_participants != 16) {
                $diff = 16 - $count_participants;
                for ($L = 1; $L <= $diff; $L++) {
                    $participants[] = "استراحت";
                }
            }
            $schema = [
                '1' => $participants[0],
                '2' => $participants[7],
                '3' => $participants[3],
                '4' => $participants[4],
                '5' => $participants[5],
                '6' => $participants[2],
                '7' => $participants[6],
                '8' => $participants[1],
                '9' => '',
                '10' => '',
                '11' => '',
                '12' => '',
                '13' => '',
                '14' => '',
                '15' => '',
                '16' => '',
            ];
            for ($i16 = 17; $i16 <= 61; $i16++) {
                $schema[$i16] = '';
            }
        }

        $table->update([
            'schema' => json_encode($schema),
        ]);

        return $schema;

    }

    public function round_schema_first_time($table, $tournament)
    {
        $participants = json_decode($tournament->participants);

        $table->update([
            'schema' => json_encode($schema),
        ]);

        return $schema;

    }

    public function row1generator($participants)
    {

        // count stands for members of Tournament
        $count = count($participants);

        // this loop defines to variables: base is (2) - squar( like 3 ) number & pow number( like 8 )
        $i = 1;
        do {

            $squar = $i;
            $pow = pow(2, $i);
            $i++;
        } while ($pow < $count);

        $games_count = $pow / 2;

        // this loop fill remaiming members of array with Bay
        $breaks_count = $pow - $count;

        for ($b = $pow; $b > $count; $b--) {
            $participants[] = 'استراحت';
        }

        // make array start from 1 instead of 0
        $temp_participants = [];

        for ($start = 1; $start <= $pow; $start++) {
            $temp_start = $start - 1;
            $temp_participants[$start] = $participants[$temp_start];
        }

        $participants = $temp_participants;

        unset($temp_participants);

        // :: :: :: :: :: :: :: :: :: :: :: make true order below :: :: :: :: :: :: :: :: :: :: :: :: ::

        $x = 1;
        $j = 1;
        while ($j <= $games_count) {

            $i = 1;
            $row1[$j][$i] = $participants[$x];

            $j++;
            $x++;
        }
        $j = $games_count;
        while ($j >= 1) {

            $i = 2;
            $row1[$j][$i] = $participants[$x];

            $j--;
            $x++;
        }

        // :: :: :: :: :: :: :: :: :: :: :: delete extra members of array that remaining from ordering :: :: :: :: :: :: :: :: :: :: :: :: ::

        if (count($row1) > $games_count) {
            for ($d = 1; $d <= $games_count; $d++) {
                array_pop($row1);
            }
        }

        // :: :: :: :: :: :: :: :: :: :: :: another ordering for bar times :: :: :: :: :: :: :: :: :: :: :: :: ::

        $games_count = count($row1) / 2;

        $bar = ($squar - 2);

        for ($k = 1; $k <= $bar; $k++) {

            $x = 1;
            $j = 1;

            while ($j <= $games_count) {

                $i = 1;
                $row1[$j][$i] = $row1[$x];

                $j++;
                $x++;

            }
            $j = $games_count;
            while ($j >= 1) {

                $i = 2;
                $row1[$j][$i] = $row1[$x];

                $j--;
                $x++;
            }

            if (count($row1) > $games_count) {
                for ($d = 1; $d <= $games_count; $d++) {
                    array_pop($row1);
                }
            }

            $games_count = count($row1) / 2;
        }

        // :: :: :: :: :: :: :: :: :: :: :: now ordering is ok, so array must be flatten :: :: :: :: :: :: :: :: :: :: :: :: ::


        function flatten(array $array)
        {
            $return = array();
            array_walk_recursive($array, function ($a) use (&$return) {
                $return[] = $a;
            });
            return $return;
        }

        $row1 = flatten($row1);
        $count_row1 = count($row1) / 2;
        [$part1, $part2] = array_chunk($row1, $count_row1, true);

        if (count($participants) < 33 && count($participants) > 16) {

            $part1 = array_chunk($part1, 2);
            $part1 = array_chunk($part1, 2);
            $part1[1] = array_reverse($part1[1]);
            $part1[2] = array_reverse($part1[2]);
            $part1[4] = $part1[2];
            $part1[2] = $part1[3];
            $part1[3] = $part1[4];
            unset($part1[4]);
            $part1 = flatten($part1);

            $part2 = array_chunk($part2, 2);
            $part2 = array_chunk($part2, 2);
            $part2[1] = array_reverse($part2[1]);
            $part2[2] = array_reverse($part2[2]);
            $part2[4] = $part2[2];
            $part2[2] = $part2[3];
            $part2[3] = $part2[4];
            unset($part2[4]);
            $part2 = flatten($part2);

        } elseif (count($participants) < 17 && count($participants) > 8) {
            $part1 = array_chunk($part1, 2);
            $part1 = array_chunk($part1, 2);
            $part1[1] = array_reverse($part1[1]);
            $part1 = flatten($part1);

            $part2 = array_chunk($part2, 2);
            $part2 = array_chunk($part2, 2);
            $part2[1] = array_reverse($part2[1]);
            $part2 = flatten($part2);
        }

        $part2 = array_reverse($part2);
        $row1 = array_merge($part1, $part2);
        $row1 = array_chunk($row1, 2);

        return $row1;
    }

    public function single_preview($participants)
    {

        // count stands for members of Tournament
        $count = count($participants);

        // this loop defines to variables: base is (2) - squar( like 3 ) number & pow number( like 8 )
        $i = 1;
        do {

            $squar = $i;
            $pow = pow(2, $i);
            $i++;
        } while ($pow < $count);

        $games_count = $pow / 2;

        // this loop fill remaiming members of array with Bay
        $breaks_count = $pow - $count;

        for ($b = $pow; $b > $count; $b--) {
            $participants[] = 'استراحت';
        }

        // make array start from 1 instead of 0
        $temp_participants = [];

        for ($start = 1; $start <= $pow; $start++) {
            $temp_start = $start - 1;
            $temp_participants[$start] = $participants[$temp_start];
        }

        $participants = $temp_participants;

        unset($temp_participants);

        // :: :: :: :: :: :: :: :: :: :: :: make true order below :: :: :: :: :: :: :: :: :: :: :: :: ::

        $x = 1;
        $j = 1;
        while ($j <= $games_count) {

            $i = 1;
            $row1[$j][$i] = $participants[$x];

            $j++;
            $x++;
        }
        $j = $games_count;
        while ($j >= 1) {

            $i = 2;
            $row1[$j][$i] = $participants[$x];

            $j--;
            $x++;
        }

        // :: :: :: :: :: :: :: :: :: :: :: delete extra members of array that remaining from ordering :: :: :: :: :: :: :: :: :: :: :: :: ::

        if (count($row1) > $games_count) {
            for ($d = 1; $d <= $games_count; $d++) {
                array_pop($row1);
            }
        }

        // :: :: :: :: :: :: :: :: :: :: :: another ordering for bar times :: :: :: :: :: :: :: :: :: :: :: :: ::

        $games_count = count($row1) / 2;

        $bar = ($squar - 2);

        for ($k = 1; $k <= $bar; $k++) {

            $x = 1;
            $j = 1;

            while ($j <= $games_count) {

                $i = 1;
                $row1[$j][$i] = $row1[$x];

                $j++;
                $x++;

            }
            $j = $games_count;
            while ($j >= 1) {

                $i = 2;
                $row1[$j][$i] = $row1[$x];

                $j--;
                $x++;
            }

            if (count($row1) > $games_count) {
                for ($d = 1; $d <= $games_count; $d++) {
                    array_pop($row1);
                }
            }

            $games_count = count($row1) / 2;
        }

        // :: :: :: :: :: :: :: :: :: :: :: now ordering is ok, so array must be flatten :: :: :: :: :: :: :: :: :: :: :: :: ::


        function flatten(array $array)
        {
            $return = array();
            array_walk_recursive($array, function ($a) use (&$return) {
                $return[] = $a;
            });
            return $return;
        }

        $row1 = flatten($row1);
        $count_row1 = count($row1) / 2;
        [$part1, $part2] = array_chunk($row1, $count_row1, true);

        if (count($participants) < 33 && count($participants) > 16) {

            $part1 = array_chunk($part1, 2);
            $part1 = array_chunk($part1, 2);
            $part1[1] = array_reverse($part1[1]);
            $part1[2] = array_reverse($part1[2]);
            $part1[4] = $part1[2];
            $part1[2] = $part1[3];
            $part1[3] = $part1[4];
            unset($part1[4]);
            $part1 = flatten($part1);

            $part2 = array_chunk($part2, 2);
            $part2 = array_chunk($part2, 2);
            $part2[1] = array_reverse($part2[1]);
            $part2[2] = array_reverse($part2[2]);
            $part2[4] = $part2[2];
            $part2[2] = $part2[3];
            $part2[3] = $part2[4];
            unset($part2[4]);
            $part2 = flatten($part2);

        } elseif (count($participants) < 17 && count($participants) > 8) {
            $part1 = array_chunk($part1, 2);
            $part1 = array_chunk($part1, 2);
            $part1[1] = array_reverse($part1[1]);
            $part1 = flatten($part1);

            $part2 = array_chunk($part2, 2);
            $part2 = array_chunk($part2, 2);
            $part2[1] = array_reverse($part2[1]);
            $part2 = flatten($part2);
        }

        $part2 = array_reverse($part2);
        $row1 = array_merge($part1, $part2);
        $row1 = array_chunk($row1, 2);

        // end of logic 00000000000000000000000000000000000000000000000000000000000000000000000
        echo '<style>

        .game {
            height: 30px;
            width: 150px;
            border-right: 1px solid #555;
            position: relative;
            margin-left: 20px;
            padding-right:5px;
        }

        .top {
            border-top: #555 1px solid;
            border-top-right-radius: 5px;
        }

        .bottom {
            border-bottom: #555 1px solid;
            bottom: 0;
            border-bottom-right-radius: 5px;
        }

        .bottom .title {
            position: absolute;
            bottom: 0;
        }

        .bottom-double .title {
            position: absolute;
            bottom: 0;
        }


        .wrapper-games {
            float: left;
            width: 150px;
            margin-bottom: 20px;
        }

        .game:last-child {
            margin-bottom: 0 !important;
        }

        </style>';

        return ['row1' => $row1, 'bar' => $bar];

    }

    public function round_preview($participants)
    {

// count stands for members of tournament
        $count = count($participants);

// groups count here
        $groups_count = 4;

        if (isset($_GET['gc'])) {
            $groups_count = $_GET['gc'];
        }

// each group members count here
        $group_member_count = ceil($count / $groups_count);

// standard member count for this tournament is here
        $real_count = $groups_count * $group_member_count;

// count of bays are below
        $breaks_count = $real_count - $count;

// :: :: :: :: this section will add bays to participants if needed :: :: :: ::
        if ($breaks_count > 0) {
            for ($i = 1; $i <= $breaks_count; $i++) {
                $participants[] = 'استراحت';
            }
        }

// :: :: :: :: this section will add bays to participants if group members count is odd :: :: :: ::
        if ($group_member_count % 2 != 0) {
            for ($o = 1; $o <= $groups_count; $o++) {
                $participants[] = 'استراحت';
            }
            $group_member_count++;
        }
// :: :: :: :: make groups schematic :: :: :: ::

        $groups = array();

        for ($j = 1; $j <= $groups_count; $j++) {
            $groups[$j] = array();
        }

// :: :: :: :: delete offset 0 from participants :: :: :: ::

        $old_participants = $participants;
        $participants = [];
        for ($vcv = 1; $vcv <= count($old_participants); $vcv++) {
            $number_temp = $vcv - 1;
            $participants[$vcv] = $old_participants[$number_temp];
        }

// :: :: :: :: :: :: :: :: :: :: :: fill groups with participants :: :: :: :: :: :: :: :: :: :: :: :: ::

        $x = 1;
        for ($n = 1; $n <= $group_member_count; $n++) {

            if ($n % 2 == 0) {
                for ($z = $groups_count; $z >= 1; $z--) {
                    $groups[$z][$n] = $participants[$x];
                    $x++;
                }
            } else {
                for ($z = 1; $z <= $groups_count; $z++) {
                    $groups[$z][$n] = $participants[$x];
                    $x++;
                }
            }
        }

// :: :: :: :: :: :: :: :: :: :: :: Generate games :: :: :: :: :: :: :: :: :: :: :: :: ::
        $last_key_index = $group_member_count;
        for ($q = 1; $q < $group_member_count; $q++) {
            $rounds[$q] = array();
            for ($r = 1; $r <= $groups_count; $r++) {
                $x = 1;
                for ($n = 1; $n <= 2; $n++) {

                    if ($n % 2 == 0) {
                        for ($z = $group_member_count / 2; $z >= 1; $z--) {
                            $rounds[$q][$r][$z][$n] = $groups[$r][$x];
                            $x++;
                        }
                    } else {
                        for ($z = 1; $z <= $group_member_count / 2; $z++) {
                            $rounds[$q][$r][$z][$n] = $groups[$r][$x];
                            $x++;
                        }
                    }
                }
            }
            for ($y = 1; $y <= $groups_count; $y++) {

                // this pre-func will put last member in second position of array
                array_splice($groups[$y], 1, 0, array(end($groups[$y])));
                unset($groups[$y][$last_key_index]);

                // this will increase array keys one
                $groups[$y] = array_combine(range(1, count($groups[$y])), array_values($groups[$y]));
            }
        }

        return ['groups' => $groups, 'rounds' => $rounds, 'groups_count' => $groups_count, 'group_member_count' => $group_member_count];

    }

    public function scheme($id)
    {
        $tournament = Tournament::where('id', $id)->first();
        $table = $tournament->tables()->first();

        switch ($table->type) {
            case 'single':
                $this->single_generated_schema($id);
                break;
            case 'double':
                $this->double_generated_schema($id);
                break;
        }
    }

    public function single_generated_schema($id)
    {

        $tournament = Tournament::where('id', $id)->firstOrFail();

        $tournament_id = $tournament->id;
        $nodes = (array)json_decode($tournament->tables->first()->schema);
        for ($ni = 1; $ni <= count($nodes); $ni++) {
            $nodes[$ni] = (array)$nodes[$ni];
        }

        $participants = json_decode($tournament->participants);
        $count = count($participants);
        $row_count = count($nodes) - 1;
        $rounds_count = $row_count;
        $participants_count = count($nodes[1]);
        $height = 40;
        $top = 30;
        $bottom = 80;

        for ($k = 1; $k <= $rounds_count; $k++) {

            echo '<div class="wrapper-games">';

            $game_no = 1;

            for ($m = 1; $m <= $participants_count; $m++) {
                echo '<div class="game top" style="height: ' . $height . 'px;margin-top:' . $top . 'px;">';
                if ($nodes[$k][$m] == 'استراحت') {
                    echo '<span class="title" style="color: darkslategray;">استراحت</span>';
                } elseif ($nodes[$k][$m] == null) {
                    echo '<span></span>';
                } else {
                    echo '<a data-game="' . $game_no . '" data-round="' . $k . '" data-title="' . $nodes[$k][$m] . '" class="title send-winner" style="cursor:pointer;">' . $nodes[$k][$m] . '</a>';
                }
                echo '</div>';
                $m++;
                echo '<div class="game bottom" style="height: ' . $height . 'px;margin-bottom: ' . $bottom . 'px;">';
                if ($nodes[$k][$m] == 'استراحت') {
                    echo '<span class="title" style="color: darkslategray;">استراحت</span>';
                } elseif ($nodes[$k][$m] == null) {
                    echo '<span></span>';
                } else {
                    echo '<a data-game="' . $game_no . '" data-round="' . $k . '" data-title="' . $nodes[$k][$m] . '" class="title send-winner" style="cursor:pointer;">' . $nodes[$k][$m] . '</a>';
                }
                echo '</div>';
                $game_no++;
            }
            echo '</div>';

            $top = $top + $height;
            $height = $height * 2;

            $bottom = $bottom * 2;
            $participants_count = $participants_count / 2;

        }

        if ($k = $row_count + 2) {

            echo '<div class="wrapper-games">';

            echo '<div class="game bottom" style="height: 1px;margin-top:' . $top . 'px;border-right:none;border-radius:0;"><span class="title text-center d-block w-100">' . $nodes[--$k][1] ?? "" . '</span></div>';

            echo '</div>';

        }

    }

    public function double_generated_schema($id)
    {

        $tournament = Tournament::where('id', $id)->first();
        $table = $tournament->tables()->first();
        $schema = json_decode($table->schema, 1);
        $participants = json_decode($tournament->participants);
        $count_participants = count($participants);
        if ($count_participants == 4 && !($count_participants > 4)) {
            echo '<div class="double-sheet" style="background: url(' . asset("/img/double4.png") . ');width: 599px;height: 291px;float: left">';

            echo '<a data-pw="7" data-pl="5" data-w="' . $schema[1] . '" data-l="' . $schema[2] . '" class="row1" style="top:2px;"> ' . $schema[1] . '</a>';
            echo '<a data-pw="7" data-pl="5" data-w="' . $schema[2] . '" data-l="' . $schema[1] . '" class="row1" style="top:35px;"> ' . $schema[2] . '</a>';

            echo '<a data-pw="8" data-pl="6" data-w="' . $schema[3] . '" data-l="' . $schema[4] . '" class="row1" style="top:79px;"> ' . $schema[3] . '</a>';
            echo '<a data-pw="8" data-pl="6" data-w="' . $schema[4] . '" data-l="' . $schema[3] . '" class="row1" style="top:112px;"> ' . $schema[4] . '</a>';

            echo '<a data-pw="9" data-w="' . $schema[5] . '" class="row2" style="top:186px;left: 2px;"> ' . $schema[5] . '</a>';
            echo '<a data-pw="9" data-w="' . $schema[6] . '" class="row2" style="top:222px;left: 2px;"> ' . $schema[6] . '</a>';

            echo '<a data-pw="11" data-pl="10" data-w="' . $schema[7] . '" data-l="' . $schema[8] . '" class="row2" style="top:33px;left: 151px;"> ' . $schema[7] . '</a>';
            echo '<a data-pw="11" data-pl="10" data-w="' . $schema[8] . '" data-l="' . $schema[7] . '" class="row2" style="top:85px;left: 152px;"> ' . $schema[8] . '</a>';

            echo '<a data-pw="12" data-w="' . $schema[9] . '" class="row2" style="top:217px;left: 153px;"> ' . $schema[9] . '</a>';
            echo '<a data-pw="12" data-w="' . $schema[10] . '" class="row2" style="top:269px;left: 153px;"> ' . $schema[10] . '</a>';

            echo '<a data-pw="13" data-w="' . $schema[11] . '" class="row2" style="top:72px;left: 301px;"> ' . $schema[11] . '</a>';
            echo '<a data-pw="13" data-w="' . $schema[12] . '" class="row2" style="top:231px;left: 303px;"> ' . $schema[12] . '</a>';

            echo '<a class="row2" style="top:140px;left: 450px;"> ' . $schema[13] . '</a>';

            echo '</div>';
        } else if ($count_participants > 4 && $count_participants < 9) {
            if ($count_participants != 8) {
                $diff = 8 - $count_participants;
                for ($L = 1; $L <= $diff; $L++) {
                    $participants[] = "استراحت";
                }
            }
            echo '<div class="double-sheet" style="background: url(' . asset("/img/double8.png") . ') no-repeat;width: 900px;height: 633px;float: left; margin-bot">';

            echo '<a data-pw="13" data-pl="9" data-w="' . $schema[1] . '" data-l="' . $schema[2] . '" class="row1" style="top:2px;"> ' . $schema[1] . '</a>';
            echo '<a data-pw="13" data-pl="9" data-w="' . $schema[2] . '" data-l="' . $schema[1] . '" class="row1" style="top:35px;"> ' . $schema[2] . '</a>';

            echo '<a data-pw="14" data-pl="10" data-w="' . $schema[3] . '" data-l="' . $schema[4] . '" class="row1" style="top:82px;"> ' . $schema[3] . '</a>';
            echo '<a data-pw="14" data-pl="10" data-w="' . $schema[4] . '" data-l="' . $schema[3] . '" class="row1" style="top:115px;"> ' . $schema[4] . '</a>';

            echo '<a data-pw="15" data-pl="11" data-w="' . $schema[5] . '" data-l="' . $schema[6] . '" class="row1" style="top:162px;"> ' . $schema[5] . '</a>';
            echo '<a data-pw="15" data-pl="11" data-w="' . $schema[6] . '" data-l="' . $schema[5] . '" class="row1" style="top:195px;"> ' . $schema[6] . '</a>';

            echo '<a data-pw="16" data-pl="12" data-w="' . $schema[7] . '" data-l="' . $schema[8] . '" class="row1" style="top:242px;"> ' . $schema[7] . '</a>';
            echo '<a data-pw="16" data-pl="12" data-w="' . $schema[8] . '" data-l="' . $schema[7] . '" class="row1" style="top:275px;"> ' . $schema[8] . '</a>';

            echo '<a data-pw="17" data-w="' . $schema[9] . '" class="row1" style="top:361px;"> ' . $schema[9] . '</a>';
            echo '<a data-pw="17" data-w="' . $schema[10] . '" class="row1" style="top:394px;"> ' . $schema[10] . '</a>';

            echo '<a data-pw="19" data-w="' . $schema[11] . '" class="row1" style="top:479px;"> ' . $schema[11] . '</a>';
            echo '<a data-pw="19" data-w="' . $schema[12] . '" class="row1" style="top:512px;"> ' . $schema[12] . '</a>';

            echo '<a data-pw="21" data-pl="20" data-w="' . $schema[13] . '" data-l="' . $schema[14] . '" class="row2" style="top:33px;left:152px;"> ' . $schema[13] . '</a>';
            echo '<a data-pw="21" data-pl="20" data-w="' . $schema[14] . '" data-l="' . $schema[13] . '" class="row2" style="top:87px;left:152px;"> ' . $schema[14] . '</a>';

            echo '<a data-pw="22" data-pl="18" data-w="' . $schema[15] . '" data-l="' . $schema[16] . '" class="row2" style="top:193px;left:152px;"> ' . $schema[15] . '</a>';
            echo '<a data-pw="22" data-pl="18" data-w="' . $schema[16] . '" data-l="' . $schema[15] . '" class="row2" style="top:247px;left:152px;"> ' . $schema[16] . '</a>';

            echo '<a data-pw="23" data-w="' . $schema[17] . '" class="row2" style="top:393px;left:152px;"> ' . $schema[17] . '</a>';
            echo '<a data-pw="23" data-w="' . $schema[18] . '" class="row2" style="top:425px;left:152px;"> ' . $schema[18] . '</a>';

            echo '<a data-pw="24" data-w="' . $schema[19] . '" class="row2" style="top:511px;left:152px;"> ' . $schema[19] . '</a>';
            echo '<a data-pw="24" data-w="' . $schema[20] . '" class="row2" style="top:543px;left:152px;"> ' . $schema[20] . '</a>';

            echo '<a data-pw="25" data-pl="27" data-w="' . $schema[21] . '" data-l="' . $schema[22] . '" class="row2" style="top:73px; left:302px;"> ' . $schema[21] . '</a>';
            echo '<a data-pw="25" data-pl="27" data-w="' . $schema[22] . '" data-l="' . $schema[21] . '" class="row2" style="top:207px; left:302px;"> ' . $schema[22] . '</a>';

            echo '<a data-pw="26" data-w="' . $schema[23] . '" class="row2" style="top:421px; left:302px;"> ' . $schema[23] . '</a>';
            echo '<a data-pw="26" data-w="' . $schema[24] . '" class="row2" style="top:517px; left:302px;"> ' . $schema[24] . '</a>';

            echo '<a data-pw="29" data-w="' . $schema[25] . '" class="row2" style="top:143px; left:602px;"> ' . $schema[25] . '</a>';

            echo '<a data-pw="28" data-w="' . $schema[26] . '" class="row2" style="top:483px; left:452px;"> ' . $schema[26] . '</a>';
            echo '<a data-pw="28" data-w="' . $schema[27] . '" class="row2" style="top:579px; left:452px;"> ' . $schema[27] . '</a>';

            echo '<a data-pw="29" data-w="' . $schema[28] . '" class="row2" style="top:518px; left:602px;"> ' . $schema[28] . '</a>';

            echo '<a class="row2" style="top:307px; left:752px;"> ' . $schema[29] . '</a>';

            echo '</div>';

        } else if ($count_participants > 8) {

            echo '<div>به زودی با خرید اکانت پیشرفته میتواندید بیشتر از 8 نفر را ترسیم کنید اما برای الآن ممکن نیست!</div>';

        }

    }

}
