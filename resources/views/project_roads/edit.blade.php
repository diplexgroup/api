@extends('main')

@section('content')
    <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.6.0/jquery.min.js"></script>
    <script src="/js/main.js"></script>
    <section class="content">
        <div class="container-fluid">
            <!-- Small boxes (Stat box) -->
            <div class="row">



                @if ($doc)
                    <div class="col-lg-12 col-12 text-center">
                        <div class="card">
                            <div class="card-body table-responsive p-0">
                                <form action="/{{$link}}/edit/{{$doc->id ? $doc->id : 0}}" method="POST">
                                    @csrf
                                    <div><h3 class="text-danger">{{$error}}</h3></div>
                                <table class="table table-striped table-valign-middle">
                                    <thead>
                                    <tr>
                                        <th>{{$docLabel}} #{{$doc->id}}</th>
                                        <th><a href="/{{$link}}/view/{{$doc->id}}" class="fa fa-eye">просмотреть</a></th>
                                    </tr>
                                    </thead>
                                    <tbody>
                                    @foreach ($fields as $field=>$label)
                                        @include('base.input')
                                    @endforeach

                                    <tr id="fee-strategy-tr">
                                        <td>Стратегия комиссии</td>
                                        <td><textarea id="fee_strategy_textarea" name="forms[tax_strategy]">{{$doc->tax_strategy}}</textarea></td>
                                    </tr>


                                    <tr>
                                        <td></td>
                                        <td><button class="btn btn-success">OK</button></td>
                                    </tr>
                                    </tbody>
                                </table>
                                </form>
                            </div>
                        </div>
                    </div>

                @else

                    <div class="col-lg-12 col-12 text-center">
                        <h3>Ничего не найдено</h3>
                    </div>
                @endif

            </div>
        </div>
    </section>

@endsection
