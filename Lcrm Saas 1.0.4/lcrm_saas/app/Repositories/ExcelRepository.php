<?php namespace App\Repositories;


interface ExcelRepository
{
    public function load($filePath);
    public function getAllRows($filePath);
}