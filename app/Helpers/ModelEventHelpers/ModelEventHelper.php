<?php 
namespace App\Helpers\ModelEventHelpers;
use Carbon\Carbon;
use App\Models\User;  
use App\Models\ActivityLog;  
use App\Models\DocumentType;  
 
class ModelEventHelper
{
    
     
    /**
     * Compare a model instance's current values vs incoming updated data for selected columns.
     *
     * @param  class-string<Model>  $modelClass
     * @param  int|string           $modelId
     * @param  array<int,string>    $columnsToCheck
     * @param  array<string,mixed>  $updatedData
     * @return array{
     *   found: bool,
     *   changed: bool,
     *   changes: array<string, array{from:mixed,to:mixed}>,
     *   checked: array<int,string>,
     * }
     */
    public static function detect_model_changes(
        string $modelClass,
        int|string $modelId,
        array $columnsToCheck,
        array $updatedData
    ): array {
        /** @var Model|null $model */
        $model = $modelClass::query()->find($modelId);

        if (!$model) {
            return [
                'found'   => false,
                'changed' => false,
                'changes' => [],
                'checked' => $columnsToCheck,
            ];
        }

        $changes = [];
        $data_comparisons = [];

        foreach ($columnsToCheck as $column) {
            // Only compare if the incoming data actually contains the key
            if (!array_key_exists($column, $updatedData)) {
                continue;
            }

            $old = $model->getAttribute($column);
            $new = $updatedData[$column];

            // dd("old: ".$old." =>  new:".$new);

            // Normalize common types to avoid false positives (e.g., "1" vs 1)
            $oldNormalized = ModelEventHelper::normalize_for_compare($old);
            $newNormalized = ModelEventHelper::normalize_for_compare($new);

            $data_comparisons[$column] = [
                'from' => $old,
                'to'   => $new,
            ];


            if ($oldNormalized !== $newNormalized) {
                $changes[$column] = [
                    'from' => $old,
                    'to'   => $new,
                ];
            }
        }

        return [
            'found'   => true,
            'changed' => !empty($changes),
            'changes' => $changes,
            'data_comparisons' => $data_comparisons,
            'checked' => $columnsToCheck,
        ];
    } 
    /**
     * Sample Usage:
        $model_columns_keys_to_check = ['name', 'order'];

        $updated_data = [
            'name'       => $name,
            'order'      => $index + 1,
            'created_by' => Auth::id(),
            'updated_by' => Auth::id(),
        ];

        $result = detect_model_changes(
            \App\Models\DocumentType::class,
            $modelId,
            $model_columns_keys_to_check,
            $updated_data
        );

        if (!$result['found']) {
            // handle missing model
        }

        if ($result['changed']) {
            // there are changes
            // $result['changes'] contains per-column diffs
        } else {
            // no changes on name/order
        }

     * 
     * 
     */




 
    /**
     * Normalize values for strict comparison.
     * - trims strings
     * - converts numeric strings to numbers
     * - keeps null as null
     */
    public static function normalize_for_compare(mixed $value): mixed
    {
        if (is_string($value)) {
            $value = trim($value);

            // If it's a numeric string, normalize to int/float
            if (is_numeric($value)) {
                return str_contains($value, '.') ? (float) $value : (int) $value;
            }

            return $value;
        }

        // For numbers, bools, null, arrays, etc., return as-is
        return $value;
    } 



}
