@extends('layouts.main')

@section('content')
<div class="mt-3 ml-3 font-weight-light col-12">
    @php

        echo '<div class="wrapper-games">';
                foreach ($row1 as $single) :

                    echo '<div class="game top"><span class="title">' . $single[0] . '</span></div>';
                    echo '<div class="game bottom" style="margin-bottom: 20px;"><span class="title">' . $single[1] . '</span></div>';

                endforeach;
            echo '</div>';
            $height = 40; $top = 30; $bottom = 80;

            $my_number = count($row1) / 2;
            for ($k = 1; $k <= $bar + 1; $k++) :

                echo '<div class="wrapper-games">';
                for ($m = 1; $m <= $my_number; $m++) :
                    echo '<div class="game top" style="height:' . $height . 'px;margin-top:' . $top . 'px;"><span class="title"></span></div>';
                    echo '<div class="game bottom" style="height:' . $height . 'px;margin-bottom: ' . $bottom . 'px;"><span class="title"></span></div>';
                endfor;
                echo '</div>';

                $top = $top + $height;
                $height = $height * 2;
                $bottom = $bottom * 2;
                $my_number = $my_number / 2;

            endfor;

            if ($k = $bar + 2) :

                echo '<div class="wrapper-games">';
                    echo '<div class="game top" style="height: 1px;margin-top:' . $top . 'px;border-right:none;border-radius:0;"><span class="title"></span></div>';
                echo '</div>';

            endif;

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
