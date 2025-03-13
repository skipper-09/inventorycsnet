<?php

namespace Database\Seeders;

use App\Models\User;
// use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        $this->call([
            PermissionSeeder::class,
            RoleSeeder::class,
            UserSeeder::class,
            UnitProductSeeder::class,
            ProductSeeder::class,
            BranchSeeder::class,
            SettingSeeder::class,
            TaskTemplateSeeder::class,
            TaskDataSeeder::class,
            TaskDetailSeeder::class,
            DepartmentSeeder::class,
            PositionSeeder::class,
            EmployeeSeeder::class,
            DeductionTypeSeeder::class,
            // EmployeeLeaveSeeder::class,
            AllowanceTypeSeeder::class,
            // TaskReportSeeder::class,
            // BranchProductStockSeeder::class,
            // OutcomeProductSeeder::class,
            // TransferProductSeeder::class,
            ZoneOdpSeeder::class,
            OdpSeeder::class,
            CompanySeeder::class,
            OfficeSeedeer::class,
        ]);
    }
}
