<?php

namespace App\Http\Controllers;

use App\Expense;
use App\Payment;
use App\Pos;
use App\PosItem;
use App\Product;
use App\Purchase;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\DB;

class HomeController extends Controller
{
    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct()
    {
        $this->middleware(['auth']);

        $this->middleware('can:backup', ['only' => ['backup']]);

    }

    /**
     * Show the application dashboard.
     *
     * @return \Illuminate\Contracts\Support\Renderable
     */
    public function index()
    {
        ini_set('max_execution_time', '0');

        $payments = new Payment();
        // $paymentsTen = $payments->take(10)->where('payment_type', 'receive')->get();
        $todaysReceive = $payments->whereDate('payment_date', \Carbon\Carbon::today())->where('payment_type', 'receive')->sum('pay_amount');
        $monthlyReceived = DB::table('payments')->whereBetween('payment_date', [date('Y-m-1'), date('Y-m-t')])->where('payment_type', 'receive')->sum('pay_amount');
        $totalReceived = DB::table('payments')->where('payment_type', 'receive')->sum('pay_amount');

        return view('dashboard', compact('todaysReceive', 'monthlyReceived', 'totalReceived'))
            // ->withPayments($paymentsTen)
            // ->withPosItems(new PosItem())
            // ->withProducts(new Product())
            // ->withSale(new Pos())
            ->withExpense(new Expense());
        // ->withPurchase(new Purchase());
    }

    public function front_home()
    {
        return redirect()->route('login');
    }

    public function backup()
    {
        if (config('pos.app_mode') == 'demo') {
            session()->flash('error', 'This Feature is not available in Demo');
            return back();
        }


        $files = Storage::files(config('app.name'));
        foreach ($files as $file) {
            Storage::delete($file);
        }

        Artisan::call('backup:run', ['--only-db' => true]);
        $files = Storage::files(config('app.name'));

        if ($files != null) {
            return Storage::download($files[0]);
        } else {
            session()->flash('warning', 'Database not backed up.');
            return back();
        }
    }
}