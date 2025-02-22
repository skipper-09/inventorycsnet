<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Leave extends Model
{
    protected $primaryKey = "id";

    protected $fillable = [
        "employee_id",
        "start_date",
        "end_date",
        "reason",
        "status",
        "year",
    ];

    public function getStatusBadge($value)
    {
        switch ($value) {
            case 'approved':
                return '<span class="badge badge-label-success">Approved</span>';
            case 'pending':
                return '<span class="badge badge-label-warning">Pending</span>';
            case 'rejected':
                return '<span class="badge badge-label-danger">Rejected</span>';
            default:
                return '<span class="badge bg-secondary">Unknown</span>';
        }
    }

    public function employee()
    {
        return $this->belongsTo(Employee::class);
    }
}
