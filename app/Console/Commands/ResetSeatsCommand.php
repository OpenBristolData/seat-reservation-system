<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\Reservation;
use Carbon\Carbon;

class ResetSeatsCommand extends Command
{
    protected $signature = 'seats:reset';
    protected $description = 'Reset seat reservations daily after 4 PM';

    public function handle(): int
    {
        $now = Carbon::now();
        
        if ($now->hour >= 16) {
            $today = $now->format('Y-m-d');
            
            $count = Reservation::where('reservation_date', $today)
                ->where('status', 'active')
                ->update(['status' => 'cancelled']);
                
            $this->info("Reset {$count} reservations for {$today}");
            return self::SUCCESS;
        }
        
        $this->info('Too early - will run after 4 PM');
        return self::SUCCESS;
    }
}