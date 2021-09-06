<div class="container-fluid">
    <nav id="main-nav" class="navbar navbar-expand-md navbar-dark bg-dark rounded mt-1">
        <a class="navbar-brand" href="{{ url('/') }}">LorinCup</a>
        <button class="navbar-toggler" type="button" data-toggle="collapse" data-target="#navbarSupportedContent"
                aria-controls="navbarSupportedContent" aria-expanded="false" aria-label="Toggle navigation">
            <span class="navbar-toggler-icon"></span>
        </button>

        <div class="collapse navbar-collapse" id="navbarSupportedContent">
            <ul class="navbar-nav ml-auto">
                <li class="nav-item">
                    <a class="nav-link" href="{{ url('/tournaments') . '/' . $tournament->id }}"><i class="fa fa-arrow-right"></i> بازگشت</a>
                </li>
                <li class="nav-item">
                    <a class="nav-link" href="{{ route('home') }}"><i class="fa fa-tachometer-alt"></i> میزکار</a>
                </li>
                <li class="nav-item">
                    <a id="print-table-link" class="nav-link" href="#"><i class="fa fa-print"></i> چاپ</a>
                </li>
            </ul>
        @if($tournament->status == 'raw')
            <!-- Left Side Of Navbar -->
                <ul class="navbar-nav mr-auto">
                    @yield('custom-nav-sheet')
                    <li class="nav-item">
                        <!-- Button trigger modal -->
                        <button type="button"
                                class="btn nav-link text-dark mr-3 px-3 py-1 bg-success rounded float-left"
                                data-toggle="modal" data-target="#exampleModal">
                            شروع مسابقه
                        </button>
                    </li>
                </ul>
            @elseif($tournament->status == 'started')
                <ul class="navbar-nav mr-auto">
                    <li class="nav-item">
                        <!-- Button trigger modal -->
                        <button type="button"
                                class="btn nav-link text-dark mr-3 px-3 py-1 bg-success rounded float-left"
                                data-toggle="modal" data-target="#endModal">
                            پایان مسابقه
                        </button>
                    </li>
                </ul>
            @elseif($tournament->status == 'finished')
                <ul class="navbar-nav mr-auto">
                    <li class="nav-item">
                        <!-- Button trigger modal -->
                        <button id="open-table" type="button"
                                class="btn nav-link text-dark mr-3 px-3 py-1 bg-success rounded float-left"
                                data-toggle="modal" data-target="#openModal">
                            بازکردن مجدد
                        </button>
                    </li>
                </ul>
            @endif
        </div>
    </nav>
</div>

