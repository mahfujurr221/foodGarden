<?php
namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;


class PermissionSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        DB::statement('SET FOREIGN_KEY_CHECKS=0;');
        DB::table('permissions')->truncate();
        DB::table('model_has_permissions')->truncate();
        DB::table('role_has_permissions')->truncate();
        DB::statement('SET FOREIGN_KEY_CHECKS=1;');

        $permissions_map = ['l' => 'list', 'c' => 'create', 'r' => 'show', 'u' => 'edit', 'd' => 'delete'];

        $resourece_features['unit'] = ['l', 'c', 'd'];
        $resourece_features['owner'] = ['l', 'c', 'u', 'd'];
        // Bank Account
        $resourece_features['bank_account'] = ['l', 'c', 'r'];
        $other_permissions['bank_account-add_money'] = 'bank_account';
        $other_permissions['bank_account-withdraw_money'] = 'bank_account';
        $other_permissions['bank_account-transfer'] = 'bank_account';
        $other_permissions['bank_account-history'] = 'bank_account';


        $resourece_features['brand'] = ['l', 'c', 'u', 'd'];
        $resourece_features['category'] = ['l', 'c', 'u', 'd'];

        // Product
        $resourece_features['product'] = ['l', 'c', 'u', 'd'];
        $other_permissions['product-sell_history'] = 'product';
        $other_permissions['product-add_category'] = 'product';
        $other_permissions['product-add_brand'] = 'product';
        $other_permissions['product-barcode'] = 'product';
        // Pos
        $resourece_features['pos'] = ['l', 'c', 'r', 'u', 'd'];
        $other_permissions['pos-add_payment'] = 'pos';
        $other_permissions['pos-add_customer'] = 'pos';
        $other_permissions['pos_receipt'] = 'pos';
        $other_permissions['chalan_receipt'] = 'pos';
        $other_permissions['pos-profit'] = 'pos';
        $other_permissions['pos-purchase_cost'] = 'pos';
        $other_permissions['pos-purchase_cost_breakdown'] = 'pos';

        // sell return
        $resourece_features['return'] = ['l', 'c', 'd'];
        // Estimate
        $resourece_features['estimate'] = ['l', 'c', 'r', 'u', 'd'];
        $other_permissions['estimate-todays-delivery'] = 'estimate';

        // Purchase
        $resourece_features['purchase'] = ['l', 'c', 'r', 'u', 'd'];
        $other_permissions['purchase-add_payment'] = 'purchase';
        $other_permissions['purchase-add_supplier'] = 'purchase';
        $other_permissions['purchase-receipt'] = 'purchase';

        // Customer
        $resourece_features['customer'] = ['l', 'c', 'u', 'd'];
        $other_permissions['customer-wallet_payment'] = 'customer';
        $other_permissions['customer-report'] = 'customer';
        $other_permissions['customer-info'] = 'customer';

        // Supplier
        $resourece_features['supplier'] = ['l', 'c', 'u', 'd'];
        $other_permissions['supplier-wallet_payment'] = 'supplier';
        $other_permissions['supplier-report'] = 'supplier';
        // Sr
        $resourece_features['sr'] = ['l', 'c', 'r', 'u', 'd'];
        // Expense
        $resourece_features['expense_category'] = ['l', 'c', 'u', 'd'];
        $resourece_features['expense'] = ['l', 'c', 'u', 'd'];

        //stock
        $other_permissions['stock'] = 'stock';

        // Payments
        $resourece_features['payment'] = ['l', 'c', 'd'];
        $other_permissions['payment_receipt'] = 'payment';

        // Damage
        $resourece_features['damage'] = ['l', 'c', 'd','u'];

        // Promotional SMS
        $other_permissions['promotional_sms'] = 'promotional_sms';

        // Report
        $other_permissions['today_report'] = 'report';
        $other_permissions['current_month_report'] = 'report';
        $other_permissions['summary_report'] = 'report';
        $other_permissions['daily_report'] = 'report';
        $other_permissions['customer_due_report'] = 'report';
        $other_permissions['supplier_due_report'] = 'report';
        $other_permissions['low_stock_report'] = 'report';
        $other_permissions['top_customer_report'] = 'report';
        $other_permissions['top_product_report'] = 'report';
        $other_permissions['top_product_all_time_report'] = 'report';
        $other_permissions['purchase_report'] = 'report';
        $other_permissions['customer_ledger'] = 'report';
        $other_permissions['supplier_ledger'] = 'report';
        $other_permissions['profit_loss_report'] = 'report';


        // Setting
        $other_permissions['setting'] = 'misc';
        $other_permissions['backup'] = 'misc';
        // $other_permissions['roles']='roles';

        $resourece_features['role'] = ['l', 'c', 'u', 'd'];
        $resourece_features['user'] = ['l', 'c', 'u', 'd'];
        $other_permissions['permissions'] = 'role';



        $other_permissions['profile'] = 'profile';
        $other_permissions['change_password'] = 'profile';

        // Dashboard
        $other_permissions['dashboard'] = 'dashboard';
        //new
        $other_permissions['today_order'] = 'dashboard';
        $other_permissions['today_sold'] = 'dashboard';
        $other_permissions['today_damage'] = 'dashboard';
        $other_permissions['today_collection'] = 'dashboard';
        //month
        $other_permissions['this_month_order'] = 'dashboard';
        $other_permissions['this_month_sold'] = 'dashboard';
        $other_permissions['this_month_damage'] = 'dashboard';
        $other_permissions['this_month_collection'] = 'dashboard';

        $other_permissions['current_month_sold'] = 'dashboard';
        $other_permissions['current_month_purchased'] = 'dashboard';
        $other_permissions['current_month_expense'] = 'dashboard';
        $other_permissions['current_month_returned'] = 'dashboard';
        $other_permissions['current_month_profit'] = 'dashboard';

        //old
        $other_permissions['today_sold-purchase_cost'] = 'dashboard';
        $other_permissions['today_expense'] = 'dashboard';
        $other_permissions['today_profit'] = 'dashboard';
        $other_permissions['this_month_sell'] = 'dashboard';
        $other_permissions['this_month_purchase'] = 'dashboard';
        $other_permissions['this_month_expense'] = 'dashboard';
        $other_permissions['this_month_profit'] = 'dashboard';

        $other_permissions['total_sold'] = 'dashboard';
        $other_permissions['total_purchased'] = 'dashboard';
        $other_permissions['total_expense'] = 'dashboard';
        $other_permissions['total_returned'] = 'dashboard';
        $other_permissions['total_profit'] = 'dashboard';

        $other_permissions['total_receivable'] = 'dashboard';
        $other_permissions['total_payable'] = 'dashboard';

        $other_permissions['stock-purchase_value'] = 'dashboard';
        $other_permissions['stock-sell_value'] = 'dashboard';

        $other_permissions['total_customer'] = 'dashboard';
        $other_permissions['total_supplier'] = 'dashboard';
        $other_permissions['total_invoices'] = 'dashboard';
        $other_permissions['total_products'] = 'dashboard';


        foreach ($resourece_features as $key => $rf) {
            foreach ($rf as $feature) {
                $access = $permissions_map[$feature];
                Permission::create([
                    'name' => $access . "-" . $key,
                    'feature' => $key
                ]);
            }
        }


        foreach ($other_permissions as $permission => $value) {
            Permission::create([
                'name' => $permission,
                'feature' => $value
            ]);
        }

        $all_permissions = Permission::pluck('name');

        $admin = Role::where('name', 'admin')->first();
        $test_admin = Role::where('name', 'test_admin')->first();

        $admin->syncPermissions($all_permissions);
        $test_admin->syncPermissions($all_permissions);

        $operator = Role::where('name', 'operator')->first();

        $operator_permissions = [
            'pos-add_payment',
            'pos-add_customer',
            'list-pos',
            'create-pos',
            'show-pos',
            'edit-pos',
            'delete-pos',
            'pos_receipt',
            'chalan_receipt',
            'profile',
            'change_password'
        ];

        $operator->syncPermissions($operator_permissions);
    }
}