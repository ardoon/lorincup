@extends('layouts.app')

@section('content')

    @if($tournament->status != 'raw')
        <style>
            .trash-participant {
                display: none;
            }

            .arrows-icon {
                display: none;
            }
        </style>
    @endif

    <div class="container pt-5">

        <main role="main">
            <ul class="nav nav-pills mb-3 pr-0 justify-content-center" id="pills-tab" role="tablist">
                <li class="nav-item" role="presentation">
                    <a class="nav-link" id="pills-home-tab" data-toggle="pill" href="#pills-home" role="tab"
                       aria-controls="pills-home" aria-selected="true">اطلاعات عمومی</a>
                </li>
                <li class="nav-item" role="presentation">
                    <a class="nav-link active" id="pills-profile-tab" data-toggle="pill" href="#pills-profile"
                       role="tab" aria-controls="pills-profile" aria-selected="false">شرکت کنندگان</a>
                </li>
                <li class="nav-item" role="presentation">
                    <a class="nav-link" id="pills-contact-tab" data-toggle="pill" href="#pills-contact" role="tab"
                       aria-controls="pills-contact" aria-selected="false">جداول مسابقاتی</a>
                </li>
            </ul>
            <div class="tab-content my-5 text-light" id="pills-tabContent">
                <div class="tab-pane fade" id="pills-home" role="tabpanel" aria-labelledby="pills-home-tab">
                    <div class="row justify-content-center">
                        <form class="text-light col-12 col-lg-6 p-4 mb-5" method="post"
                              action="{{ route('tournaments.update', [$tournament->id]) }}" autocomplete="off">
                            @csrf
                            @method('PUT')
                            <div class="form-group">
                                <label for="tournament-title"><span class="required-star">*</span> عنوان مسابقه</label>
                                <input type="text" name="tournament_title"
                                       class="form-control bg-dark border-0 text-light" id="tournament-title"
                                       value="{{ $tournament->title }}" required>
                            </div>
                            <div class="form-group">
                                <label for="tournament-field">رشته ورزشی</label>
                                <select name="tournament_field" id="tournament-field"
                                        class="form-control bg-dark border-0 text-light" autocomplete="off">
                                    <option>انتخاب کنید</option>
{{--                                    <option value="badminton"--}}
{{--                                            @if($tournament->field == 'badminton') selected="selected" @endif>بدمینتون--}}
{{--                                    </option>--}}
                                    <option value="other" @if($tournament->field == 'other') selected="selected" @endif>
                                        متفرقه
                                    </option>
                                </select>
                            </div>
                            <div class="form-group pb-3">
                                <label for="tournament-desc">شرح مختصر</label>
                                <textarea name="tournament_desc" class="form-control bg-dark border-0 text-light"
                                          id="tournament-desc">{{ $tournament->description }}</textarea>
                            </div>
                            <button type="submit" class="btn btn-success float-left text-dark ">ذخیره</button>
                            <button type="button" id="delete-tournament" class="btn btn-secondary float-left text-dark ml-2">حذف</button>
                        </form>
                        <form method="post" id="tournament-delete-form" action="{{ route('tournaments.update', [$tournament->id]) }}">
                            @csrf
                            @method('DELETE')
                        </form>
                    </div>
                </div>
                <div class="tab-pane fade show active" id="pills-profile" role="tabpanel"
                     aria-labelledby="pills-profile-tab">
                    <div class="row justify-content-center">
                        <div id="participants-wrapper" class="col-12 col-md-6 col-lg-5">
                            @if($tournament->status == 'raw')
                                <h5 class="font-weight-light text-center text-secondary w-75 mx-auto mb-4"
                                    style="line-height: 25px">لطفا در صورت تصمیم به سید گذاری خودکار، اسامی را به ترتیب
                                    رتبه
                                    و سید وارد کنید.</h5>
                            @elseif($tournament->status != 'raw')
                                <h5 class="font-weight-light text-center text-secondary w-75 mx-auto mb-4"
                                    style="line-height: 25px">شرکت کنندگان به ترتیب زیر هستند</h5>
                            @endif
                            @if($tournament->status == 'raw')
                                <div class="form-group col position-relative">
                                    <input type="text" class="form-control bg-dark border-0 text-light"
                                           id="participants-new" name="newParticipant"
                                           placeholder="نام شرکت کننده جدید">
                                    <button id="add-participant-button"
                                            class="bg-transparent border-0 text-success font-weight-bolder position-absolute"
                                            style="top: 4px;
