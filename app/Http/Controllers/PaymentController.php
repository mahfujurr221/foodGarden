<?php

namespace App\Http\Controllers;

use App\ActualPayment;
use App\Brand;
use App\Customer;
use App\DueCollection;
use App\Payment;
use App\Pos;
use App\Services\PaymentService;
use App\Supplier;
use Illuminate\Http\Request;

class PaymentController extends Controller
{
    public function __construct()
    {
        $this->middleware('can:create-payment',  ['only' => ['create', 'store']]);
        // $this->middleware('can:edit-expense',  ['only' => ['edit', 'update']]);
        $this->middleware('can:delete-payment', ['only' => ['destroy']]);
        $this->middleware('can:list-payment', ['only' => ['index']]);
        // $this->middleware('can:show-customer', ['only' => ['show']]);

        $this->middleware('can:payment_receipt', ['only' => ['payment_receipt']]);
    }

    public function index(Request $request)
    {
        $payments       = ActualPayment::query();
        $customers      = Customer::all();

        if ($request->customer) {
            $payments = $payments->where('customer_id', $request->customer);
        }

        if ($request->start_date) {
            $payments = $payments->where('date', '>=', $request->start_date);
        }

        if ($request->end_date) {
            $payments = $payments->where('date', '<=', $request->end_date);
        }
        return view('pages.payments.index')->with(['customers' => $customers, 'payments' => $payments->where('customer_id', '!=', null)->where('payment_type', '=', 'receive')->latest()->paginate(20)]);
    }

    // supplierPaymentList
    public function supplierPayment(Request $request)
    {
        $payments       = ActualPayment::query();
        $suppliers      = Supplier::all();

        if ($request->supplier) {
            $payments = $payments->where('supplier_id', $request->supplier);
        }

        if ($request->start_date) {
            $payments = $payments->where('date', '>=', $request->start_date);
        }

        if ($request->end_date) {
            $payments = $payments->where('date', '<=', $request->end_date);
        }
        return view('pages.payments.supplier-payment')->with(['suppliers' => $suppliers, 'payments' => $payments->where('supplier_id', '!=', null)->latest()->paginate(20)]);
    }

    //customerDueList
    public function customerDueList(Request $request)
    {
        $payments       = DueCollection::query();
        $customers      = Customer::all();
        $brands = Brand::select('id', 'name')->get();

        if ($request->customer) {
            $payments = $payments->where('customer_id', $request->customer);
        }
        if ($request->brand) {
            $payments = $payments->where('brand_id', $request->brand);
        }

        if ($request->due_by) {
            $payments = $payments->where('due_by', $request->due_by);
        }

        if ($request->start_date) {
            $payments = $payments->where('committed_due_date', '>=', $request->start_date);
        }

        if ($request->end_date) {
            $payments = $payments->where('committed_due_date', '<=', $request->end_date);
        }
        $payments = $payments->where('due', '>', 0)->latest()->paginate(20);
        // $payments=$payments->where('payment_type','=','pay')->latest()->paginate(20);
        return view('pages.payments.due-list', compact('customers', 'payments', 'brands'));
    }

    //customerDueList
    public function customerDuePayment(Request $request)
    {
        $payments       = DueCollection::query();
        $customers      = Customer::all();
        $brands = Brand::select('id', 'name')->get();

        // if ($request->customer) {
        //     $payments = $payments->where('customer_id', $request->customer);
        // }
        // if ($request->brand) {
        //     $payments = $payments->where('brand_id', $request->brand);
        // }

        // if ($request->due_by) {
        //     $payments = $payments->where('due_by', $request->due_by);
        // }

        // if ($request->start_date) {
        //     $payments = $payments->where('committed_due_date', '>=', $request->start_date);
        // }

        // if ($request->end_date) {
        //     $payments = $payments->where('committed_due_date', '<=', $request->end_date);
        // }
        // $payments = $payments->latest()->paginate(20);
        $payments = $payments->where('due', '>', 0)->latest()->get();
        // $payments=$payments->where('payment_type','=','pay')->latest()->paginate(20);
        return view('pages.payments.due-collection', compact('customers', 'payments', 'brands'));
    }
    
