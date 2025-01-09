@if($item->customer_id)
<table>
    <tbody>
        <tr>
        <th>Shop Name:</th>
        @php
        $customer=\App\Customer::find($item->customer_id);
        @endphp
        <td>{{ $customer->shop_name }}</td>
    </tr>
    <tr>
        <th>Customer Name:</th>
        <td>{{ $customer->name }}</td>
    </tr>
    <tr>
        <th>Phone:</th>
        <td>{{ $customer->phone }}</td>
    </tr>
    <tr>
        <th>Address:</th>
        <td>{{ $customer->address->name }}</td>
    </tr>
    </tbody>
</table>
@elseif($item->supplier_id)
<table>
    <tbody>
    <tr>
        <th>Supplier Name:</th>
        @php
        $supplier=\App\Supplier::find($item->supplier_id);
        @endphp
        <td>{{ $supplier!=null?$supplier->name:"-" }}</td>
    </tr>
    <tr>
        <th>Phone:</th>
        <td>{{ $supplier!=null?$supplier->phone:"" }}</td>
    </tr>
    </tbody>
</table>
@else
<table>
    <tbody>
    <tr>
        <th>Customer Name:</th>
        <td>Walk-In Customer</td>
    </tr>
    {{-- <tr>
        <th>Phone:</th>
        <td>{{ $supplier!=null?$supplier->phone:"" }}</td>
    </tr> --}}
    </tbody>
</table>
@endif
