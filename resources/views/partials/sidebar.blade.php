<!-- Sidebar -->
<aside class="sidebar sidebar-expand-lg sidebar-dark">
    <header class="sidebar-header bg-dark">
        <a class="logo-icon" href="{{ route('admin') }}"><img src="{{ asset('dashboard/images/food-garden-logo.jpg') }}"
                alt="logo icon"></a>
        <span class="logo">
            <a href="{{ route('admin') }}">
                <img src="{{ asset('dashboard/images/food-garden-logo.jpg') }}" alt="logo">
            </a>
        </span>
        <span class="sidebar-toggle-fold"></span>
    </header>

    <nav class="sidebar-navigation">

        <ul class="menu">
            <li class="menu-item {{ Request::routeIs('admin') ? 'active' : '' }}">
                <a class="menu-link" href="{{ route('admin') }}">
                    <span class="icon fa fa-home"></span>
                    <span class="title">Dashboard</span>
                </a>
            </li>

            {{-- pos --}}
            @canany(['list-pos', 'create-pos'])
            <li class="menu-category">Sale, Order & Purchase</li>
            @can('create-pos')
            <li class="menu-item {{ Request::routeIs('pos.create') ? 'active' : '' }}">
                <a class="menu-link" href="{{ route('pos.create') }}">
                    <span class="icon fa fa-cart-plus"></span>
                    <span class="title">POS</span>
                </a>
            </li>
            @endcan
            @endcanany

            {{-- orders --}}
            @canany(['list-estimate', 'create-estimate'])
            <li class="menu-item {{ Request::routeIs('estimate.create')||Request::routeIs('estimate.index') ? 'active open' : '' }}">
                <a class="menu-link" href="#">
                    <img src="{{ asset('dashboard/sidebar_icons/purchase_cart.svg') }}" alt=""
                        class="sidebar_icon icon">
                    <span class="title">Order</span>
                    <span class="arrow"></span>
                </a>

                <ul class="menu-submenu">
                    @can('create-estimate')
                    <li class="menu-item {{ Request::routeIs('estimate.create') ? 'active' : '' }}">
                        <a class="menu-link" href="{{ route('estimate.create') }}">
                            <span class="dot"></span>
                            <span class="title">Add Order</span>
                        </a>
                    </li>
                    @endcan
                    @can('create-estimate')
                    <li class="menu-item {{ Request::routeIs('estimate.index') ? 'active' : '' }}">
                        <a class="menu-link" href="{{ route('estimate.index') }}">
                            <span class="dot"></span>
                            <span class="title">Manage Orders</span>
                        </a>
                    </li>
                    @endcan

                </ul>
            </li>
            @endcanany

            {{-- Todays Activity --}}
            @canany(['list-estimate', 'create-estimate','list-pos', 'create-pos'])
            <li class="menu-item {{ Request::routeIs('estimate.today_delivery')||Request::routeIs('pos.delivery_by') ? 'active open' : '' }}">
                <a class="menu-link" href="#">
                    <img src="{{ asset('dashboard/sidebar_icons/purchase_cart.svg') }}" alt=""
                        class="sidebar_icon icon">
                    <span class="title">Todays Activity</span>
                    <span class="arrow"></span>
                </a>

                <ul class="menu-submenu">
                    @can('estimate-todays-delivery')
                    <li class="menu-item {{ Request::routeIs('estimate.today_delivery') ? 'active' : '' }}">
                        <a class="menu-link" href="{{ route('estimate.today_delivery') }}">
                            <span class="dot"></span>
                            <span class="title">Today Deliveries</span>
                        </a>
                    </li>
                    @endcan

                    @can('list-pos')
                    <li class="menu-item {{ Request::routeIs('pos.delivery_by') ? 'active' : '' }}">
                        <a class="menu-link" href="{{ route('pos.delivery_by') }}">
                            <span class="dot"></span>
                            <span class="title">Delivery By</span>
                        </a>
                    </li>
                    @endcan

                    @can('list-payment')
                    <li class="menu-item {{ Request::routeIs('payment.today-due-payment') ? 'active' : '' }}">
                        <a class="menu-link" href="{{ route('payment.today-due-payment') }}">
                            <span class="dot"></span>
                            <span class="title">Today Due Collection</span>
                        </a>
                    </li>
                    @endcan

                </ul>
            </li>
            @endcanany

            {{-- sales --}}
            @canany(['list-pos', 'create-pos'])
            {{-- <li class="menu-item {{ Request::routeIs('pos.*') ? 'active open' : '' }}"> --}}
            <li
                class="menu-item {{ (Request::routeIs('pos.index') || Request::routeIs('report.profit_loss_report') || Request::routeIs('summary_report') || Request::routeIs('daily_report')) ? 'active open' : '' }}">

                <a class="menu-link" href="#">
                    <span class="icon fa fa-shopping-bag"></span>
                    <span class="title">Sales</span>
                    <span class="arrow"></span>
                </a>

                <ul class="menu-submenu">
                    @can('list-pos')
                    <li class="menu-item {{ Request::routeIs('pos.index') ? 'active' : '' }}">
                        <a class="menu-link" href="{{ route('pos.index') }}">
                            <span class="dot"></span>
                            <span class="title"> Sales</span>
                        </a>
                    </li>
                    @endcan

                    @can('daily_report')
                    <li class="menu-item {{ Request::routeIs('daily_report') ? 'active' : '' }}">
                        <a class="menu-link" href="{{ route('daily_report') }}">
                            <span class="dot"></span>
                            <span class="title">Daily Report</span>
                        </a>
                    </li>
                    @endcan

                    @can('profit_loss_report')
                    <li class="menu-item {{ Request::routeIs('report.profit_loss_report') ? 'active' : '' }}">
                        <a class="menu-link" href="{{ route('report.profit_loss_report') }}">
                            <span class="dot"></span>
                            <span class="title">Monthly Report</span>
                        </a>
                    </li>
                    @endcan

                    @can('summary_report')
                    <li class="menu-item {{ Request::routeIs('summary_report') ? 'active' : '' }}">
                        <a class="menu-link" href="{{ route('summary_report') }}">
                            <span class="dot"></span>
                            <span class="title"> Summary Report</span>
                        </a>
                    </li>
                    @endcan
                </ul>
            </li>
            @endcanany

            {{-- customer --}}
            @canany(['list-customer', 'create-customer', 'top_customer_report', 'customer_ledger',
            'customer_due_report'])
            <li
                class="menu-item {{ (Request::routeIs('customer.*') || Request::routeIs('report.top_customer') || Request::routeIs('report.customer_ledger') || 
                Request::routeIs('report.customer_due')) || Request::routeIs('payment.customer-due-payment') ? 'active open' : '' }}">
                <a class="menu-link" href="#">
                    <img src="{{ asset('dashboard/sidebar_icons/customers.svg') }}" alt="" class="sidebar_icon icon">
                    <span class="title">Customers</span>
                    <span class="arrow"></span>
                </a>

                <ul class="menu-submenu">
                    @can('create-customer')
                    <li class="menu-item {{ Request::routeIs('customer.create') ? 'active' : '' }}">
                        <a class="menu-link" href="{{ route('customer.create') }}">
                            <span class="dot"></span>
                            <span class="title">Add Customer</span>
                        </a>
                    </li>
                    @endcan

                    @can('list-customer')
                    <li class="menu-item {{ Request::routeIs('customer.index') ? 'active' : '' }}">
                        <a class="menu-link" href="{{ route('customer.index') }}">
                            <span class="dot"></span>
                            <span class="title">Manage Customers</span>
                        </a>
                    </li>
                    @endcan

                    @can('customer-info')
                    <li class="menu-item {{ Request::routeIs('customer.info') ? 'active' : '' }}">
                        <a class="menu-link" href="{{ route('customer.info') }}">
                            <span class="dot"></span>
                            <span class="title">Customers Info</span>
                        </a>
                    </li>
                    @endcan

                    @can('top_customer_report')
                    <li class="menu-item {{ Request::routeIs('report.top_customer') ? 'active' : '' }}">
                        <a class="menu-link" href="{{ route('report.top_customer') }}">
                            <span class="dot"></span>
                            <span class="title">Top Customer</span>
                        </a>
                    </li>
                    @endcan

                    @can('customer_ledger')
                    <li class="menu-item {{ Request::routeIs('report.customer_ledger') ? 'active' : '' }}">
                        <a class="menu-link" href="{{ route('report.customer_ledger') }}">
                            <span class="dot"></span>
                            <span class="title">Customer Ledger</span>
                        </a>
                    </li>
                    @endcan

                    @can('customer_due_report')
                    <li class="menu-item {{ Request::routeIs('report.customer_due') ? 'active' : '' }}">
                        <a class="menu-link" href="{{ route('report.customer_due') }}">
                            <span class="dot"></span>
                            <span class="title">Customer Due Report</span>
                        </a>
                    </li>
                    @endcan

                    @can('customer_due_report')
                    <li class="menu-item {{ Request::routeIs('payment.customer-due-payment') ? 'active' : '' }}">
                        <a class="menu-link" href="{{ route('payment.customer-due-payment') }}">
                            <span class="dot"></span>
                            <span class="title">Customer Due Payment</span>
                        </a>
                    </li>
                    @endcan

                    @can('list-customer')
                    <li class="menu-item {{ Request::routeIs('customer.view_address') ? 'active' : '' }}">
                        <a class="menu-link" href="{{ route('customer.view_address') }}">
                            <span class="dot"></span>
                            <span class="title">Manage Address</span>
                        </a>
                    </li>
                    @endcan
                    @can('list-customer')
                    <li class="menu-item {{ Request::routeIs('customer.view_business_category') ? 'active' : '' }}">
                        <a class="menu-link" href="{{ route('customer.view_business_category') }}">
                            <span class="dot"></span>
                            <span class="title">Manage Business Category</span>
                        </a>
                    </li>
                    @endcan

                </ul>
            </li>
            @endcanany

            {{-- payment --}}
            @canany(['list-payment', 'create-payment'])
            <li class="menu-item {{ Request::routeIs('payment.index') || Request::routeIs('payment.create') || Request::routeIs('payment.supplier-payment') || Request::routeIs('payment.customer-due-list') ? 'active open' : '' }}">
                <a class="menu-link" href="#">
                    <img src="{{ asset('dashboard/sidebar_icons/payments.svg') }}" alt="" class="sidebar_icon icon">
                    <span class="title">Due/Payments</span>
                    <span class="arrow"></span>
                </a>

                <ul class="menu-submenu">
                    @can('create-payment')
                    <li class="menu-item {{ Request::routeIs('payment.create') ? 'active' : '' }}">
                        <a class="menu-link" href="{{ route('payment.create') }}">
                            <span class="dot"></span>
                            <span class="title">Add Due/Payment</span>
                        </a>
                    </li>
                    @endcan

                    @can('list-payment')
                    <li class="menu-item {{ Request::routeIs('payment.index') ? 'active' : '' }}">
                        <a class="menu-link" href="{{ route('payment.index') }}">
                            <span class="dot"></span>
                            <span class="title">Customer Payments</span>
                        </a>
                    </li>
                    @endcan

                    @can('list-payment')
                    <li class="menu-item {{ Request::routeIs('payment.supplier-payment') ? 'active' : '' }}">
                        <a class="menu-link" href="{{ route('payment.supplier-payment') }}">
                            <span class="dot"></span>
                            <span class="title">Supplier Payments</span>
                        </a>
                    </li>
                    @endcan

                    @can('list-payment')
                    <li class="menu-item {{ Request::routeIs('payment.customer-due-list') ? 'active' : '' }}">
                        <a class="menu-link" href="{{ route('payment.customer-due-list') }}">
                            <span class="dot"></span>
                            <span class="title">Customer Due List</span>
                        </a>
                    </li>
                    @endcan

                </ul>
            </li>
            @endcanany

            {{-- damage --}}
            @canany(['list-damage', 'create-damage'])
            <li class="menu-item {{ Request::routeIs('damage.*') ? 'active open' : '' }}">
                <a class="menu-link" href="#">
                    <img src="{{ asset('dashboard/sidebar_icons/damage.svg') }}" alt="" class="sidebar_icon icon">
                    <span class="title">Damages</span>
                    <span class="arrow"></span>
                </a>
                <ul class="menu-submenu">
                    @can('create-damage')
                    <li class="menu-item {{ Request::routeIs('damage.create') ? 'active' : '' }}">
                        <a class="menu-link" href="{{ route('damage.create') }}">
                            <span class="dot"></span>
                            <span class="title">Add Damage</span>
                        </a>
                    </li>
                    @endcan
                    @can('list-damage')
                    <li class="menu-item {{ Request::routeIs('damage.index') ? 'active' : '' }}">
                        <a class="menu-link" href="{{ route('damage.index') }}">
                            <span class="dot"></span>
                            <span class="title">Inhouse Damages</span>
                        </a>
                    </li>
                    @endcan

                    @can('list-damage')
                    <li class="menu-item {{ Request::routeIs('damage.order') ? 'active' : '' }}">
                        <a class="menu-link" href="{{ route('damage.order') }}">
                            <span class="dot"></span>
                            <span class="title">Order Damage</span>
                        </a>
                    </li>
                    @endcan
                    @can('list-damage')
                    <li class="menu-item {{ Request::routeIs('damage.adjusted') ? 'active' : '' }}">
                        <a class="menu-link" href="{{ route('damage.adjusted') }}">
                            <span class="dot"></span>
                            <span class="title">Adjusted Damage</span>
                        </a>
                    </li>
                    @endcan
                </ul>
            </li>
            @endcanany

            {{-- return --}}
            @can('list-return')
            <li class="menu-item {{ Request::routeIs('return.*') ? 'active open' : '' }}">
                <a class="menu-link" href="#">
                    <img src="{{ asset('dashboard/sidebar_icons/return_box.svg') }}" alt="" class="sidebar_icon icon">
                    <span class="title">Return</span>
                    <span class="arrow"></span>
                </a>
                <ul class="menu-submenu">
                    @can('list-return')
                    <li class="menu-item {{ Request::routeIs('return.order') ? 'active' : '' }}">
                        <a class="menu-link" href="{{ route('return.order') }}">
                            <span class="dot"></span>
                            <span class="title">Order Returns</span>
                        </a>
                    </li>
                    @endcan
                </ul>
            </li>
            @endcan

            {{-- purchase --}}
            @canany(['list-purchase', 'create-purchase', 'purchase_report'])
            <li
                class="menu-item {{ (Request::routeIs('purchase.*') || Request::routeIs('report.purchase_report')) ? 'active open' : '' }}">
                <a class="menu-link" href="#">
                    <img src="{{ asset('dashboard/sidebar_icons/purchase_cart.svg') }}" alt=""
                        class="sidebar_icon icon">
                    <span class="title">Purchase</span>
                    <span class="arrow"></span>
                </a>

                <ul class="menu-submenu">
                    @can('create-purchase')
                    <li class="menu-item {{ Request::routeIs('purchase.create') ? 'active' : '' }}">
                        <a class="menu-link" href="{{ route('purchase.create') }}">
                            <span class="dot"></span>
                            <span class="title">Add Purchase</span>
                        </a>
                    </li>
                    @endcan
                    @can('list-purchase')
                    <li class="menu-item {{ Request::routeIs('purchase.index') ? 'active' : '' }}">
                        <a class="menu-link" href="{{ route('purchase.index') }}">
                            <span class="dot"></span>
                            <span class="title">Manage Purchases</span>
                        </a>
                    </li>
                    @endcan
                    @can('purchase_report')
                    <li class="menu-item {{ Request::routeIs('report.purchase_report') ? 'active' : '' }}">
                        <a class="menu-link" href="{{ route('report.purchase_report') }}">
                            <span class="dot"></span>
                            <span class="title">Purchase Report</span>
                        </a>
                    </li>
                    @endcan
                </ul>
            </li>
            @endcanany

            {{-- products --}}
            @canany(['list-product', 'create-product','top_product_report', 'top_product_all_time_report'])
            <li
                class="menu-item {{ (Request::routeIs('product.*') || Request::routeIs('report.top_product') || Request::routeIs('report.top_product_all_time')) ? 'active open' : '' }}">
                <a class="menu-link" href="#">
                    <img src="{{ asset('dashboard/sidebar_icons/product_dairy.svg') }}" alt=""
                        class="sidebar_icon icon">
                    <span class="title">Products</span>
                    <span class="arrow"></span>
                </a>

                <ul class="menu-submenu">
                    @can('create-product')
                    <li class="menu-item {{ Request::routeIs('product.create') ? 'active' : '' }}">
                        <a class="menu-link" href="{{ route('product.create') }}">
                            <span class="dot"></span>
                            <span class="title">Add Product</span>
                        </a>
                    </li>
                    @endcan

                    @can('list-product')
                    <li class="menu-item {{ Request::routeIs('product.index') ? 'active' : '' }}">
                        <a class="menu-link" href="{{ route('product.index') }}">
                            <span class="dot"></span>
                            <span class="title">Manage Products</span>
                        </a>
                    </li>
                    @endcan

                    @can('top_product_report')
                    <li class="menu-item {{ Request::routeIs('report.top_product') ? 'active' : '' }}">
                        <a class="menu-link" href="{{ route('report.top_product') }}">
                            <span class="dot"></span>
                            <span class="title">Top Product</span>
                        </a>
                    </li>
                    @endcan

                    @can('top_product_all_time_report')
                    <li class="menu-item {{ Request::routeIs('report.top_product_all_time') ? 'active' : '' }}">
                        <a class="menu-link" href="{{ route('report.top_product_all_time') }}">
                            <span class="dot"></span>
                            <span class="title">Top Product - All Time</span>
                        </a>
                    </li>
                    @endcan

                </ul>
            </li>
            @endcanany

            {{-- expense --}}
            @canany(['list-expense', 'create-expense', 'list-expense_category'])
            <li
                class="menu-item {{ (Request::routeIs('expense.*') || Request::routeIs('expense-category.index')) ? 'active open' : '' }}">
                <a class="menu-link" href="#">
                    <img src="{{ asset('dashboard/sidebar_icons/expense.svg') }}" alt="" class="sidebar_icon icon">
                    <span class="title">Expenses</span>
                    <span class="arrow"></span>
                </a>

                <ul class="menu-submenu">
                    @can('create-expense')
                    <li class="menu-item {{ Request::routeIs('expense.create') ? 'active' : '' }}">
                        <a class="menu-link" href="{{ route('expense.create') }}">
                            <span class="dot"></span>
                            <span class="title">Add Expense</span>
                        </a>
                    </li>
                    @endcan

                    @can('list-expense')
                    <li class="menu-item {{ Request::routeIs('expense.index') ? 'active' : '' }}">
                        <a class="menu-link" href="{{ route('expense.index') }}">
                            <span class="dot"></span>
                            <span class="title">Manage Expenses</span>
                        </a>
                    </li>
                    @endcan

                    @can('list-expense_category')
                    <li class="menu-item {{ Request::routeIs('expense-category.index') ? 'active' : '' }}">
                        <a class="menu-link" href="{{ route('expense-category.index') }}">
                            <span class="dot"></span>
                            <span class="title">Expense Category</span>
                        </a>
                    </li>
                    @endcan
                </ul>
            </li>
            @endcanany

            {{-- Stock --}}
            @canany(['stock', 'low_stock_report'])
            <li
                class="menu-item {{ (Request::routeIs('stock.*') || Request::routeIs('report.low_stock')) ? 'active open' : '' }}">
                <a class="menu-link" href="#">
                    <img src="{{ asset('dashboard/sidebar_icons/stock_box_tick.svg') }}" alt=""
                        class="sidebar_icon icon">
                    <span class="title">Stock</span>
                    <span class="arrow"></span>
                </a>

                <ul class="menu-submenu">
                    @can('stock')
                    <li
                        class="menu-item {{ (Request::routeIs('stock.index') || Request::routeIs('stock.create')) ? 'active' : '' }}">
                        <a class="menu-link" href="{{ route('stock.index') }}">
                            <span class="dot"></span>
                            <span class="title">Stock</span>
                        </a>
                    </li>
                    @endcan

                    @can('low_stock_report')
                    <li class="menu-item {{ Request::routeIs('report.low_stock') ? 'active' : '' }}">
                        <a class="menu-link" href="{{ route('report.low_stock') }}">
                            <span class="dot"></span>
                            <span class="title">Low Stock Report</span>
                        </a>
                    </li>
                    @endcan
                </ul>
            </li>
            @endcanany

            {{-- supplier --}}
            @canany(['list-brand', 'create-brand', 'supplier_ledger', 'supplier_due_report'])
            <li
                class="menu-item {{ (Request::routeIs('supplier.*') || Request::routeIs('report.supplier_ledger') || Request::routeIs('report.supplier_due')) ? 'active open' : '' }}">
                <a class="menu-link" href="#">
                    {{-- <span class="icon fa fa-wheelchair-alt"></span> --}}
                    <img src="{{ asset('dashboard/sidebar_icons/supplier_product.svg') }}" alt=""
                        class="sidebar_icon icon">
                    <span class="title">Brands</span>
                    <span class="arrow"></span>
                </a>

                <ul class="menu-submenu">
                    @can('create-brand')
                    <li class="menu-item {{ Request::routeIs('supplier.create') ? 'active' : '' }}">
                        <a class="menu-link" href="{{ route('supplier.create') }}">
                            <span class="dot"></span>
                            <span class="title">Add Brand</span>
                        </a>
                    </li>
                    @endcan

                    @can('list-brand')
                    <li class="menu-item {{ Request::routeIs('supplier.index') ? 'active' : '' }}">
                        <a class="menu-link" href="{{ route('supplier.index') }}">
                            <span class="dot"></span>
                            <span class="title">Manage Brands</span>
                        </a>
                    </li>
                    @endcan

                    @can('brand_ledger')
                    <li class="menu-item {{ Request::routeIs('report.supplier_ledger') ? 'active' : '' }}">
                        <a class="menu-link" href="{{ route('report.supplier_ledger') }}">
                            <span class="dot"></span>
                            <span class="title">Brand Ledger</span>
                        </a>
                    </li>
                    @endcan

                    @can('brand_due_report')
                    <li class="menu-item {{ Request::routeIs('report.supplier_due') ? 'active' : '' }}">
                        <a class="menu-link" href="{{ route('report.supplier_due') }}">
                            <span class="dot"></span>
                            <span class="title">Brand Due Report</span>
                        </a>
                    </li>
                    @endcan
                </ul>
            </li>
            @endcanany

            {{-- sr --}}
            @canany(['list-sr', 'create-sr'])
            <li class="menu-item {{ Request::routeIs('sr.*') ? 'active open' : '' }}">
                <a class="menu-link" href="#">
                    <span class="icon fa fa-users"></span>
                    <span class="title">SR Manage</span>
                    <span class="arrow"></span>
                </a>

                <ul class="menu-submenu">
                    @can('create-sr')
                    <li class="menu-item {{ Request::routeIs('sr.create') ? 'active' : '' }}">
                        <a class="menu-link" href="{{ route('sr.create') }}">
                            <span class="dot"></span>
                            <span class="title">Add SR</span>
                        </a>
                    </li>
                    @endcan

                    @can('list-sr')
                    <li class="menu-item {{ Request::routeIs('sr.index') ? 'active' : '' }}">
                        <a class="menu-link" href="{{ route('sr.index') }}">
                            <span class="dot"></span>
                            <span class="title">Manage SR</span>
                        </a>
                    </li>
                    @endcan
                </ul>
            </li>
            @endcanany

            {{-- units --}}
            @canany(['list-unit', 'create-unit'])
            <li class="menu-item {{ Request::routeIs('unit.*') ? 'active open' : '' }}">
                <a class="menu-link" href="#">
                    <img src="{{ asset('dashboard/sidebar_icons/unit_kg.svg') }}" alt="" class="sidebar_icon icon">
                    <span class="title">Units</span>
                    <span class="arrow"></span>
                </a>

                <ul class="menu-submenu">
                    @can('create-unit')
                    <li class="menu-item {{ Request::routeIs('unit.create') ? 'active' : '' }}">
                        <a class="menu-link" href="{{ route('unit.create') }}">
                            <span class="dot"></span>
                            <span class="title">Add Unit</span>
                        </a>
                    </li>
                    @endcan

                    @can('list-unit')
                    <li class="menu-item {{ Request::routeIs('unit.index') ? 'active' : '' }}">
                        <a class="menu-link" href="{{ route('unit.index') }}">
                            <span class="dot"></span>
                            <span class="title">Manage Units</span>
                        </a>
                    </li>
                    @endcan
                </ul>
            </li>
            @endcanany

            {{-- categories --}}
            @canany(['list-category', 'create-category'])
            <li class="menu-item {{ Request::routeIs('category.*') ? 'active open' : '' }}">
                <a class="menu-link" href="#">
                    <img src="{{ asset('dashboard/sidebar_icons/categories_4_box.svg') }}" alt=""
                        class="sidebar_icon icon">
                    <span class="title">Categories</span>
                    <span class="arrow"></span>
                </a>

                <ul class="menu-submenu">
                    @can('create-category')
                    <li class="menu-item {{ Request::routeIs('category.create') ? 'active' : '' }}">
                        <a class="menu-link" href="{{ route('category.create') }}">
                            <span class="dot"></span>
                            <span class="title">Add Category</span>
                        </a>
                    </li>
                    @endcan

                    @can('list-category')
                    <li class="menu-item {{ Request::routeIs('category.index') ? 'active' : '' }}">
                        <a class="menu-link" href="{{ route('category.index') }}">
                            <span class="dot"></span>
                            <span class="title">Manage Categories</span>
                        </a>
                    </li>
                    @endcan

                </ul>
            </li>
            @endcanany

            {{-- brands --}}
            {{-- @canany(['list-brand', 'create-brand'])
            <li class="menu-item {{ Request::routeIs('brand.*') ? 'active open' : '' }}">
                <a class="menu-link" href="#">
                    <img src="{{ asset('dashboard/sidebar_icons/brand.svg') }}" alt="" class="sidebar_icon icon">
                    <span class="title">Brands</span>
                    <span class="arrow"></span>
                </a>

                <ul class="menu-submenu">
                    @can('create-brand')
                    <li class="menu-item {{ Request::routeIs('brand.create') ? 'active' : '' }}">
                        <a class="menu-link" href="{{ route('brand.create') }}">
                            <span class="dot"></span>
                            <span class="title">Add Brand</span>
                        </a>
                    </li>
                    @endcan

                    @can('list-brand')
                    <li class="menu-item {{ Request::routeIs('brand.index') ? 'active' : '' }}">
                        <a class="menu-link" href="{{ route('brand.index') }}">
                            <span class="dot"></span>
                            <span class="title">Manage Brands</span>
                        </a>
                    </li>
                    @endcan

                </ul>
            </li>
            @endcanany --}}

            {{-- sms --}}
            @can('promotional_sms')
            {{-- <li class="menu-category">Promotional SMS</li> --}}
            <li class="menu-item {{ Request::routeIs('promotion.sms') ? 'active' : '' }}">
                <a class="menu-link" href="{{ route('promotion.sms') }}">
                    <span class="icon fa fa-envelope"></span>
                    <span class="title"> Promotional SMS </span>
                </a>
            </li>
            @endcan

            {{-- @can('list-owner')
            <li class="menu-item {{ Request::routeIs('owners.index') ? 'active' : '' }}">
                <a class="menu-link" href="{{ route('owners.index') }}">
                    <img src="{{ asset('dashboard/sidebar_icons/suited_man.svg') }}" alt="" class="sidebar_icon icon">
                    <span class="title">Owners</span>
                </a>
            </li>
            @endcan --}}

            @can('list-bank_account')
            <li class="menu-item {{ Request::routeIs('bank_account.index') ? 'active' : '' }}">
                <a class="menu-link" href="{{ route('bank_account.index') }}">
                    <img src="{{ asset('dashboard/sidebar_icons/bank_card.svg') }}" alt="" class="sidebar_icon icon">
                    <span class="title">Bank Accounts</span>
                </a>
            </li>
            @endcan

            {{-- <li class="menu-item {{ Request::routeIs('payment.*') ? 'active' : '' }}">
                <a class="menu-link" href="{{ route('payment.index') }}">
                    <span class="icon fa fa-money"></span>
                    <span class="title"> Payment</span>
                </a>
            </li> --}}



            {{-- @canany(['today_report', 'current_month_report', 'summary_report', 'daily_report',
            'customer_due_report',
            'supplier_due_report', 'low_stock_report', 'top_customer_report', 'top_product_report',
            'top_product_all_time_report', 'purchase_report', 'customer_ledger', 'supplier_ledger',
            'profit_loss_report'])
            <li class="menu-category">Reports</li>


            @can('today_report')
            <li class="menu-item {{ Request::routeIs('today_report') ? 'active' : '' }}">
                <a class="menu-link" href="{{ route('today_report') }}">
                    <img src="{{ asset('dashboard/sidebar_icons/24hr.svg') }}" alt="" class="sidebar_icon icon">
                    <span class="title"> Today Report</span>
                </a>
            </li>
            @endcan

            @can('current_month_report')
            <li class="menu-item {{ Request::routeIs('current_month_report') ? 'active' : '' }}">
                <a class="menu-link" href="{{ route('current_month_report') }}">
                    <img src="{{ asset('dashboard/sidebar_icons/calendar30.svg') }}" alt="" class="sidebar_icon icon">
                    <span class="title"> Current Month Report</span>
                </a>
            </li>
            @endcan


            @endcanany --}}

            @canany(['setting', 'backup', 'list-role', 'list-user'])
            <li class="menu-category">Setting & Customize</li>

            @can('setting')
            <li class="menu-item {{ Request::routeIs('pos.pos_setting') ? 'active' : '' }}">
                <a class="menu-link" href="{{ route('pos.pos_setting') }}">
                    <span class="icon fa fa-wrench"></span>
                    <span class="title"> Settings</span>
                </a>
            </li>
            @endcan

            @can('list-role')
            <li class="menu-item {{ Request::routeIs('roles.index') ? 'active' : '' }}">
                <a class="menu-link" href="{{ route('roles.index') }}">
                    <img src="{{ asset('dashboard/sidebar_icons/user_role.svg') }}" alt="" class="sidebar_icon icon">
                    <span class="title"> Roles & Permissions</span>
                </a>
            </li>
            @endcan

            @can('list-user')
            <li class="menu-item {{ Request::routeIs('users.index') ? 'active' : '' }}">
                <a class="menu-link" href="{{ route('users.index') }}">
                    <img src="{{ asset('dashboard/sidebar_icons/users.svg') }}" alt="" class="sidebar_icon icon">
                    <span class="title"> Users</span>
                </a>
            </li>
            @endcan


            @can('backup')
            <li class="menu-item {{ Request::routeIs('backup') ? 'active' : '' }}">
                <a class="menu-link" href="{{ route('backup') }}">
                    <img src="{{ asset('dashboard/sidebar_icons/box_download.svg') }}" alt="" class="sidebar_icon icon">
                    <span class="title"> Backup</span>
                </a>
            </li>
            @endcan
            @endcanany
        </ul>
    </nav>

</aside>
<!-- END Sidebar -->