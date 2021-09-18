<?php

namespace App\TableGenerator\Singles;

use Illuminate\Support\Arr;

class Single
{
    private $count;

    private $participants;

    public function __construct($participants)
    {
        $this->count = count($participants);

        $this->participants = $participants;
    }

    public function makePreview()
    {
        $smallest_power_of_two = $this->findingPowerOfTwoWhichParticipantsCountIsSmallerThanItsResult($this->count);

        $this->fillingRemainingMembersOfParticipantsArrayWithBay($smallest_power_of_two);

        $first_round = $this->determiningCompetitor($smallest_power_of_two);

        $first_round_with_lower_half_and_upper_half_true_order = $this->makeLowerHalfAndUpperHalfGamesOrderTrue($first_round, $smallest_power_of_two);

        $first_round_which_lower_half_is_reversed = $this->reverseLowerHalfOfFirstRound($first_round_with_lower_half_and_upper_half_true_order);

        $first_round_games = array_chunk($first_round_which_lower_half_is_reversed, 2);

        return $first_round_games;
    }

    public function findingPowerOfTwoWhichParticipantsCountIsSmallerThanItsResult($count): int
    {
        $power_of_two = 0;
        do {
            $smallest_power_of_two = $power_of_two;
            $power = pow(2, $power_of_two);
            $power_of_two++;
        } while ($power < $count);

        return $smallest_power_of_two;
    }

    private function fillingRemainingMembersOfParticipantsArrayWithBay(int $smallest_power_of_two): void
    {
        $count_participants_with_bays = pow(2, $smallest_power_of_two);

        for ($count_participants_with_bays; $count_participants_with_bays > $this->count; $count_participants_with_bays--) {
            $this->participants[] = 'استراحت';
        }
    }

    private function determiningCompetitor(int $smallest_power_of_two): array
    {
        $games_count = pow(2, $smallest_power_of_two) / 2;

        $participant_index = 0;
        $game_no = 1;
        $first_round = [];

        while ($game_no <= $games_count) {

            $i = 1;
            $first_round[$game_no][$i] = $this->participants[$participant_index];

            $game_no++;
            $participant_index++;
        }
        $game_no = $games_count;
        while ($game_no >= 1) {

            $i = 2;
            $first_round[$game_no][$i] = $this->participants[$participant_index];

            $game_no--;
            $participant_index++;
        }
        return $first_round;
    }

    private function makeLowerHalfAndUpperHalfGamesOrderTrue(array $first_round, int $smallest_power_of_two): array
    {
        $games_count = count($first_round) / 2;

        $bar_count = ($smallest_power_of_two - 2);

        for ($bar = 1; $bar <= $bar_count; $bar++) {

            $game_position = 1;
            $participants_position = 1;

            while ($game_position <= $games_count) {

                $player_position = 1;
                $first_round[$game_position][$player_position] = $first_round[$participants_position];

                $game_position++;
                $participants_position++;

            }
            $game_position = $games_count;
            while ($game_position >= 1) {

                $player_position = 2;
                $first_round[$game_position][$player_position] = $first_round[$participants_position];

                $game_position--;
                $participants_position++;

            }

            if (count($first_round) > $games_count) {
                for ($d = 1; $d <= $games_count; $d++) {
                    array_pop($first_round);
                }
            }

            $games_count = count($first_round) / 2;
        }

        return $this->flatten($first_round);

    }

    private function reverseLowerHalfOfFirstRound($first_round): array
    {
        $count_first_round = count($first_round) / 2;

        [$upper_half, $lower_half] = array_chunk($first_round, $count_first_round, true);

        [$upper_half, $lower_half] = $this->exceptionHandling($upper_half, $lower_half);

        $lower_half_reversed = array_reverse($lower_half);

        return array_merge($upper_half, $lower_half_reversed);
    }

    private function flatten(array $array)
    {
        $array_to_flatt = array();
        array_walk_recursive($array, function ($a) use (&$array_to_flatt) {
            $array_to_flatt[] = $a;
        });
        return $array_to_flatt;
    }

    private function exceptionHandling($part1, $part2){
        if ($this->count < 33 && $this->count > 16) {
            $part1 = array_chunk($part1, 2);
            $part1 = array_chunk($part1, 2);
            $part1[1] = array_reverse($part1[1]);
            $part1[2] = array_reverse($part1[2]);
            $part1[4] = $part1[2];
            $part1[2] = $part1[3];
            $part1[3] = $part1[4];
            unset($part1[4]);
            $part1 = $this->flatten($part1);

            $part2 = array_chunk($part2, 2);
            $part2 = array_chunk($part2, 2);
            $part2[1] = array_reverse($part2[1]);
            $part2[2] = array_reverse($part2[2]);
            $part2[4] = $part2[2];
            $part2[2] = $part2[3];
            $part2[3] = $part2[4];
            unset($part2[4]);
            $part2 = $this->flatten($part2);

        } elseif ($this->count < 17 && $this->count > 8) {
            $part1 = array_chunk($part1, 2);
            $part1 = array_chunk($part1, 2);
            $part1[1] = array_reverse($part1[1]);
            $part1 = $this->flatten($part1);

            $part2 = array_chunk($part2, 2);
            $part2 = array_chunk($part2, 2);
            $part2[1] = array_reverse($part2[1]);
            $part2 = $this->flatten($part2);
        }

        return [$part1, $part2];
    }

    public function getSingleSchema(int $row_count, $row_member_count, array $schema, int $count_first_round_participants, $first_round_flatted): array
    {
        for ($j = 1; $j <= $row_count + 1; $j++) {

            for ($k = 1; $k <= $row_member_count; $k++) {
                $schema[$j][$k] = '';
            }

            $row_member_count = $row_member_count / 2;
        }

        for ($member = 1; $member <= $count_first_round_participants; $member++) {
            $schema[1][$member] = $first_round_flatted;
        }
        return $schema;
    }

    public function storeSingleSchema($first_round)
    {
        $first_round_flatted = Arr::flatten($first_round);

        $count_first_round_participants = count($first_round_flatted);

        $row_count = $this->findingPowerOfTwoWhichParticipantsCountIsSmallerThanItsResult($count_first_round_participants);

        $schema = [];

        $row_member_count = $count_first_round_participants;

        $schema = $this->getSingleSchema($row_count, $row_member_count, $schema, $count_first_round_participants, $first_round_flatted);

        return $schema;

    }
}