    // TodayCustomerDuePayment
    public function todayCustomerDuePayment(Request $request)
    {
        $payments       = DueCollection::query();
        $customers      = Customer::all();
        $brands = Brand::select('id', 'name')->get();
        $payments = $payments->where('due', '>', 0)->where('committed_due_date', date('Y-m-d'))->latest()->get();
        return view('pages.payments.today-due-collection', compact('customers', 'payments', 'brands'));
    }

    public function create()
    {
        return view('pages.payments.create');
    }

    // public function supplier_create()
    // {
    //     return view('pages.payments.supplier_create');
    // }

    // public function customer_create()
    // {
    //     return view('pages.payments.customer_create');
    // }


    public function send_customer_sms($request, $customer, $requestAmount)
    {
        if ($request->sms != null) {
            // $customer = Customer::find($request->customer);

            $name = $customer->name;
            // $order_id = $pos->id;
            // $payable = $pos->payable;
            // $paid = $pos->paid();
            $due = $customer->sell_due();

            $sms_body = "Dear " . $name . ", Payment Tk." . $requestAmount . " has been successfully done. Current Due: Tk. " . $due . " Dated " . date("d/m/Y", strtotime($request->payment_date)) . " \n";

            $sms_body .= "Note : " . $request->note . "\n";

            $sms_body .= "--Oriental Metal and Engineering Works";

            // dd($sms_body);
            $mobile_number = "88" . $customer->phone;
            // $mobile_number = "8801741045212";
            // $mobile_number = "8801779724380";
            // SmsHelper::sendSms($mobile_number, $sms_body);
            // $mobile_number="8801741045212";

        }
    }


    public function send_supplier_sms($request, $supplier, $requestAmount)
    {
        if ($request->sms != null) {
            // $customer = Customer::find($request->customer);

            $name = $supplier->name;
            // $order_id = $pos->id;
            // $payable = $pos->payable;
            // $paid = $pos->paid();
            $due = $supplier->purchase_due();
            $sms_body = "Dear " . $name . ", Payment Tk." . $requestAmount . " has been successfully done. Current Due: Tk. " . $due . " Dated " . date("d/m/Y", strtotime($request->payment_date)) . " \n";

            $sms_body .= "Note : " . $request->note . "\n";
            $sms_body .= "--Oriental Metal and Engineering Works";
            // dd($sms_body);
            $mobile_number = "88" . $supplier->phone;
            // $mobile_number = "8801741045212";
            // $mobile_number = "8801779724380";
            // SmsHelper::sendSms($mobile_number, $sms_body);
            // $mobile_number="8801741045212";
        }
    }

    public function store(Request $request)
    {
        // dd($request->all());
        $this->validate($request, [
            'payment_date'     => 'required',
            'payment_type' => 'required',
            'account_type'     => 'required',
            'account_id'       => 'required',
            'amount'           => 'required',
            'committed_date'   => 'nullable',
            'due_by'           => 'required',
            // 'method'           => 'required',
            // 'transaction_id' => 'required|unique:payments',
        ]);

        // dd($request->all());
        if ($request->account_type == 'customer') {
            $actual_payment = PaymentService::add_customer_payment($request);
            if ($request->payment_type == 'pay') {
                $dueCollection = DueCollection::create([
                    'customer_id' => $request->account_id,
                    'due_by' => $request->due_by,
                    'direct_transection'=>1,
                    'payment_id' => $actual_payment->id??null,
                    'last_due_date' => $request->payment_date,
                    'committed_due_date' => $request->committed_date ?? $request->payment_date,
                    'amount'=>$request->amount,
                    'due' => $request->amount,
                    'brand_id' => $request->brand ?? null,
                ]);
            }
        }

        if ($request->account_type == 'supplier') {
            $actual_payment = PaymentService::add_supplier_payment($request);
        }

        //if request has from_customer
        if ($request->from_customer == 1) {
            // dd('from_customer');
            session()->flash('success', 'Payment Added Successfully');
            return back();
        } else {
            return redirect()->route('payment_receipt', $actual_payment->id);
        }
    }

