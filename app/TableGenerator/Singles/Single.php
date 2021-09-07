<?php

namespace App\TableGenerator\Singles;

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
        $smallest_power_of_two = $this->findingPowerOfTwoWhichParticipantsCountIsSmallerThanItsResult();

        $this->fillingRemainingMembersOfParticipantsArrayWithBay($smallest_power_of_two);

        $first_round = $this->determiningCompetitor($smallest_power_of_two);

//        $uselessMethod = $this->uselessMethod($smallest_power_of_two, $first_round);
        $first_round_with_true_order = $this->makeGamesOrderTrue($first_round, $smallest_power_of_two);

        $first_round_with_true_order_flatten = $this->flatten($first_round_with_true_order);

        $count_first_round = count($first_round_with_true_order_flatten) / 2;
        [$part1, $part2] = array_chunk($first_round_with_true_order_flatten, $count_first_round, true);

    }

    private function findingPowerOfTwoWhichParticipantsCountIsSmallerThanItsResult(): int
    {
        $i = 0;
        do {

            $squar = $i;
            $pow = pow(2, $i);
            $i++;
        } while ($pow < $this->count);

        return $squar;
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

        $x = 0;
        $j = 1;
        while ($j <= $games_count) {

            $i = 1;
            $row1[$j][$i] = $this->participants[$x];

            $j++;
            $x++;
        }
        $j = $games_count;
        while ($j >= 1) {

            $i = 2;
            $row1[$j][$i] = $this->participants[$x];

            $j--;
            $x++;
        }
        return $row1;
    }

    private function uselessMethod(int $smallest_power_of_two, array $first_round): array
    {
        $games_count = pow(2, $smallest_power_of_two) / 2;

        if (count($first_round) > $games_count) {
            for ($d = 1; $d <= $games_count; $d++) {
                array_pop($first_round);
            }
        }
        return $first_round;
    }

    private function makeGamesOrderTrue(array $first_round, int $smallest_power_of_two): array
    {
// :: :: :: :: :: :: :: :: :: :: :: another ordering for bar times :: :: :: :: :: :: :: :: :: :: :: :: ::

        $games_count = count($first_round) / 2;

        $bar = ($smallest_power_of_two - 2);

        for ($k = 1; $k <= $bar; $k++) {

            $x = 1;
            $j = 1;

            while ($j <= $games_count) {

                $i = 1;
                $first_round[$j][$i] = $first_round[$x];

                $j++;
                $x++;

            }
            $j = $games_count;
            while ($j >= 1) {

                $i = 2;
                $first_round[$j][$i] = $first_round[$x];

                $j--;
                $x++;
            }

            if (count($first_round) > $games_count) {
                for ($d = 1; $d <= $games_count; $d++) {
                    array_pop($first_round);
                }
            }

            $games_count = count($first_round) / 2;
        }
        return $first_round;
    }

    private function flatten(array $array)
    {
        $return = array();
        array_walk_recursive($array, function ($a) use (&$return) {
            $return[] = $a;
        });
        return $return;
    }
}
