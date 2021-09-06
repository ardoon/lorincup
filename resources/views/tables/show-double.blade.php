@extends('layouts.main')

@section('content')
<div class="mt-3 ml-3 font-weight-light col-12 position-relative">
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
    @php

        $count_participants = count($participants);

                if ($count_participants < 4){
                    echo 'لطفا حداقل 4 شرکت کننده را وارد کنید';
                }
                else if ($count_participants == 4 && !($count_participants > 4)){
                    echo '<div class="double-sheet position-relative" style="background: url(' . asset("/img/double4.png") . ');width: 599px;height: 291px;float: left">';
                    echo '<span class="row1" style="top:0px;"> ' . $participants[0] . '</span>';
                    echo '<span class="row1" style="top:35px;"> ' . $participants[1] . '</span>';
                    echo '<span class="row1" style="top:77px;"> ' . $participants[2] . '</span>';
                    echo '<span class="row1" style="top:112px;"> ' . $participants[3] . '</span>';
                    echo '</div>';
                }
                else if ($count_participants > 4 && $count_participants < 9){
                    if ($count_participants != 8){
                        $diff = 8 - $count_participants;
                        for($L = 1; $L <= $diff; $L++){
                            $participants[] = "استراحت";
                        }
                    }
                    echo '<div class="double-sheet position-relative" style="background: url(' . asset("/img/double8.png") . ') no-repeat;width: 900px;height: 633px;float: left; margin-bot">';
                    echo '<span class="row1" style="top:2px;"> ' . $participants[0] . '</span>';
                    echo '<span class="row1" style="top:35px;"> ' . $participants[7] . '</span>';
                    echo '<span class="row1" style="top:82px;"> ' . $participants[3] . '</span>';
                    echo '<span class="row1" style="top:115px;"> ' . $participants[4] . '</span>';
                    echo '<span class="row1" style="top:162px;"> ' . $participants[5] . '</span>';
                    echo '<span class="row1" style="top:195px;"> ' . $participants[2] . '</span>';
                    echo '<span class="row1" style="top:242px;"> ' . $participants[6] . '</span>';
                    echo '<span class="row1" style="top:275px;"> ' . $participants[1] . '</span>';
                    echo '</div>';
                }
                else if ($count_participants > 8 && $count_participants < 17){
                    if ($count_participants != 16){
                        $diff = 16 - $count_participants;
                        for($L = 1; $L <= $diff; $L++){
                            $participants[] = "استراحت";
                        }
                    }
                    echo '<div class="double-sheet position-relative" style="background: url(' . asset("/img/double16.png") . ') no-repeat;width: 1200px;height: 1245px;float: left; margin-bot">';
                    echo '<span class="row1" style="top:2px;"> ' . $participants[0] . '</span>';
                    echo '<span class="row1" style="top:35px;"> ' . $participants[15] . '</span>';
                    echo '<span class="row1" style="top:82px;"> ' . $participants[7] . '</span>';
                    echo '<span class="row1" style="top:115px;"> ' . $participants[8] . '</span>';
                    echo '<span class="row1" style="top:162px;"> ' . $participants[4] . '</span>';
                    echo '<span class="row1" style="top:195px;"> ' . $participants[11] . '</span>';
                    echo '<span class="row1" style="top:242px;"> ' . $participants[3] . '</span>';
                    echo '<span class="row1" style="top:275px;"> ' . $participants[12] . '</span>';
                    echo '<span class="row1" style="top:322px;"> ' . $participants[13] . '</span>';
                    echo '<span class="row1" style="top:355px;"> ' . $participants[2] . '</span>';
                    echo '<span class="row1" style="top:402px;"> ' . $participants[10] . '</span>';
                    echo '<span class="row1" style="top:435px;"> ' . $participants[5] . '</span>';
                    echo '<span class="row1" style="top:482px;"> ' . $participants[9] . '</span>';
                    echo '<span class="row1" style="top:515px;"> ' . $participants[6] . '</span>';
                    echo '<span class="row1" style="top:562px;"> ' . $participants[14] . '</span>';
                    echo '<span class="row1" style="top:595px;"> ' . $participants[1] . '</span>';
                    echo '</div>';
                }

    @endphp
</div>

<!-- Modal -->
<div class="modal fade" id="exampleModal" tabindex="-1" aria-labelledby="exampleModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content bg-dark">
            <div class="modal-header text-white border-secondary">
                <h5 class="modal-title font-weight-lighter" id="exampleModalLabel">از شروع مسابقه اطمینان دارید؟</h5>
                <button type="button" class="close ml-0 pl-0 mr-auto" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true" class="text-white">&times;</span>
                </button>
            </div>
            <div class="modal-body text-light text-justify font-weight-lighter">
                با شروع کردن مسابقه دیگر قادر به تغییر شرکت کنندگان نیستید و فقط می توانید نتایج را ثبت کنید. همچنین انواع مختلف جداولی که ساخته اید حذف میشوند. بنابر این اطمینان حاصل کنید زیرا <strong>این عمل غیرقابل برگشت خواهد بود!</strong>
            </div>
            <div class="modal-footer border-secondary">
                <button type="button" class="btn btn-secondary" data-dismiss="modal">بیخیال</button>
                <button id="start-table" type="button" class="btn btn-success text-dark">شروع مسابقه</button>
            </div>
        </div>
    </div>
</div>


<script>
    $('#print-table-link').on('click', function () {
        window.print();
    });

    $('#start-table').on('click', function () {
        let id = {{ $table->id }};
        window.location.href = "{{ url('/tables/start') }}/" + id;
    });
</script>
@endsection
