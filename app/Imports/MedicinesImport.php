<?php

namespace App\Imports;

use Illuminate\Support\Collection;
use Maatwebsite\Excel\Concerns\ToCollection;
use App\Models\Medicine;
use Maatwebsite\Excel\Concerns\ToModel;
use Maatwebsite\Excel\Concerns\WithHeadingRow;
use Maatwebsite\Excel\Concerns\WithValidation;
use Carbon\Carbon;
// // Convert Arabic/Persian numbers to English if needed
// $count = $this->convertArabicNumbers($row['count']);
// $subUnitPerUnit = $this->convertArabicNumbers($row['subunit_unit']);
// $countOfSubunit = $this->convertArabicNumbers($row['countofsubunit']);
// $pricePerUnit = $this->convertArabicNumbers($row['price_unit']);

// // Parse expiry date
// $expiryDate = $this->parseExpiryDate($row['expire_date'] ?? '');

class MedicinesImport implements ToModel, WithHeadingRow, WithValidation
{
    public function model(array $row)
    {
        // Parse and format the expiry date
        $expiryDate = $this->formatExpiryDate($row['expire_date'] ?? null);

        return new Medicine([
            'name' => $row['medicinename'],
            'type' => $row['unittypes'],
            'quantity' => $row['count'],
            'subunits_per_unit' => $row['subunit_unit'],
            'subunits_count' => $row['countofsubunit'],
            'price' => $row['price_unit'],
            'scientific_form' => $row['dosageform'],
            'expiry_date' => $expiryDate,
        ]);
    }

    private function formatExpiryDate($date)
    {
        // Default date if no date is provided
        if (empty($date)) {
            return '4030-12-01';
        }

        try {
            // Check if the date is in the format "9/2027" or "09/2027"
            if (preg_match('/^(\d{1,2})\/(\d{4})$/', $date, $matches)) {
                $month = str_pad($matches[1], 2, '0', STR_PAD_LEFT); // Ensure month is 2 digits
                $year = $matches[2];
                return "{$year}-{$month}-01"; // Always use 01 for day
            }

            // If the date format doesn't match, return default date
            return '4030-12-01';
        } catch (\Exception $e) {
            // If any error occurs, return default date
            return '4030-12-01';
        }
    }

    public function rules(): array
    {
        return [
            'medicinename' => 'required',
            'unittypes' => 'required',
            'count' => 'required',
            'subunit_unit' => 'required',
            'countofsubunit' => 'required',
            'price_unit' => 'required',
            'dosageform' => 'required',
            'expire_date' => 'nullable', // Changed to nullable since we have a default value
        ];
    }

    private function convertArabicNumbers($string)
    {
        $arabic = ['٠', '١', '٢', '٣', '٤', '٥', '٦', '٧', '٨', '٩'];
        $english = range(0, 9);
        return str_replace($arabic, $english, $string);
    }

    private function parseExpiryDate($date)
    {
        try {
            // If date is empty or invalid, return a far future date
            if (empty($date) || $date == '' || $date == null) {
                return Carbon::create(4020, 12, 31); // Far future date
            }

            // Try to parse the date
            $parts = explode('\\', $date);
            if (count($parts) == 2) {
                return Carbon::createFromFormat('m/Y', $parts[0] . '/' . $parts[1])->endOfMonth();
            }

            // If parsing fails, return far future date
            return Carbon::create(4020, 12, 31);
        } catch (\Exception $e) {
            // If any error occurs, return far future date
            return Carbon::create(4020, 12, 31);
        }
    }

    private function mapUnitType($type)
    {
        $types = [
            'علبة' => 'box',
            'شريط' => 'strip',
            'قطعة' => 'piece'
        ];
        return $types[$type] ?? 'box';
    }

    private function mapDosageForm($form)
    {
        $forms = [
            'كبسول/أقراص' => 'tablets/capsules',
            // Add more mappings as needed
        ];
        return $forms[$form] ?? $form;
    }
}
