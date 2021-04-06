<?php

namespace App\Imports;

use App\Models\Graduate;
use Maatwebsite\Excel\Concerns\ToModel;
use Carbon\Carbon;
use Maatwebsite\Excel\Concerns\WithStartRow;
use Maatwebsite\Excel\Concerns\WithValidation;
use Illuminate\Support\Facades\Auth;

class GraduatesImport implements ToModel, WithStartRow, WithValidation
{
    /**
    * @param array $row
    *
    * @return \Illuminate\Database\Eloquent\Model|null
    */
    public function rules(): array
    {
        return [
            '10' => 'unique:graduates,registrationNumber',
        ];
    }

    public function customValidationMessages()
    {
        return [
            '10.unique' => 'Данные из этого файла были импортированы ранее.',
        ];
    }

    public function startRow(): int
    {
        return 2;
    }

    public function model(array $row)
    {
        if(!empty($row[8]) && !empty($row[7]) && !empty($row[18]) && !empty($row[19])){
            return new Graduate([
                'addUserId' => Auth::user()->id,
                'documentName' => $row[0],
                'documentType' => $row[1],
                'documentStatus' => $row[2],
                'confirmLoss' => $row[3] == 'Да'? true : false,
                'confirmSwap' => $row[4] == 'Да'? true : false,
                'confirmDelete' => $row[5] == 'Да'? true : false,
                'educationLevel' => $row[6],
                'series' => $row[7],
                'number' => $row[8],
                'issueDate' => date('Y-m-d', ($row[9] - 25569) * 86400),
                'registrationNumber' => $row[10],
                'specialtyCode' => $row[11],
                'specialtyName' => $row[12],
                'qualificationName' => $row[13],
                'enteredYear' => $row[15],
                'exitYear' => $row[16],
                'trainingPeriod' => (int) $row[16] - (int) $row[15],
                'lastName' => $row[18],
                'firstName' => $row[19],
                'secondName' => $row[20],
                'dateBirthday' => date('Y-m-d', ($row[21] - 25569) * 86400),
                'sex' => $row[22],
                'citizenship' => $row[23],
                'educationForm' => $row[24],
                'first' => $row[25] == 'Да'? true : false,
                'fundingSource' => $row[26],
                'snills' => $row[27],
            ]);
        }
    }
}
