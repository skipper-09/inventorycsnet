<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class EmployeeTask extends Model
{
    protected $fillable = [
        'task_detail_id',
        'employee_id',
        'task_assign_id',
        'status'
    ];
    protected $primaryKey = 'id';

    public function taskDetail()
    {
        return $this->belongsTo(TaskDetail::class, 'task_detail_id', 'id');
    }

    public function employee()
    {
        return $this->belongsTo(Employee::class, 'employee_id', 'id');
    }

    public function taskAssign()
    {
        return $this->belongsTo(TaskAssign::class, 'task_assign_id', 'id');
    }

    public function taskReports()
    {
        return $this->hasMany(TaskReport::class, 'employee_task_id', 'id');
    }

    public function getStatusBadge($value)
    {
        switch ($value) {
            case 'complated':
                return '<span class="badge badge-label-success">Completed</span>';
            case 'pending':
                return '<span class="badge badge-label-warning">Pending</span>';
            default:
                return '<span class="badge badge-label-danger">Overdue</span>';
        }
    }

    public function getStatus()
    {
        switch ($this->status) {
            case 'complated':
                return '<span class="badge badge-success">Completed</span>';
            case 'pending':
                return '<span class="badge badge-info">Pending</span>';
            default:
                return '<span class="badge badge-danger">Overdue</span>';
        }
    }

}
