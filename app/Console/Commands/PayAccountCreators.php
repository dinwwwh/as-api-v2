<?php

namespace App\Console\Commands;

use App\Models\Account;
use DB;
use Illuminate\Console\Command;

class PayAccountCreators extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'account:pay';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Pay money for creators of bought accounts';

    /**
     * Create a new command instance.
     *
     * @return void
     */
    public function __construct()
    {
        parent::__construct();
    }

    /**
     * Execute the console command.
     *
     * @return int
     */
    public function handle()
    {
        try {
            DB::beginTransaction();

            $accounts = Account::whereNotNull('creator_id')
                ->whereNotNull('bought_at_price')
                ->where('confirmed_at', '<=', now())
                ->where('bought_at', '<=', now())
                ->whereNull('paid_at')
                ->with('creator')
                ->get();

            $accounts->each(function (Account $account) {
                $money = $account->bought_at_price - $account->tax;

                $account->creator->updateBalance($money, 'thanh toán tiền bán tài khoản #' . $account->getKey());
                $account->update([
                    'paid_at' => now(),
                ]);
            });

            DB::commit();
        } catch (\Throwable $th) {
            DB::rollBack();
            $this->error('Paid for account creators is fail.');
            throw $th;
        }

        $this->info($accounts->count() . ' accounts paid for creator successfully.');
    }
}
