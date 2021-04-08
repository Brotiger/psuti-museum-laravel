<?php

namespace App\Imports;

use App\Models\Graduate;
use Maatwebsite\Excel\Concerns\ToModel;
use Carbon\Carbon;
use Maatwebsite\Excel\Concerns\WithValidation;
use Illuminate\Support\Facades\Auth;
use Maatwebsite\Excel\Concerns\WithHeadingRow;

use Maatwebsite\Excel\Imports\HeadingRowFormatter;

HeadingRowFormatter::extend('custom', function($value) {
    return trim($value); 
});

HeadingRowFormatter::default('custom');

class GraduatesImport implements ToModel, WithValidation, WithHeadingRow
{
    /**
    * @param array $row
    *
    * @return \Illuminate\Database\Eloquent\Model|null
    */
    public function rules(): array
    {
        return [
            'Регистрационный номер' => 'unique:graduates,registrationNumber',
        ];
    }

    public function customValidationMessages()
    {
        return [
            'Регистрационный номер.unique' => 'Данные из этого файла были импортированы ранее.',
        ];
    }
    public function headingRow(): int
    {
        return 1;
    }

    public function model(array $row)
    {

        if(!empty($row['Серия документа']) && !empty($row['Номер документа']) && !empty($row['Фамилия получателя']) && !empty($row['Имя получателя'])){

            $confirmDelete = null;

            if(!empty($row['Подтверждение уничтожения'])){
                $confirmDelete = $row['Подтверждение уничтожения'] == 'Да'? true : false;
            }

            $first = null;

            if(!empty($row['Высшее образование, получаемое впервые'])){
                $row['Высшее образование, получаемое впервые'] == 'Да'? true : false;
            }

            return new Graduate([
                'addUserId' => Auth::user()->id,
                'documentName' => !empty($row['Наименование документа'])? $row['Наименование документа'] : $row['Название документа'],
                'documentType' => $row['Вид документа'],
                'documentStatus' => $row['Статус документа'],
                'confirmLoss' => $row['Подтверждение утраты'] == 'Да'? true : false,
                'confirmSwap' => $row['Подтверждение обмена'] == 'Да'? true : false,
                'confirmDelete' => $confirmDelete,
                'educationLevel' => $row['Уровень образования'],
                'series' => $row['Серия документа'],
                'number' => $row['Номер документа'],
                'issueDate' => date('Y-m-d', ((int) $row['Дата выдачи'] - 25569) * 86400),
                'registrationNumber' => $row['Регистрационный номер'],
                'specialtyCode' => $row['Код специальности, направления подготовки'],
                'specialtyName' => $row['Наименование специальности, направления подготовки'],
                'qualificationName' => $row['Наименование квалификации'],
                'enteredYear' => $row['Год поступления'],
                'exitYear' => $row['Год окончания'],
                'trainingPeriod' => (int) $row['Год окончания'] - (int) $row['Год поступления'],
                'lastName' => $row['Фамилия получателя'],
                'firstName' => $row['Имя получателя'],
                'secondName' => $row['Отчество получателя'],
                'dateBirthday' => date('Y-m-d', ((int) $row['Дата рождения получателя'] - 25569) * 86400),
                'sex' => $row['Пол получателя'],
                'citizenship' => !empty($row['Гражданство получателя (код страны по ОКСМ)'])? $row['Гражданство получателя (код страны по ОКСМ)'] : null,
                'educationForm' => !empty($row['Форма обучения'])? $row['Форма обучения'] : null,
                'first' => $first,
                'fundingSource' => !empty($row['Источник финансирования обучения'])? $row['Источник финансирования обучения'] : null,
                'snills' => !empty($row['СНИЛС'])? $row['СНИЛС']: null,
            ]);
        }
    }
}
