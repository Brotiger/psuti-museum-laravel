<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Graduate extends Model
{
    use HasFactory;

    protected $fillable = [
        'addUserId',
        'documentName',
        'documentType',
        'documentStatus',
        'confirmLoss',
        'confirmSwap',
        'confirmDelete',
        'educationLevel',
        'series',
        'number',
        'issueDate',
        'registrationNumber',
        'specialtyCode',
        'specialtyName',
        'qualificationName',
        'enteredYear',
        'exitYear',
        'trainingPeriod',
        'lastName',
        'firstName',
        'secondName',
        'dateBirthday',
        'sex',
        'citizenship',
        'educationForm',
        'first',
        'fundingSource',
        'snills',
      ];
}