    public function destroy(ActualPayment $payment)
    {
        // $actual_payment = ActualPayment::where('id', $payment->actual_payment_id)->first();
        // if ($actual_payment != null) {
        //     $actual_payment->amount = $actual_payment->amount - $payment->pay_amount;
        //     $actual_payment->save();
        //     if ($actual_payment->amount == 0) {
        //         $actual_payment->delete();
        //     }
        // }

        if ($payment->delete()) {
            session()->flash('success', 'Payment Delete Success');
        } else {
            session()->flash('warning', 'Deletion Failed!');
        }
        return back();
    }

    public function payment_receipt(ActualPayment $actual_payment)
    {
        // $payment       = ActualPayment::find($payment_id);
        // $payment_items = Payment::where('actual_payment_id', $payment->id)->get();
        // dd($actual_payment);
        // dd($actual_payment->payments);
        return view('pages.payments.receipt')->with(['payment' => $actual_payment, 'payment_items' => $actual_payment->payments]);
    }

    public function partial_delete(Payment $payment)
    {
        if ($payment->delete()) {
            session()->flash('success', 'Payment Delete Success');
        } else {
            session()->flash('warning', 'Deletion Failed!');
        }
        return back();
    }



    //dueCollectionPayment
    public function dueCollectionPayment(Request $request)
    {
        // dd($request->all());
        $this->validate($request, [
            'payment_date'     => 'required',
            'payment_type' => 'required',
            'account_type'     => 'required',
            'account_id'       => 'required',
            'amount'           => 'required',
            'committed_date'   => 'nullable',
            'due_by'           => 'required',
        ]);

        $dueCollection = DueCollection::find($request->due_collection_id);
        $customer=Customer::find($dueCollection->customer_id);

        $actual_payment = ActualPayment::create([
            'customer_id' => $dueCollection->customer_id,
            'collect_by'=> $request->due_by,
            'amount' => $request->amount,
            'payment_type' => 'receive',
            'date' => $request->payment_date,
            'note' => $request->note
        ]);

        // dd($dueCollection);
        if($dueCollection){
            if($dueCollection->pos_id!=null){
                $pos=Pos::find($dueCollection->pos_id);
                
                $pos->payments()->create([
                    'payment_date' => $request->payment_date,
                    'actual_payment_id' => $actual_payment->id,
                    'bank_account_id' => $request->bank_account_id,
                    'payment_type' => 'receive',
                    'pay_amount' => $request->amount,
                ]);
            }
            else{
                $customer->payments()->create([
                    'actual_payment_id' => $actual_payment->id,
                    'bank_account_id'   => $request->bank_account_id,
                    'payment_date' => $request->payment_date,
                    'payment_type' => $request->payment_type,
                    'pay_amount' => $request->amount,
                ]);
            }
        }

        $dueCollection->due = $dueCollection->due - $request->amount;
        $dueCollection->paid = $dueCollection->paid + $request->amount;
        $dueCollection->last_due_date = $request->payment_date;
        $dueCollection->committed_due_date = $request->committed_date ?? $request->payment_date;
        $dueCollection->payment_id = $actual_payment->id;
        $dueCollection->save();

        if($dueCollection->due==0){
            $dueCollection->status=1;
            $dueCollection->save();
        }

        if($actual_payment){
            session()->flash('success', 'Payment Added Successfully');
            return redirect()->route('payment_receipt', $actual_payment->id);
        }
        else{
            session()->flash('warning', 'Payment Failed');
            return back();
        }
    }
}
