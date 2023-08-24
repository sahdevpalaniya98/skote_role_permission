@extends('layouts.app')

@if (isset($page_title) && $page_title != '')
    @section('title', $page_title . ' | ' . config('app.name'))
@else
    @section('title', config('app.name'))
@endif
@section('content')
    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-body p-b-0" style="padding-bottom: 0px;">
                    <div class="row">
                        <div class="col-6">
                            <div class="media d-flex justify-content-start">
                                <div class="media-body">
                                    <div class="text-muted" style="max-width: 250px;">
                                        <h5>
                                            Total Amount:  <span class="{{ $employee_total_amount > 0 ? 'text-success' : 'text-danger' }}">${{number_format($employee_total_amount, 2)}}</span>
                                        </h5>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="col-6">
                            <div class="media d-flex justify-content-end">
                                <div class="me-4">
                                    <i class="mdi mdi-account-circle text-primary h1"></i>
                                </div>

                                <div class="media-body">
                                    <div class="text-muted" style="max-width: 250px;">
                                        <h5>{{ $employee->name }}</h5>
                                        <p class="mb-1"><i class="fa fa-envelope"></i> {{ $employee->email }}</p>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="card-body">
                    <div class="table-rep-plugin">
                        <div class="table-responsive mb-0" data-pattern="priority-columns">
                            <table id="dataTable" class="table table-striped">
                                <thead>
                                    <tr>
                                        <th>#</th>
                                        <th>Item Brand</th>
                                        <th>Item Name</th>
                                        <th>Purchase Amount</th>
                                        <th>Item Status</th>
                                        <th>Created At</th>
                                    </tr>
                                </thead>
                                <tbody></tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection
@section('scripts')
    @parent
    <script>
        $(document).ready(function() {
            var table;
            var url = '{!! $data_table_link !!}';

            var columns = [
                { data: "id", name: "id" },
                { data: 'brand_id', name: 'brand_id' },
                { data: 'phone_model.model_name', name: 'phone_model.model_name' },
                { data: 'purchase_price', name: 'purchase_price' },
                { data: 'is_sold', name: 'is_sold' },
                { data: 'created_at', name: 'created_at' },
            ];
            createDataTable(url, columns);
        });
    </script>
@endsection