left: 22px;
font-size: 19px;"><i class="fas fa-plus"></i></button>
                                </div>
                            @endif
                            <div class="col-12 order-last order-md-first">
                                <ol class="sortable col p-0">
                                </ol>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="tab-pane fade" id="pills-contact" role="tabpanel" aria-labelledby="pills-contact-tab">

                    @if($tournament->status == 'raw')
                        <div class="row justify-content-center">
                            <div class="col-12 col-md-6 col-lg-4">
                                <label for="tournament-type" class="font-weight-lighter">نوع جدول مسابقاتی</label>
                                <select name="tournament_type" id="tournament-type"
                                        class="form-control bg-dark border-0 text-light" autocomplete="off">
                                    <option value="none">انتخاب کنید</option>
                                    <option value="single"
                                            @foreach($tables as $table) @if($table->type == 'single') disabled @endif @endforeach>
                                        یک حذفی
                                    </option>
                                    <option value="double"
                                            @foreach($tables as $table) @if($table->type == 'double') disabled @endif @endforeach>
                                        دو حذفی
                                    </option>
                                    <option value="round"
                                            @foreach($tables as $table) @if($table->type == 'round') disabled @endif @endforeach>
                                        دوره ایی
                                    </option>
                                </select>
                                <button id="create-table" class="btn btn-success w-100 mt-2 text-dark">ایجاد</button>
                            </div>
                        </div>
                    @endif

                    <div class="row justify-content-center">
                        <div class="col-12 col-md-6 col-lg-4">
                            @if($tournament->status == 'raw')
                                <h6 class="font-weight-lighter mt-3 mb-3">لیست جداول مسابقه</h6>
                            @elseif($tournament->status == 'started')
                                <h6 class="font-weight-lighter mt-3 mb-3 text-center">برای ثبت ادامه مسابقه روی جدول زیر
                                    کلیک کنید</h6>
                            @endif
                            <ul id="tables-list" class="list-group col p-0">
                                @foreach($tables as $table)
                                    <li data-id="{{ $table->id }}"
                                        class="list-group-item pt-2 bg-dark text-light font-weight-light tournament-table-link">
                                        @if($table->type == 'single')یک حذفی@endif
                                        @if($table->type == 'double')دو حذفی@endif
                                        @if($table->type == 'round')دوره ایی@endif

                                        @if($table->status == 'raw')<span
                                            class="badge badge-primary float-left mt-1 text-dark py-1 px-3">پیش نمایش</span>
                                    </li>@endif
                                    @if($table->status == 'started')<span
                                        class="badge badge-primary float-left mt-1 text-dark py-1 px-3">درحال تکمیل</span></li>@endif
                                    @if($table->status == 'finished')<span
                                        class="badge badge-success float-left mt-1 text-dark py-1 px-3">پایان یافته</span></li>@endif

                                @endforeach
                            </ul>
                        </div>
                    </div>
                </div>
            </div>

        </main>

        @include('footer')

        <script src="{{ asset('js/jquery.sortable.min.js') }}"></script>
        <script>

            $.ajaxSetup({
                headers: {
                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                }
            });

            function saveParticipant() {
                $.ajax({
                    url: "{{ route('participants.store') }}",
                    method: "POST",
                    data: {
                        tournament_id: {{ $tournament->id }},
                        participants: allParticipants
                    },
                    success: function (data) {

                    },
                    error: function (err) {
                        console.log("Error: " + err);
                    }
                });
            };

            $("document").ready(function () {
                @if($participants != NULL)
                @foreach($participants as $participant)
                $(".sortable").append("<li class='participant pt-2' ondragend='dragendfunc(event)'><i class='fas fa-arrows-alt float-right mt-1 ml-2 arrows-icon' data-toggle='tooltip' data-placement='top' title='به کمک موس جابجا کنید'></i>" + "{{ $participant }}" + "<span class='trash-participant fas fa-trash'></span></li>");

                @endforeach
                @endif
                $(".sortable").sortable();

                @if($tournament->status != 'raw')
                $(".sortable").sortable("disable")
                @endif

            });

            let allParticipants = [];

            function dragendfunc() {
                allParticipants = [];
                $(".sortable li").each(function () {
                    allParticipants.push($(this).text())
                });
                allParticipants = allParticipants.filter(function (v) {
                    return v !== ''
                });

                saveParticipant();

            }

            $('.sortable').sortable();
            $("#participants-new").val("");

            $("#participants-wrapper").on('click', '#add-participant-button', function () {
                let participant = $("#participants-new").val();
                if (participant != "") {
                    $(".sortable").append("<li class='participant pt-2' ondragend='dragendfunc(event)'><i class='fas fa-arrows-alt float-right mt-1 ml-2 arrows-icon' data-toggle='tooltip' data-placement='top' title='به کمک موس جابجا کنید'></i>" + participant + "<span class='trash-participant fas fa-trash'></span></li>");
                }
                $('.sortable').sortable();
                $("#participants-new").val("");
                $("#participants-new").focus();

                allParticipants = [];
                $(".sortable li").each(function () {
                    allParticipants.push($(this).text())
                });

                saveParticipant();
            });

            $('#participants-wrapper').on('click', '.trash-participant', function () {

                $(this).parent('li').remove();

                allParticipants = [];
                $(".sortable li").each(function () {
                    allParticipants.push($(this).text())
                });

                $('.sortable').sortable();
                saveParticipant();

            });

            $(document).on('click', '#create-table', function () {
                let type = $('#tournament-type').val();

                if (type == 'none') {
                    $("#tournament-type").focus();
                } else {
                    $.ajax({
                        url: "{{ route('tables.store') }}",
                        method: "POST",
                        data: {
                            tournament_id: {{ $tournament->id }},
                            tournament_type: type
                        },
                        success: function (data) {
                            let id = data;
                            $("#tournament-type option[value=" + type + "]").attr('disabled','disabled');
                            $("#tournament-type").val('none');
                            if (data == 1){
                                alert('شما مجاز به افزودن شرکت کننده بیشتر نیستید! به زودی می توانید اکانت پیشرفته تهیه کنید و نامحدود شرکت کننده وارد کنید.')
                            } else {
                                let title = null;
                                if (type == 'single'){
                                    title = 'یک حذفی';
                                } else if (type == 'double'){
                                    title = 'دو حذفی';
                                } else if (type == 'round'){
                                    title = 'دوره ایی';
                                }
                                $("#tables-list").append('<li data-id=' + id + ' class="list-group-item pt-2 bg-dark text-light font-weight-light tournament-table-link">' + title + '<span class="badge badge-primary float-left mt-1 text-dark py-1 px-3">پیش نمایش</span></li>');
                            }

                        },
                        error: function (err) {
                            console.log("Error: " + err);
                        }
                    });
                }
            });


            $('#tables-list').on('click', '.tournament-table-link', function () {

                let id = $(this).data('id');

                @if($tournament->status == 'raw')
                    window.location.href = "{{ url('/tables') }}/" + id;
                @elseif($tournament->status == 'started')
                    window.location.href = "{{ url('/tables/progress') }}/" + id;
                @elseif($tournament->status == 'finished')
                    window.location.href = "{{ url('/tables/progress') }}/" + id;
                @endif

            });

            $('#delete-tournament').on('click', function (e) {
                e.preventDefault();
                var answer=confirm('آیا می خواهید مسابقه حذف شود?');
                if(answer){
                    $('#tournament-delete-form').submit();
                }
                else{
                    e.preventDefault();
                }
            });

        </script>

@endsection
