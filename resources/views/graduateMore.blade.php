<x-app-layout>
    <div class="container">
        <div class="alert alert-primary position-fixed bottom-1 right-1" role="alert">Вы внесли:<br><strong id="counter">{{ $counter }}</strong> файлов</div>
        <form enctype="multipart/form-data" class="moreGraduateForm mt-5 pb-4">
            <h1 class="h1">Подробная информация о выпускнике</h1>
            <div class="my-4">
                <div class="mb-3">
                    <div class="row">
                        <label class="col-sm-3 col-form-label">Фамилия</label>
                        <div class="col-sm-9">
                            <input type="text" class="form-control" placeholder="Фамилия"  value="{{ !empty($graduate)? $graduate->lastName : '' }}" disabled>
                        </div>
                    </div>
                </div>
                <div class="mb-3 row">
                    <label class="col-sm-3 col-form-label">Имя</label>
                    <div class="col-sm-9">
                        <input type="text" class="form-control" placeholder="Имя" value="{{ !empty($graduate)? $graduate->firstName : '' }}" disabled>
                    </div>
                </div>
                <div class="mb-3 row">
                    <label class="col-sm-3 col-form-label">Отчество</label>
                    <div class="col-sm-9">
                        <input type="text" class="form-control" placeholder="Отчетство" value="{{ !empty($graduate)? $graduate->secondName : '' }}" disabled>
                    </div>
                </div>
                <div class="form-group mb-3 row">
                    <label class="col-3 col-form-label">Дата рождения</label>
                    <div class="col-sm-9">
                        <input class="form-control" type="date" value="{{ !empty($graduate)? $graduate->dateBirthday : '' }}" disabled>
                    </div>
                </div>
                <div class="form-group mb-3 row">
                    <label class="col-3 col-form-label">СНИЛС</label>
                    <div class="col-sm-9">
                        <input class="form-control" type="text" value="{{ !empty($graduate)? $graduate->snills : '' }}" disabled>
                    </div>
                </div>
                <div class="form-group mb-3 row">
                    <label class="col-3 col-form-label">Источник финансирования обучения</label>
                    <div class="col-sm-9">
                        <input class="form-control" type="text" value="{{ !empty($graduate)? $graduate->fundingSource : '' }}" disabled>
                    </div>
                </div>
                <div class="form-group mb-3 row">
                    <label class="col-3 col-form-label">Высшее образование, получаемое впервые</label>
                    <div class="col-sm-9">
                        <input class="form-control" type="checkbox" {{$graduate->first? 'checked' : ''}} disabled>
                    </div>
                </div>
                <div class="form-group mb-3 row">
                    <label class="col-3 col-form-label">Форма обучения</label>
                    <div class="col-sm-9">
                        <input class="form-control" type="text" value="{{ $graduate->educationForm }}" disabled>
                    </div>
                </div>
                <div class="form-group mb-3 row">
                    <label class="col-3 col-form-label">Гражданство</label>
                    <div class="col-sm-9">
                        <input class="form-control" type="text" value="{{ $graduate->citizenship }}" disabled>
                    </div>
                </div>
                <div class="form-group mb-3 row">
                    <label class="col-3 col-form-label">Пол</label>
                    <div class="col-sm-9">
                        <input class="form-control" type="text" value="{{ $graduate->sex }}" disabled>
                    </div>
                </div>
                <div class="form-group mb-3 row">
                    <label class="col-3 col-form-label">Срок обучения</label>
                    <div class="col-sm-9">
                        <input class="form-control" type="text" value="{{ $graduate->trainingPeriod }}" disabled>
                    </div>
                </div>
                <div class="form-group mb-3 row">
                    <label class="col-3 col-form-label">Год поступления</label>
                    <div class="col-sm-9">
                        <input class="form-control" type="text" value="{{ $graduate->enteredYear }}" disabled>
                    </div>
                </div>
                <div class="form-group mb-3 row">
                    <label class="col-3 col-form-label">Год окончания</label>
                    <div class="col-sm-9">
                        <input class="form-control" type="text" value="{{ $graduate->exitYear }}" disabled>
                    </div>
                </div>
                <div class="form-group mb-3 row">
                    <label class="col-3 col-form-label">Наименование специальности, направления подготовки</label>
                    <div class="col-sm-9">
                        <input class="form-control" type="text" value="{{ $graduate->specialtyName }}" disabled>
                    </div>
                </div>
                <div class="form-group mb-3 row">
                    <label class="col-3 col-form-label">Код специальности, направления подготовки</label>
                    <div class="col-sm-9">
                        <input class="form-control" type="text" value="{{ $graduate->specialtyCode }}" disabled>
                    </div>
                </div>
                <div class="form-group mb-3 row">
                    <label class="col-3 col-form-label">Регистрационный номер</label>
                    <div class="col-sm-9">
                        <input class="form-control" type="text" value="{{ $graduate->registrationNumber }}" disabled>
                    </div>
                </div>
                <div class="form-group mb-3 row">
                    <label class="col-3 col-form-label">Дата выдачи</label>
                    <div class="col-sm-9">
                        <input class="form-control" type="date" value="{{ $graduate->issueDate }}" disabled>
                    </div>
                </div>
                <div class="form-group mb-3 row">
                    <label class="col-3 col-form-label">Номер документа</label>
                    <div class="col-sm-9">
                        <input class="form-control" type="text" value="{{ $graduate->number }}" disabled>
                    </div>
                </div>
                <div class="form-group mb-3 row">
                    <label class="col-3 col-form-label">Серия документа</label>
                    <div class="col-sm-9">
                        <input class="form-control" type="text" value="{{ $graduate->series }}" disabled>
                    </div>
                </div>
                <div class="form-group mb-3 row">
                    <label class="col-3 col-form-label">Уровень образования</label>
                    <div class="col-sm-9">
                        <input class="form-control" type="text" value="{{ $graduate->educationLevel }}" disabled>
                    </div>
                </div>
                <div class="form-group mb-3 row">
                    <label class="col-3 col-form-label">Подтверждение уничтожения</label>
                    <div class="col-sm-9">
                        <input class="form-control" type="checkbox" {{$graduate->confirmDelete? 'checked' : ''}} disabled>
                    </div>
                </div>
                <div class="form-group mb-3 row">
                    <label class="col-3 col-form-label">Подтверждение обмена</label>
                    <div class="col-sm-9">
                        <input class="form-control" type="checkbox" {{$graduate->confirmSwap? 'checked' : ''}} disabled>
                    </div>
                </div>
                <div class="form-group mb-3 row">
                    <label class="col-3 col-form-label">Подтверждение утраты</label>
                    <div class="col-sm-9">
                        <input class="form-control" type="checkbox" {{$graduate->confirmLoss? 'checked' : ''}} disabled>
                    </div>
                </div>
                <div class="form-group mb-3 row">
                    <label class="col-3 col-form-label">Статус документа</label>
                    <div class="col-sm-9">
                        <input class="form-control" type="text" value="{{ $graduate->documentStatus }}" disabled>
                    </div>
                </div>
                <div class="form-group mb-3 row">
                    <label class="col-3 col-form-label">Вид документа</label>
                    <div class="col-sm-9">
                        <input class="form-control" type="text" value="{{ $graduate->documentType }}" disabled>
                    </div>
                </div>
                <div class="form-group mb-3 row">
                    <label class="col-3 col-form-label">Наименование документа</label>
                    <div class="col-sm-9">
                        <input class="form-control" type="text" value="{{ $graduate->documentName }}" disabled>
                    </div>
                </div>
            </div>
        </form>
    </div>
</x-app-layout>