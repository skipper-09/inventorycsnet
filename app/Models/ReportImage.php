<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ReportImage extends Model
{
    protected $primaryKey = "id";
    
    protected $fillable = [
        "report_task_id",
        "report_type",
        "image",
    ];
    
    public function taskReport() // Perbaikan dari ReportTask menjadi taskReport
    {
        return $this->belongsTo(TaskReport::class, 'report_task_id', 'id');
    }
}