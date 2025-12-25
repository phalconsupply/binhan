<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Services\AccountBalanceService;

class RecalculateTransactionBalances extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'transactions:recalculate-balances';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Tái tính toán số dư tài khoản cho tất cả giao dịch';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $this->info('Bắt đầu tái tính toán số dư tài khoản...');
        
        try {
            AccountBalanceService::recalculateAllBalances();
            
            $this->info('✅ Hoàn thành! Đã cập nhật số dư cho tất cả giao dịch.');
            
            // Hiển thị tổng quan
            $balances = AccountBalanceService::getBalancesSummary();
            
            $this->newLine();
            $this->info('📊 TỔNG QUAN SỐ DƯ:');
            $this->table(
                ['Tài khoản', 'Số dư'],
                [
                    ['🏢 Quỹ công ty', number_format($balances['company_fund'], 0, ',', '.') . 'đ'],
                    ['📊 Quỹ dự kiến chi', number_format($balances['company_reserved'], 0, ',', '.') . 'đ'],
                    ['💵 Khả dụng công ty', number_format($balances['company_available'], 0, ',', '.') . 'đ'],
                ]
            );
            
            return Command::SUCCESS;
        } catch (\Exception $e) {
            $this->error('❌ Lỗi: ' . $e->getMessage());
            return Command::FAILURE;
        }
    }
}
