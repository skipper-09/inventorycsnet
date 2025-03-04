<?php

namespace App\Console\Commands;

use App\Models\EmployeeTask;
use Illuminate\Console\Command;
use Illuminate\Support\Carbon;

class UpdateEmployeeTaskStatus extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'task:update-employeetask-status';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Update Employetask Status Overdue for task not complated';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $currentDate = Carbon::now();

        $tasks = EmployeeTask::where('status', 'pending')
            ->whereHas('taskAssign', function ($query) use ($currentDate) {
                $query->where('assign_date', '<', $currentDate);
            })
            ->get();

        foreach ($tasks as $task) {
            $task->status = 'overdue';
            $task->save();

            $this->info("Task ID {$task->id} status updated to Overdue");
        }
    }
}
