@extends('layouts.app')
@section('content')
<div class="container">
    <div class="row justify-content-center">
        <div class="col-md-10">
            <div class="card mb-3">
                <div class="card-header">待審核任務欄</div>
                <div class="card-body">
                    @if ($tasks->get('notConfirmedTasks')->count() > 0)
                        <table class="table table-striped">
                            <thead class="thead-light">
                                <tr>
                                    <td scope="col">任務名</td>
                                    <td scope="col">敘述</td>
                                    <td scope="col">分數</td>
                                    <td scope="col">到期日</td>
                                    <td scope="col">剩餘次數</td>
                                    <td scope="col">待審核者</td>
                                    <td scope="col">是否通過</td>
                                </tr>
                            </thead>
                            @foreach ($tasks->get('notConfirmedTasks') as $task)
                                @foreach ($task->users as $user)
                                <tbody id="category-{{$task->category_id}}">
                                    <tr>
                                        <td>{{ $task->name }}</td>
                                        <td>{{ $task->description }}</td>
                                        <td>{{ $task->score }}</td>
                                        <td>{{ \Carbon\Carbon::parse($task->expired_at)
                                            ->tz('Europe/London')
                                            ->setTimeZone('Asia/Taipei')->locale('zh_TW')
                                            ->diffForHumans()
                                            }}</td>
                                        <td>{{ $task->remain_times }}</td>
                                        <td><span style="font:bold; color:blue; cursor:pointer" onclick="">{{$user->name}}</span></td>
                                        <td>
                                            <button class="btn btn-sm btn-primary">
                                                通過
                                            </button>
                                            <button class="btn btn-sm btn-secondary">
                                                駁回
                                            </button>
                                        </td>
                                    </tr>
                                </tbody>
                                @endforeach
                            @endforeach
                        </table>
                    @else
                    目前沒有待審核任務
                    @endif
                </div>
            </div>
            <div class="card">
                <div class="card-header">已審核任務欄</div>
                <div class="card-body">
                    @if ($tasks->get('confirmedTasks')->count() > 0)
                        <table class="table table-striped">
                            <thead class="thead-light">
                                <tr>
                                    <td scope="col">任務名</td>
                                    <td scope="col">敘述</td>
                                    <td scope="col">分數</td>
                                    <td scope="col">到期日</td>
                                    <td scope="col">剩餘次數</td>
                                    <td scope="col">完成任務者</td>
                                </tr>
                            </thead>
                            @foreach ($tasks->get('confirmedTasks') as $task)
                                @foreach ($task->users as $user)
                                <tbody id="category-{{$task->category_id}}">
                                    <tr>
                                        <td>{{ $task->name }}</td>
                                        <td>{{ $task->description }}</td>
                                        <td>{{ $task->score }}</td>
                                        <td>{{ \Carbon\Carbon::parse($task->expired_at)
                                            ->tz('Europe/London')
                                            ->setTimeZone('Asia/Taipei')->locale('zh_TW')
                                            ->diffForHumans()
                                            }}</td>
                                        <td>{{ $task->remain_times }}</td>
                                        <td>{{$user->name}}</td>
                                    </tr>
                                </tbody>
                                @endforeach
                            @endforeach
                        </table>
                    @else
                    目前沒有人完成任務
                    @endif
                </div>
            </div>
        </div>
    </div>
</div>
<script>
    $(() => {
        $('#catInputGroupSelect').change(() => {
            let selected = $("#catInputGroupSelect").find(":selected").val();
            if(selected > 0) {
                $('tbody').hide();
                $(`tbody#category-${$("#catInputGroupSelect").find(":selected").val()}`).show();
            }else if(selected < 0) {
                $('tbody').show();
            }
        })
    })
</script>
@endsection
