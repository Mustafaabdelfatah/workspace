<?php

namespace Modules\Core\Imports;

use Illuminate\Support\Facades\Validator;
use Maatwebsite\Excel\Concerns\SkipsEmptyRows;
use Maatwebsite\Excel\Concerns\SkipsFailures;
use Maatwebsite\Excel\Concerns\SkipsOnFailure;
use Maatwebsite\Excel\Concerns\ToModel;
use Maatwebsite\Excel\Concerns\WithHeadingRow;

class ImportExcelData implements ToModel, WithHeadingRow, SkipsOnFailure, SkipsEmptyRows
{
    use SkipsFailures;

    protected $validationRules;
    protected $fieldMappings;
    protected $importedData = [];  // Initialize the imported data array

    public function __construct(array $validationRules, array $fieldMappings)
    {
        $this->validationRules = $validationRules;
        $this->fieldMappings = $fieldMappings;
    }

    public function model(array $row)
    {
        // Validate the row with the provided validation rules
        $validator = Validator::make($row, $this->validationRules);

        if ($validator->fails()) {
            // Skip the row if it fails validation
            return null;
        }

        // Map row data to model attributes based on provided mappings
        $modelData = count($this->fieldMappings) > 0 ? [] : $row;
        foreach ($this->fieldMappings as $dbField => $excelField) {
            $modelData[$dbField] = $row[$excelField] ?? null;
        }

        // Store the valid row data in the imported data array
        $this->importedData[] = $modelData;

        // Return null, as we don't want to instantiate any models
        return null;
    }

    public function getImportedData()
    {
        return $this->importedData;
    }

    public function importData()
    {
        return $this->importedData;  // Just return the collected data
    }
}
