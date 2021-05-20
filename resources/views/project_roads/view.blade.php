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
                                <table class="table table-striped table-valign-middle">
                                    <thead>
                                    <tr>
                                        <th>{{$docLabel}} #{{$doc->id}}</th>
                                        <th><a href="/{{$link}}/edit/{{$doc->id}}" class="fa fa-edit">редактировать</a></th>
                                    </tr>
                                    </thead>
                                    <tbody>
                                    @foreach ($fields as $field=>$label)
                                        <tr>
                                            <td>{{$label}}</td>
                                            <td>{!! $doc->getAttr($field) !!}</td>
                                        </tr>
                                    @endforeach

                                    </tbody>
                                </table>
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
