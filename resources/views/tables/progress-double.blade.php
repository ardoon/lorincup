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
    <style>
        .double-sheet {
            font-size: 15px;
            position: relative;
        }

        .double-sheet span {
            position: absolute;
        }

        .double-sheet span.row1{
            left: 1px;
            width: 140px;
        }

        .double-sheet a {
            position: absolute;
        }

        .double-sheet a.row1{
            left: 1px;
            width: 140px;
            cursor: pointer;
        }
        .double-sheet a.row2{
            width: 140px;
            height: 20px;
            cursor: pointer;
            /*background: #3f9ae5;*/
        }
    </style>

    <div id="schema" class="mt-3 ml-3 font-weight-light col-12">


        @php
            $schema = json_decode($table->schema,1);
            $participants = json_decode($tournament->participants);
            $count_participants = count($participants);
            if ($count_participants == 4 && !($count_participants > 4)){
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
            } else if ($count_participants > 4 && $count_participants < 9){
                if ($count_participants != 8){
                    $diff = 8 - $count_participants;
                    for($L = 1; $L <= $diff; $L++){
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

            } else if ($count_participants > 8 && $count_participants < 17){
                if ($count_participants != 16){
                    $diff = 16 - $count_participants;
                    for($L = 1; $L <= $diff; $L++){
                        $participants[] = "استراحت";
                    }
                }

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
                url: "{{ url('/tables/win-double') . '/' . $table->id }}",
                method: "POST",
                data: {
                    winner: $(this).data('w'),
                    looser: $(this).data('l') ?? '',
                    winnerPosition: $(this).data('pw'),
                    looserPosition: $(this).data('pl') ?? ''
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
