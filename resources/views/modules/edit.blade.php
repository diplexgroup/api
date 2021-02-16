@extends('main')

@section('content')
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