<x-app-layout>
    <div class="container">
        <div class="alert alert-primary position-fixed bottom-1 right-1" role="alert">Вы внесли:<br><strong id="counter">{{ $counter }}</strong> подразделений</div>
        <div class="alert alert-danger" style="display: none" role="alert" id="error-message">Ошибка сервера, свяжитесь с системным администратором.</div>
        <div class="mt-5 dbList">
            <h1 class="h1">Список добавленных вами подразделений</h1>
            @if($units->count() > 0)
            <div class="row mb-1">
                <span class="col-9"><small>Для того что бы отредактировать информацию о подразделении нажмите на него</small></span>
            </div>
            <table class="table table-hover">
                <thead>
                    <tr>
                        <th>Полное название подразделения</th>
                        <th>Сокращенное название подразделения</th>
                        <th>Тип подразделения</th>
                        <th>Дата создания</th>
                        <th colspan="2">Дата прекращения</th>
                    </tr>
                    <tr>
                        <th><input type="text" class="form-control" placeholder="Полное название подразделения" filter-field id="fullUnitName" autocomplete="off"></th>
                        <th><input type="text" class="form-control" placeholder="Сокращенное название подразделения" filter-field id="shortUnitName" autocomplete="off"></th>
                        <th><input type="text" class="form-control" placeholder="Тип подразделения" filter-field id="typeUnit" autocomplete="off"></th>
                        <th><input type="date" class="form-control mb-1" placeholder="С:" filter-field id="creationDateFrom"><input type="date" class="form-control" placeholder="По:" filter-field id="creationDateTo"></th>
                        <th><input type="date" class="form-control mb-1" placeholder="С:" filter-field id="terminationDateFrom"><input type="date" class="form-control" placeholder="По:" filter-field id="terminationDateTo"></th>
                        <th><button class="form-control btn btn-danger mb-1" id="reset"><i class="bi bi-arrow-counterclockwise"></i></button><button class="form-control btn btn-primary" id="search"><i class="bi bi-search"></i></button></th>
                    </tr>
                </thead>
                <tbody id="unitsTable">
                    @foreach($units as $unit)
                        <tr class="recordRow" unit-id="{{ $unit->id }}">
                            <td>{{ $unit->fullUnitName }}</td>
                            <td>{{ $unit->shortUnitName }}</td>
                            <td>{{ $unit->typeUnit }}</td>
                            <td>{{ !empty($unit->creationDate)? date('m-d-Y', strtotime($unit->creationDate)) : '' }}</td>
                            <td colspan="2">{{ !empty($unit->terminationDate)? date('m-d-Y', strtotime($unit->terminationDate)) : '' }}</td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
            @else
            <p>
                Здесь будет отображаться список добавленных вами подразделений, что бы добавить подразделение перейдите на подвкладку <strong>добавить подразделение</strong>
            </p>
            @endif
        </div>
    </div>
</x-app-layout>
<script>
    $(document).ready(function(){
        $('#unitsTable').delegate('.recordRow', 'click', function(){
            let unitId = $(this).attr('unit-id')
            window.location.href = 'edit_unit' + '/' + unitId;
        });

        $('#search').on('click', function(){
            startLoading();
            var formData = {};

            $('[filter-field]').each(function(i, ell){
                formData[ell.id] = $(this).val();
            });

            let res = $.ajax({
                url: "{{route('units_list')}}",
                type: "GET",
                processData: true,
                contentType: false,
                data: formData,
                headers: {
                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                },
                success: function(data){
                    stopLoading();
                    let positionParameters = location.pathname.indexOf('?');
                    let url = location.pathname.substring(positionParameters, location.pathname.length);
                    let newUrl = url + '?';
                    for(key in formData){
                        if(formData[key]){
                            newUrl += key + "=" + formData[key] + "&";
                        }
                    }
                    newUrl = newUrl.slice(0, -1);
                    history.pushState({}, '', newUrl);

                    $('#unitsTable').html(data)
                },
                error: function(data){
                    stopLoading();
                    $('#error-message').fadeIn(300).delay(2000).fadeOut(300);
                }
            });
        });

        $('#reset').on('click', function(){
            $("[filter-field]").each(function(){
                $(this).val("");
            });
            $('#search').click();
        });
    });
</script>