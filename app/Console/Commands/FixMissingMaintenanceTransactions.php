<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\VehicleMaintenance;

class FixMissingMaintenanceTransactions extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'maintenance:fix-transactions';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Tạo transactions cho các VehicleMaintenance còn thiếu';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $this->info('=== Tạo Transactions cho VehicleMaintenance còn thiếu ===');
        $this->newLine();

        $missingTransactions = VehicleMaintenance::whereDoesntHave('transaction')
            ->with(['maintenanceService', 'vehicle'])
            ->orderBy('date', 'desc')
            ->get();

        $totalMissing = $missingTransactions->count();
        
        if ($totalMissing === 0) {
            $this->info('✓ Tất cả VehicleMaintenance đã có transaction!');
            return 0;
        }

        $this->warn("Tìm thấy {$totalMissing} VehicleMaintenance chưa có transaction");
        $this->newLine();

        if (!$this->confirm('Bạn có muốn tạo transactions cho các bản ghi này?', true)) {
            $this->info('Đã hủy.');
            return 0;
        }

        $created = 0;
        $errors = 0;

        $progressBar = $this->output->createProgressBar($totalMissing);
        $progressBar->start();

        foreach ($missingTransactions as $m) {
            try {
                $transaction = $m->createTransaction();
                if ($transaction) {
                    $created++;
                } else {
                    $errors++;
                }
            } catch (\Exception $e) {
                $this->error("\nLỗi khi tạo transaction cho VehicleMaintenance #{$m->id}: {$e->getMessage()}");
                $errors++;
            }
            $progressBar->advance();
        }

        $progressBar->finish();
        $this->newLine(2);

        $this->info("=== Kết quả ===");
        $this->info("✓ Tạo thành công: {$created}");
        if ($errors > 0) {
            $this->error("✗ Lỗi: {$errors}");
        }
        $this->info("Tổng: {$totalMissing}");

        return 0;
    }
}
