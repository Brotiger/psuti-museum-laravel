<?php

namespace App\Imports;

use App\Models\Graduate;
use Maatwebsite\Excel\Concerns\ToModel;
use Carbon\Carbon;
use Illuminate\Support\Facades\Auth;
use Maatwebsite\Excel\Concerns\WithHeadingRow;

use Maatwebsite\Excel\Imports\HeadingRowFormatter;

HeadingRowFormatter::extend('custom', function($value) {
    return trim($value); 
});

HeadingRowFormatter::default('custom');

class GraduatesImport implements ToModel, WithHeadingRow
{
    /**
    * @param array $row
    *
    * @return \Illuminate\Database\Eloquent\Model|null
    */
    public function headingRow(): int
    {
        return 1;
    }

    public function model(array $row)
    {
        $registrationExist = (!empty($row['Регистрационный номер']) && Graduate::where('registrationNumber', $row['Регистрационный номер'])->get()->count()) ? true : false;
        if((!empty($row['Фамилия получателя']) || !empty($row['Фамилия'])) && (!empty($row['Имя получателя']) || !empty($row['Имя'])) && !$registrationExist){

            $confirmDelete = null;

            if(!empty($row['Подтверждение уничтожения'])){
                $confirmDelete = $row['Подтверждение уничтожения'] == 'Да'? true : false;
            }

            $first = null;

            if(!empty($row['Высшее образование, получаемое впервые'])){
                $row['Высшее образование, получаемое впервые'] == 'Да'? true : false;
            }

            if(!empty($row['Отчество получателя'])){
                $secondName = $row['Отчество получателя'];
            }else if (!empty($row['Отчество'])){
                $secondName = $row['Отчество'];
            }else{
                $secondName = null;
            }

            if(!empty($row['Код специальности, направления подготовки'])){
                $specialtyCode = $row['Код специальности, направления подготовки'];
            }else if(!empty($row['Код специальности по направлению'])){
                $specialtyCode = $row['Код специальности по направлению'];
            }else if(!empty($row['Код профессии, специальности'])){
                $specialtyCode = $row['Код специальности по справочнику'];
            }else if(!empty($row['Код профессии, специальности'])){
                $specialtyCode = $row['Код профессии, специальности'];
            }else{
                $specialtyCode = null;
            }

            if(!empty($row['Наименование специальности, направления подготовки'])){
                $specialtyName = $row['Наименование специальности, направления подготовки'];
            }else if(!empty($row['Направление подготовки/Специальность'])){
                $specialtyName = $row['Направление подготовки/Специальность'];
            }else if(!empty($row['Наименование профессии, специальности'])){
                $specialtyName = $row['Наименование профессии, специальности'];
            }else{
                $specialtyName = null;
            }

            if(!empty($row['Наименование квалификации'])){
                $qualificationName = $row['Наименование квалификации'];
            }else if(!empty($row['Квалификация/Степень'])){
                $qualificationName = $row['Квалификация/Степень'];
            }else{
                $qualificationName = null;
            }

            return new Graduate([
                'addUserId' => Auth::user()->id,
                'documentName' => !empty($row['Наименование документа'])? $row['Наименование документа'] : $row['Название документа'],
                'documentType' => $row['Вид документа'],
                'documentStatus' => !empty($row['Статус документа'])? $row['Статус документа'] : null,
                'confirmLoss' => $row['Подтверждение утраты'] == 'Да'? true : false,
                'confirmSwap' => $row['Подтверждение обмена'] == 'Да'? true : false,
                'confirmDelete' => $confirmDelete,
                'educationLevel' => !empty($row['Уровень образования'])? $row['Уровень образования'] : null,
                'series' => !empty($row['Серия документа'])? $row['Серия документа'] : null,
                'number' => !empty($row['Номер документа'])? $row['Номер документа'] : null,
                'issueDate' => date('Y-m-d', ((int) $row['Дата выдачи'] - 25569) * 86400),
                'registrationNumber' => $row['Регистрационный номер'],
                'specialtyCode' => $specialtyCode,
                'specialtyName' => $specialtyName,
                'qualificationName' => $qualificationName,
                'enteredYear' => $row['Год поступления'],
                'exitYear' => $row['Год окончания'],
                'trainingPeriod' => (int) $row['Год окончания'] - (int) $row['Год поступления'],
                'lastName' => !empty($row['Фамилия получателя'])? $row['Фамилия получателя'] : $row['Фамилия'],
                'firstName' => !empty($row['Имя получателя'])? $row['Имя получателя'] : $row['Имя'],
                'secondName' => $secondName,
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
