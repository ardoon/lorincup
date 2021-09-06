@extends('layouts.main')

@section('custom-nav-sheet')
    <form action="" method="get"><span id="gc-title" class="right-float text-light"> تعداد گروه: </span><input name="gc" id="gc" class="right-float" type="text" placeholder="پیش فرض 4">
        <button type="submit" class="btn btn-sm btn-primary">اعمال</button></form>
@endsection

@section('content')
<div class="mt-3 ml-3 font-weight-light col-12 position-relative">
    <div id="groups-wrapper">
        <?php $counter = 1; ?>
        <?php foreach ($groups as $group) : ?>
        <div class="group text-center">
            <h5><?= 'گروه ' . $counter ?></h5>

            <ul>
                <?php foreach ($group as $member) : ?>

                <li><?= $member ?></li>
                <?php endforeach; ?>
            </ul>

        </div>
        <?php $counter++; ?>
        <?php endforeach; ?>
    </div>
    <hr>
    <?php $counter = 1; ?>
    <?php foreach ($rounds as $round) : ?>
    <div id="groups-wrapper">
        <?php for ($r = 1; $r <= $groups_count; $r++) : ?>
        <div class="group text-center">
            <h5><?= 'دور ' . $counter ?></h5>
            <table>
                <?php for ($n = 1; $n <= $group_member_count / 2; $n++) : ?>
                <tr class='game-round'>
                    <td class='gemer'><?= $round[$r][$n][1] ?></td>
                    <td class='gemer'><?= $round[$r][$n][2] ?></td>
                </tr>
                <?php endfor; ?>
            </table>

        </div>

        <?php endfor; ?>
    </div>
    <?php $counter++; ?>
    <?php endforeach; ?>

    <hr>

    <div id="groups-wrapper">
        <?php $counter = 1; ?>
        <?php foreach ($groups as $group) : ?>
        <table class='result-table'>
            <h3 class='table-title'>گروه <?= $counter ?></h3>
            <tr>
                <td class='td-blank'></td>
                <?php foreach ($group as $g) : ?>
                <td><?= $g ?></td>
                <?php endforeach; ?>
            </tr>

            <?php $e = 1; ?>
            <?php foreach ($group as $g) : ?>
            <tr>

                <td><?= $g ?></td>
                <?php $w = 1; ?>
                <?php foreach ($group as $g) : ?>
                <td <?php if ($e == $w) {
                    echo "class='td-blank'";
                } ?>></td>
                <?php $w++; ?>
                <?php endforeach; ?>
                <?php $e++; ?>
            </tr>
            <?php endforeach; ?>

        </table>
        <?php $counter++; ?>
        <?php endforeach; ?>
    </div>
</div>

{{--<!-- Modal -->--}}
{{--<div class="modal fade" id="exampleModal" tabindex="-1" aria-labelledby="exampleModalLabel" aria-hidden="true">--}}
{{--    <div class="modal-dialog">--}}
{{--        <div class="modal-content bg-dark">--}}
{{--            <div class="modal-header text-white border-secondary">--}}
{{--                <h5 class="modal-title font-weight-lighter" id="exampleModalLabel">از شروع مسابقه اطمینان دارید؟</h5>--}}
{{--                <button type="button" class="close ml-0 pl-0 mr-auto" data-dismiss="modal" aria-label="Close">--}}
{{--                    <span aria-hidden="true" class="text-white">&times;</span>--}}
{{--                </button>--}}
{{--            </div>--}}
{{--            <div class="modal-body text-light text-justify font-weight-lighter">--}}
{{--                با شروع کردن مسابقه دیگر قادر به تغییر شرکت کنندگان نیستید و فقط می توانید نتایج را ثبت کنید. همچنین انواع مختلف جداولی که ساخته اید حذف میشوند. بنابر این اطمینان حاصل کنید زیرا <strong>این عمل غیرقابل برگشت خواهد بود!</strong>--}}
{{--            </div>--}}
{{--            <div class="modal-footer border-secondary">--}}
{{--                <button type="button" class="btn btn-secondary" data-dismiss="modal">بیخیال</button>--}}
{{--                <button id="start-table" type="button" class="btn btn-success text-dark">شروع مسابقه</button>--}}
{{--            </div>--}}
{{--        </div>--}}
{{--    </div>--}}
{{--</div>--}}

<!-- Modal -->
<div class="modal fade" id="exampleModal" tabindex="-1" aria-labelledby="exampleModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content bg-dark">
            <div class="modal-header text-white border-secondary">
                <h5 class="modal-title font-weight-lighter" id="exampleModalLabel">به زودی</h5>
                <button type="button" class="close ml-0 pl-0 mr-auto" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true" class="text-white">&times;</span>
                </button>
            </div>
            <div class="modal-body text-light text-justify font-weight-lighter">
در حال حاضر فقط می توانید پیش نمایشی از جداول دوره ایی داشته باشید، ولی به زودی می توانید جداول دوره ای را نیز تا پایان مسابقات پیش ببرید. درحال بروزرسانی رابط کاربری آن هستیم.            </div>
            <div class="modal-footer border-secondary">
                <button type="button" class="btn btn-primary" data-dismiss="modal">بسیار خب</button>
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
