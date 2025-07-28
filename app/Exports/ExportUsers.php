<?php

namespace App\Exports;

use App\Http\Filters\UserFilters;
use App\Models\User;
use Illuminate\Support\Collection;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;

class ExportUsers implements FromCollection, WithHeadings, WithMapping
{
    public function __construct(private UserFilters $filters) {}

    public function collection(): Collection
    {
        return User::filter($this->filters)
            ->withCount('properties')
            ->orderBy('name')
            ->get();
    }

    public function headings(): array
    {
        return [
            'Name',
            'Email',
            'Linked Properties',
            'Phone Number',
            'Role',
        ];
    }

    public function map($user): array
    {
        return [
            $user->name,
            $user->email,
            $user->properties_count,
            $user->phone_number,
            $user->role,
        ];
    }
}
