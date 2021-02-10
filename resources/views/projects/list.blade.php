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
                            Всего проектов: {{sizeof($projects)}}
                        </h3>
                        <div class="icon">
                            <i class="ion ion-bag"></i>
                        </div>
                        <a href="#" class="small-box-footer">Добавить</a>
                    </div>
                </div>


                @if (sizeof($projects))

                @else

                    <div class="col-lg-12 col-12 text-center">
                        <h3>Нет проектов</h3>
                    </div>
                @endif
                <div class="col-lg-12 col-12 text-center">
                    <div class="card">
                        <div class="card-body table-responsive p-0">
                            <table class="table table-striped table-valign-middle">
                                <thead>
                                <tr>
                                    <th>Id</th>
                                    <th>Название</th>
                                    <th>Ссылка на проект</th>
                                    <th>Ссылка АПИ</th>
                                </tr>
                                </thead>
                                <tbody>
                                @foreach($projects as $project)
                                <tr>
                                    <td>
                                        <img src="dist/img/default-150x150.png" alt="Product 1"
                                             class="img-circle img-size-32 mr-2">
                                        {{$project->id}}
                                    </td>
                                    <td>
                                        {{$project->name}}
                                    </td>
                                    <td>
                                        {{$project->api_endpont}}
                                    </td>
                                    <td>
                                        {{$project->api_front_link}}
                                    </td>
                                </tr>
                                @endforeach

                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>

            </div>
        </div>
    </section>

@endsection