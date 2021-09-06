@extends('layouts.main')

@section('content')

    <!-- Modal -->
    <div class="modal fade" id="exampleModal" tabindex="-1" aria-labelledby="exampleModalLabel" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content bg-dark">
                <div class="modal-header text-white border-secondary">
                    <h5 class="modal-title font-weight-lighter" id="exampleModalLabel">از شروع مسابقه اطمینان
                        دارید؟</h5>
                    <button type="button" class="close ml-0 pl-0 mr-auto" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true" class="text-white">&times;</span>
                    </button>
                </div>
                <div class="modal-body text-light text-justify font-weight-lighter">
                    با شروع کردن مسابقه دیگر قادر به تغییر شرکت کنندگان نیستید و فقط می توانید نتایج را ثبت کنید. همچنین
                    انواع مختلف جداولی که ساخته اید حذف میشوند. بنابر این اطمینان حاصل کنید زیرا <strong>این عمل غیرقابل
                        برگشت خواهد بود!</strong>
                </div>
                <div class="modal-footer border-secondary">
                    <button type="button" class="btn btn-secondary" data-dismiss="modal">بیخیال</button>
                    <button id="start-table" type="button" class="btn btn-success text-dark">شروع مسابقه</button>
                </div>
            </div>
        </div>
    </div>

    <!-- Modal -->
    <div class="modal fade" id="endModal" tabindex="-1" aria-labelledby="exampleModalLabel" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content bg-dark">
                <div class="modal-header text-white border-secondary">
                    <h5 class="modal-title font-weight-lighter" id="exampleModalLabel">از پایان دادن به مسابقه اطمینان
                        دارید؟</h5>
                    <button type="button" class="close ml-0 pl-0 mr-auto" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true" class="text-white">&times;</span>
                    </button>
                </div>
                <div class="modal-body text-light text-justify font-weight-lighter">
                    این عمل قابل برگشت است و میتوانید مجددا مسابقه را به جریان بیاندازید و نتایج را ویرایش کنید.
                </div>
                <div class="modal-footer border-secondary">
                    <button type="button" class="btn btn-secondary" data-dismiss="modal">بیخیال</button>
                    <button id="end-table" type="button" class="btn btn-success text-dark">پایان مسابقه</button>
                </div>
            </div>
        </div>
    </div>

    <style>

        @if($tournament->status == 'finished')
        #schema a {
            text-decoration: none;
            color: #000;
            cursor: text !important;
        }

        #print-table-link{
            color: #51b9ec !important;
        }

        @endif

        .title {
            width: 100%;
            display: block;
            text-align: center;
            font-size: 12px;
        }

        .game {
            height: 30px;
            width: 150px;
            border-right: 1px solid #555;
            position: relative;
            margin-left: 20px;
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
    </style>

    <div id="schema" class="mt-0 ml-3 font-weight-light col-12">

        @php

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


        @endphp
    </div>




    <script>

        $.ajaxSetup({
            headers: {
                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
            }
        });

        $('#print-table-link').on('click', function () {
            window.print();
        });

        $('#start-table').on('click', function () {
            let id = {{ $table->id }};
            window.location.href = "{{ url('/tables/start') }}/" + id;
        });

        $('#end-table').on('click', function () {
            let id = {{ $table->id }};
            window.location.href = "{{ url('/tables/end') }}/" + id;
        });

        $('#open-table').on('click', function () {
            let id = {{ $table->id }};
            window.location.href = "{{ url('/tables/open') }}/" + id;
        });

        function getSchemaAjax() {
            $.ajax({
                url: "{{ url('/tables/schema') . '/' . $tournament->id }}",
                method: "GET",
                data: {},
                success: function (data) {
                    // var parsData = JSON.parse(data);
                    // var row1 = parsData.schema;
                    document.getElementById("schema").innerHTML = data;
                },
                error: function (err) {
                    console.log("Error: " + err);
                }
            });
        };

        @if($tournament->status == 'started')

        $("#schema").on("click", 'a', function (event) {
            event.preventDefault()
            $.ajax({
                url: "{{ url('/tables/win') . '/' . $table->id }}",
                method: "POST",
                data: {
                    game: $(this).data('game'),
                    round: $(this).data('round'),
                    title: $(this).data('title')
                },
                success: function (data) {
                    getSchemaAjax();
                },
                error: function (err) {
                    console.log("Error: " + err);
                }
            });
        });

        @endif

    </script>
@endsection
