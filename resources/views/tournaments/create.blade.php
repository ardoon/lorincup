@extends('layouts.app')

@section('content')

{{--    <link rel="stylesheet" href="{{ asset('css/album.css') }}">--}}

    <div class="container pt-5">

        <main role="main">

            <section class="jumbotron pb-0 pt-sm-0 pt-lg-2 text-center bg-transparent text-light">
                <div class="container">
                    <h3 style="font-weight: lighter">ایجاد مسابقه جدید</h3>
                    <p style="font-weight: lighter" class="lead text-muted">اطلاعات عمومی مربوط به مسابقه جدید رو وارد کنید</p>
                </div>
            </section>

            <div class="row justify-content-center">
                <form class="text-light col-12 col-lg-6 p-4 mb-5" method="post" action="{{ route('tournaments.store') }}">
                    @csrf
                    <div class="form-group">
                        <label for="tournament-title"><span class="required-star">*</span> عنوان مسابقه</label>
                        <input type="text" name="tournament_title" class="form-control bg-dark border-0 text-light" id="tournament-title" required>
                    </div>
                    <div class="form-group">
                        <label for="tournament-field">رشته ورزشی</label>
                        <select name="tournament_field" id="tournament-field" class="form-control bg-dark border-0 text-light">
                            <option value="">انتخاب کنید</option>
                            <option value="other">متفرقه</option>
                        </select>
                    </div>
                    <div class="form-group pb-3">
                        <label for="tournament-desc">شرح مختصر</label>
                        <textarea name="tournament_desc" class="form-control bg-dark border-0 text-light" id="tournament-desc"></textarea>
                    </div>
                    <button type="submit" class="btn btn-success float-left text-dark">ایجاد مسابقه</button>
                    <a href="{{ route('tournaments.index') }}" class="btn btn-secondary float-left text-dark ml-3">بازگشت</a>
                </form>
            </div>

        </main>

        @include('footer')
    </div>
@endsection
