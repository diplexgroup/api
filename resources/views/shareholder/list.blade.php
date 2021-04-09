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
                            {{$docsLabel}}: {{$count}}
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
                            @if ($mxPage > 1)
                                <div class="text-center font-weight-bold text-lg">
                                    @if ($page > 2) <a href="?page=1">1</a> <span>...</span> @endif
                                    @if ($page > 1) <a href="?page={{$page - 1}}">{{$page - 1}}</a> @endif
                                    @if ($page) <a href="?page={{$page}}">{{$page}}</a> @endif
                                    @if ($page < $mxPage) <a href="?page={{$page + 1}}">{{$page + 1}}</a> @endif
                                    @if ($page + 1 < $mxPage) <span>...</span> <a
                                            href="?page={{$mxPage}}">{{$mxPage}}</a> @endif
                                </div>
                            @endif
                        </div>
                    </div>

                @else
                    <div class="col-lg-12 col-12 text-center">
                        <h3>Нет данных</h3>
                    </div>
                @endif

            </div>
            <div class="row mt-2">
                <div class="d-flex justify-content-center full-width">
                <form method="POST" class="bg-white p-2" enctype="multipart/form-data">
                    @csrf
                    <div class="col-12">
                        <label class="form-group">
                            Загрузить csv
                            <input class="form-control" name="file" type="file"/>
                        </label>
                    </div>
                    <div class="col-12 text-right">
                        <button class="btn btn-success">Обновить</button>
                    </div>
                </form>
                </div>
            </div>
        </div>
    </section>

@endsection