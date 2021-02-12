@extends('main')

@section('content')
    <section class="content">
        <div class="container-fluid">
            <!-- Small boxes (Stat box) -->
            <div class="row">
                <div class="col-lg-12 col-12">
                    <!-- small box -->
                    <div href="/projects/add" class="small-box bg-info">
                        <h3 class="inner text-center">
                            {{$docsLabel}}: {{sizeof($docs)}}
                        </h3>
                        <div class="icon">
                            <i class="ion ion-bag"></i>
                        </div>
                        <a href="/{{$link}}/edit/0" class="small-box-footer">Добавить</a>
                    </div>
                </div>


                @if (sizeof($docs))
                    <div class="col-lg-12 col-12 text-center">
                        <div class="card">
                            <div class="card-body table-responsive p-0">
                                <table class="table table-striped table-valign-middle">
                                    <thead>
                                    <tr>
                                        @foreach ($fields as $field=>$lavel)
                                            <th>{{$lavel}}</th>
                                        @endforeach
                                        <th></th>
                                    </tr>
                                    </thead>
                                    <tbody>
                                    @foreach($docs as $doc)
                                        <tr>
                                            @foreach ($fields as $field=>$lavel)
                                                <td>{!! $doc->getAttr($field) !!}</td>
                                            @endforeach
                                            <td>
                                                <a class="fa fa-edit" href="/{{$link}}/edit/{{$doc->id}}"></a>
                                                <a class="fa fa-eye" href="/{{$link}}/view/{{$doc->id}}"></a>
                                            </td>
                                        </tr>
                                    @endforeach

                                    </tbody>
                                </table>
                            </div>
                        </div>
                    </div>

                @else

                    <div class="col-lg-12 col-12 text-center">
                        <h3>Нет проектов</h3>
                    </div>
                @endif

            </div>
        </div>
    </section>

@endsection