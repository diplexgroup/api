@extends('main')

@section('content')
    <section class="content">
        <div class="container-fluid">
            <!-- Small boxes (Stat box) -->
            <div class="row">
                <div class="col-lg-12 col-12 text-center">
                    <div class="card">
                        <div class="card-body table-responsive p-0">
                            <form action="/{{$link}}" method="POST">
                                @csrf
                                <div><h3 class="text-danger">{{$error}}</h3></div>
                                @if ($explorer_id)
                                    <div><h3 class="text-info">Explorer ID:  {{$explorer_id}}</h3></div>
                                @endif
                                <table class="table table-striped table-valign-middle">
                                    <thead>
                                    <tr>
                                        <th>Перевод монет</th>
                                        <th></th>
                                    </tr>
                                    </thead>
                                    <tbody>
                                    <tr>
                                        <td width="50%">Кошелёк отправителя</td>
                                        <td><input name="sender-wallet" class="form-control"/></td>
                                    </tr>
                                    <tr>
                                        <td width="50%">Приватный ключ</td>
                                        <td><input name="sender-priv-key" class="form-control"/></td>
                                    </tr>
                                    <tr>
                                        <td width="50%">Кошелёк получателя</td>
                                        <td><input name="receiver-wallet" class="form-control"/></td>
                                    </tr>
                                    <tr>
                                        <td width="50%">Сумма</td>
                                        <td><input name="amount" type="number" step="0.000001" min="0.000001" class="form-control"/></td>
                                    </tr>

                                    <tr>
                                        <td></td>
                                        <td>
                                            <button class="btn btn-success">OK</button>
                                        </td>
                                    </tr>
                                    </tbody>
                                </table>
                            </form>
                        </div>
                    </div>
                </div>

            </div>
        </div>
    </section>

@endsection
