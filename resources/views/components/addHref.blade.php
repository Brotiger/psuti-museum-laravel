<!-- ссылка на сотрудника -->
<div class="form-group row modalAddHref p-3" style="display: none" id="modalEmp">
    <h4 class="h4 px-0">Добавление ссылки на сотрудника 
    @if($site == 'pguty' || $site == 'psuti')
        ПГУТИ
    @else
        КС ПГУТИ
    @endif
    </h4>
    <small class="mb-3 px-0">На выбор предлагаются первые 15 совпадений, если в списке нету того что вы искали, попробуйте задать параметры для поиска более конкретно</small>
    <div class="px-0">
        <input type="text" class="search-select form-control mb-3 searchField" placeholder="Фамилия" id="searchLastNameHref">
        <input type="text" class="search-select form-control mb-3 searchField" placeholder="Имя" id="searchFirstNameHref">
        <input type="text" class="search-select form-control mb-3 searchField" placeholder="Отчество" id="searchSecondNameHref">
        <div class="px-0">
            <input type="button" class="btn btn-danger mb-3 resetSearchHref" value="Сбросить">
            <input type="button" class="btn btn-primary mb-3" value="Поиск" id="searchEmp">
        </div>
    </div>
    <hr class="mb-3">
    <label class="mb-1 px-0">Результаты поиска</label>
    <div class="px-0">
        <select class="custom-select custom-select-lg form-control border border-secondary rounded-0 mb-3" id="searchEmpResult">
            <option value="">Не выбрано</option>
            @foreach($employees_search as $employee)
                <option value="{{ $employee->id }}">{{ $employee->lastName }} {{ $employee->firstName }} {{ $employee->secondName }}</option>
            @endforeach
        </select>
    </div>
    <label class="mb-1 px-0">Текст ссылки</label>
    <div class="px-0">
        <input type="text" class="search-select form-control mb-3" placeholder="Текст ссылки" id="empHrefText">
        <input type="button" class="btn btn-danger closeModal" value="Закрыть">
        <input type="button" class="btn btn-primary addModalHref" value="Добавить" id="addEmpHrefDes">
    </div>
</div>
<!-- ссылка на подразделение -->
<div class="form-group row modalAddHref p-3" style="display: none" id="modalUnit">
    <h4 class="h4 px-0">Добавление ссылки на подразделение 
        @if($site == 'pguty' || $site == 'psuti')
            ПГУТИ
        @else
            КС ПГУТИ
        @endif
    </h4>
    <small class="mb-3 px-0">На выбор предлагаются первые 15 совпадений, если в списке нету того что вы искали, попробуйте задать параметры для поиска более конкретно</small>
    <div class="px-0">
        <input type="text" class="search-select form-control mb-3 searchField" placeholder="Полное название подразделения" id="fullUnitNameHref">
        <input type="text" class="search-select form-control mb-3 searchField" placeholder="Сокращенное название подразделения" id="shortUnitNameHref">
        <input type="text" class="search-select form-control mb-3 searchField" placeholder="Тип подразделения" id="typeUnitHref">
        <div class="px-0">
            <input type="button" class="btn btn-danger mb-3 resetSearchHref" value="Сбросить">
            <input type="button" class="btn btn-primary mb-3" value="Поиск" id="searchUnit">
        </div>
    </div>
    <hr class="mb-3">
    <label class="mb-1 px-0">Результаты поиска</label>
    <div class="px-0">
        <select class="custom-select custom-select-lg form-control border border-secondary rounded-0 mb-3" id="searchUnitResult">
            <option value="">Не выбрано</option>
            @foreach($units_search as $unit)
                <option value="{{ $unit->id }}">{{ $unit->fullUnitName }} {{ $unit->shortUnitName? '(' . $unit->shortUnitName . ')' : '' }}</option>
            @endforeach
        </select>
    </div>
    <label class="mb-1 px-0">Текст ссылки</label>
    <div class="px-0">
        <input type="text" class="search-select form-control mb-3" placeholder="Текст ссылки" id="unitHrefText">
        <input type="button" class="btn btn-danger closeModal" value="Закрыть">
        <input type="button" class="btn btn-primary addModalHref" value="Добавить" id="addUnitHrefDes">
    </div>
</div>
<!-- ссылка на событие -->
<div class="form-group row modalAddHref p-3" style="display: none" id="modalEvent">
    <h4 class="h4 px-0">Добавление ссылки на событие 
        @if($site == 'pguty' || $site == 'psuti')
            ПГУТИ
        @else
            КС ПГУТИ
        @endif
    </h4>
    <small class="mb-3 px-0">На выбор предлагаются первые 15 совпадений, если в списке нету того что вы искали, попробуйте задать параметры для поиска более конкретно</small>
    <div class="px-0">
        <input type="text" class="search-select form-control mb-3 searchField" placeholder="Название события" id="eventNameHref">
        <label class="mb-1 px-0">Дата события</label>
        <input type="date" class="search-select form-control mb-3 searchField" placeholder="С" id="eventDateFromHref">
        <input type="date" class="search-select form-control mb-3 searchField" placeholder="По" id="eventDateToHref">
        <div class="px-0">
            <input type="button" class="btn btn-danger mb-3 resetSearchHref" value="Сбросить">
            <input type="button" class="btn btn-primary mb-3" value="Поиск" id="searchEvent">
        </div>
    </div>
    <hr class="mb-3">
    <label class="mb-1 px-0">Результаты поиска</label>
    <div class="px-0">
        <select class="custom-select custom-select-lg form-control border border-secondary rounded-0 mb-3" id="searchEventResult">
            <option value="">Не выбрано</option>
            @foreach($events_search as $event)
                <option value="{{ $event->id }}">{{ $event->name }}</option>
            @endforeach
        </select>
    </div>
    <label class="mb-1 px-0">Текст ссылки</label>
    <div class="px-0">
        <input type="text" class="search-select form-control mb-3" placeholder="Текст ссылки" id="eventHrefText">
        <input type="button" class="btn btn-danger closeModal" value="Закрыть">
        <input type="button" class="btn btn-primary addModalHref" value="Добавить" id="addEventHrefDes">
    </div>
</div>