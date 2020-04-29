@auth
<script>
    $(document).ready(() => {
        $("#add-task-form").submit((event) => {
            event.preventDefault();
            addTask();
        });
    });
    function addTask(){
        const data = {
            'name': $('#add-task-name').val(),
            'category_id': $("#add-task-category").find(":selected").val(),
            'description': $('#add-task-description').val(),
            'expired_at': $('#add-task-expired-at').val(),
            'score': $('#add-task-score').val(),
            'remain_times': $('#add-task-remain').val()
        };
        $.ajaxSetup({
            headers: {
                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content'),
                'authorization': `Bearer ${$('#api-token').val()}`
            }
        });
        $.ajax({
            type: "POST",
            contentType: "application/json",
            url: `/api/v1/task`,
            dataType: 'json',
            data: JSON.stringify(data),
            success: (result) => {
                const task = result.data
                const expiredAt = task.expired_at.split(" ")[0];
                let param = {'id': task.id,'name': task.name,'description': task.description,'score':task.score,'expired_at': task.expiredAt,'remain_times': task.remain_times};
                $('#success-msg').empty();
                $('#success-msg').prepend(`任務 ${task.name} 新增成功`);
                $('#success-msg').slideToggle();
                if(typeof $('#current-task').attr('id') == 'string'){
                    $(`#current-task thead`).after(`
                        <tbody id="category-${task.category.id}">
                            <tr class="table-success" id="edit-task-${task.id}">
                                <td>${task.name}</td>
                                <td>${task.description}</td>
                                <td>${task.score}</td>
                                <td>${expiredAt}</td>
                                <td class="text-center">${task.remain_times}</td>
                                <td><button class="btn btn-sm btn-primary" onclick="getTask(this)">修改</button><td>
                            </tr>
                        </tbody>
                    `);
                    // solve the problem that the above tbody add an extra td.
                    $(`#edit-task-${task.id}`).children(':last').detach();
                } else {
                    $('.card-body:eq(3)').empty().append(`
                        <table class="table table-striped" id="current-task">
                            <thead class="thead-light">
                                <tr>
                                    <td scope="col">任務名</td>
                                    <td scope="col">敘述</td>
                                    <td scope="col">分數</td>
                                    <td scope="col">到期日</td>
                                    <td scope="col">剩餘次數</td>
                                    <td scope="col">修改任務</td>
                                </tr>
                            </thead>
                            <tbody id="category-${task.category.id}">
                                <tr class="table-success" id="edit-task-${task.id}">
                                    <td>${task.name}</td>
                                    <td>${task.description}</td>
                                    <td>${task.score}</td>
                                    <td>${expiredAt}</td>
                                    <td class="text-center">${task.remain_times}</td>
                                    <td><button class="btn btn-sm btn-primary" onclick="getTask(this)">修改</button><td>
                                </tr>
                            </tbody>
                        </table>
                    `)
                }
                $('.card-body:eq(3)').slideDown();

                apiToken();
                setTimeout(()=>{
                    $('#success-msg').slideToggle();
                }, 2000);

            },
            error: (e) => {
                console.log("ERROR: ", e);
                $('#error-msg').empty();
                $('#error-msg').prepend(`任務新增失敗`);
                $('#error-msg').slideToggle();
                setTimeout(()=>{
                    $('#error-msg').slideToggle();
                }, 2000);
            },
        });
    }
</script>
@endauth