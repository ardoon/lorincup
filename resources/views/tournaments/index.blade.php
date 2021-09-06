@extends('layouts.app')

@section('content')
    <style>
        .bd-placeholder-img {
            font-size: 1.125rem;
            text-anchor: middle;
            -webkit-user-select: none;
            -moz-user-select: none;
            -ms-user-select: none;
            user-select: none;
        }

        @media (min-width: 768px) {
            .bd-placeholder-img-lg {
                font-size: 3.5rem;
            }
        }
    </style>

    <link rel="stylesheet" href="{{ asset('css/album.css') }}">

    <div class="container pt-5">

        <main role="main">

            <section class="jumbotron pt-sm-0 pt-lg-2 pb-5 text-center bg-transparent text-light">
                <div class="container">
                    <h3 style="font-weight: lighter">مسابقات شما</h3>
                    <p style="font-weight: lighter" class="lead text-muted">کلیه مسابقاتی که تا به حال ایجاد کرده اید اینجا در دسترس می باشد، میتوانید آنها را مرور و ویرایش کنید، همچنین می توانید از طریق دکمه زیر مسابقه جدید ایجاد کنید.</p>
                    <p>
                        <a href="{{ route('tournaments.create') }}" class="btn btn-success text-dark mt-2">ایجاد مسابقه جدید</a>
                    </p>
                </div>
            </section>

            <div class="album bg-transparent">
                <div class="container">

                    <div class="row">

                        @foreach($tournaments as $tournament)
                        <div class="col-sm-12 col-md-6 col-lg-4">
                            <div class="card mb-4 shadow-sm">
{{--                                <svg class="bd-placeholder-img card-img-top" width="100%" height="225" xmlns="http://www.w3.org/2000/svg" role="img" aria-label="Placeholder: Thumbnail" preserveAspectRatio="xMidYMid slice" focusable="false"><title>Placeholder</title><rect width="100%" height="100%" fill="#55595c"/><text x="50%" y="50%" fill="#eceeef" dy=".3em">فاقد تصویر</text></svg>--}}
{{--                                <span class="badge badge-light position-absolute tournament-type p-2">خام</span>--}}

                                <div class="card-body">
                                    <a class="card-link" href="{{ route('tournaments.show', ['tournament' => $tournament->id]) }}">{{ $tournament->title }}</a>
                                    <p class="card-text mt-2 font-weight-lighter">{{ $tournament->description }}</p>
                                    <div class="d-flex justify-content-between align-items-center">
                                        <div class="btn-group">
                                            <a href="{{ route('tournaments.show', ['tournament' => $tournament->id]) }}" class="btn btn-sm btn-outline-primary">ویرایش</a>
                                        </div>

                                        <small class="text-muted">{{ $tournament->participants_count }} شرکت کننده</small>
                                    </div>
                                </div>
                            </div>
                        </div>
                        @endforeach

                    </div>
                </div>
            </div>

        </main>

        @include('footer')
    </div>
@endsection
