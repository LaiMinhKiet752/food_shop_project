@extends('admin.admin_dashboard')
@section('admin')
@section('title')
    Product
@endsection
<div class="page-content">
    <!--breadcrumb-->
    <div class="page-breadcrumb d-none d-sm-flex align-items-center mb-3">
        <div class="breadcrumb-title pe-3">Product</div>
        <div class="ps-3">
            <nav aria-label="breadcrumb">
                <ol class="breadcrumb mb-0 p-0">
                    <li class="breadcrumb-item"><a href="javascript:;"><i class="bx bx-home-alt"></i></a>
                    </li>
                    <li class="breadcrumb-item active" aria-current="page">Add Product From Returned Orders</li>
                </ol>
            </nav>
        </div>
        <div class="ms-auto">

        </div>
    </div>
    <!--end breadcrumb-->
    <hr />
    <div class="card">
        <div class="card-body">
            <div class="table-responsive">
                <table id="example" class="table table-striped table-bordered" style="width:100%">
                    <thead>
                        <tr>
                            <th>No.</th>
                            <th>Invoice Number</th>
                            <th>Order Date</th>
                            <th>Return Date</th>
                            <th>Action</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach ($invoice as $key => $item)
                            <tr>
                                <td>{{ $key + 1 }}</td>
                                @php
                                    $order_date = strtotime($item->order->order_date);
                                    $order_date_format = date('d-m-Y H:i:s', $order_date);
                                @endphp
                                <td>
                                    {{ $item->order->invoice_number }}
                                </td>
                                <td>
                                    {{ $order_date_format }}
                                </td>
                                <td>
                                    {{ $item->order->return_date }}
                                </td>
                                <td>
                                    <a href="{{ route('view.product.from.returned.order', $item->order->id) }}"
                                        class="btn btn-info" title="Details"><i class="fa fa-eye"></i>
                                    </a>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                    <tfoot>
                        <tr>
                            <th>No.</th>
                            <th>Invoice Number</th>
                            <th>Order Date</th>
                            <th>Return Date</th>
                            <th>Action</th>
                        </tr>
                    </tfoot>
                </table>
            </div>
        </div>
    </div>
</div>
@endsection
