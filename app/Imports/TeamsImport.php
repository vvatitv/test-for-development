<?php

namespace App\Imports;

use App\Models\User;
use Maatwebsite\Excel\Concerns\ToModel;
use Illuminate\Support\Collection;
use Maatwebsite\Excel\Concerns\ToCollection;
use Maatwebsite\Excel\Concerns\Importable;

class TeamsImport implements ToCollection
{
    use Importable;
    
    public function collection(Collection $rows)
    {
        
    }
}
