@extends('admin.layouts.default')
@php
    use App\Helpers\Product\Price;
    $orderStatus = config('configs.order_status');
    $shippingStatus = config('configs.shipping_status');
    
@endphp
@section('title', $metaTitle)
@section('content')

    <div class="row">
        <div class="col-12 col-md-4">
            @php               
                $totalOrderSuccess = 0;
                if (isset($reportTotalByStatus) && count($reportTotalByStatus) > 0) {
                    $totalSuccess = $reportTotalByStatus->filter(function ($d) {
                        return $d['order_status'] == 'success';
                    });
                    
                    if (count($totalSuccess) > 0) {
                    	$totalSuccess = array_values($totalSuccess->toArray());
                        $totalOrderSuccess = $totalSuccess[0]['total'];
                    }
                }
                
            @endphp
            <div class="animated flipInY">
                <div class="tile-stats bg-danger">
                    <div class="icon text-white"><i class="fa fa-usd"></i>
                    </div>
                    <div class="count text-white">{!! Price::format_price($totalOrderSuccess) !!}</div>

                    <h3 class="text-white">Tổng doanh thu</h3>
                </div>
            </div>
            @include('admin.pages.dashboard.blocks.order_new')
            <div class="x_panel">
                <div class="x_title border-0">
                    <h2>Tỉ lệ doanh số</h2>
                </div>
                <div class="x_content">

                    <div id="echart_total" style="height:350px;"></div>
                </div>
            </div>
            <div class="x_panel">
                <div class="x_title border-0">
                    <h2>Tỉ lệ đơn hàng</h2>
                </div>
                <div class="x_content">
                    <div id="echart_count" style="height:350px;"></div>
                </div>
            </div>
        </div>
        <div class="col-12 col-md-8">
            <div class="x_panel">
                <div class="x_content">
                    <ul class="nav nav-tabs bar_tabs" id="myTab" role="tablist">
                        <li class="nav-item">
                            <a class="nav-link active" id="seller-tab" data-toggle="tab" href="#seller" role="tab"
                                aria-controls="seller" aria-selected="true">Sản phẩm bán chạy</a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link" id="viewer-tab" data-toggle="tab" href="#viewer" role="tab"
                                aria-controls="viewer" aria-selected="false">Sản phẩm xem nhiều</a>
                        </li>
                    </ul>
                    <div class="tab-content" id="myTabContent">
                        <div class="tab-pane fade show active" id="seller" role="tabpanel" aria-labelledby="seller-tab">
                            @include('admin.pages.dashboard.blocks.seller')
                        </div>
                        <div class="tab-pane fade" id="viewer" role="tabpanel" aria-labelledby="viewer-tab">
                            @include('admin.pages.dashboard.blocks.viewer')
                        </div>
                    </div>
                </div>
            </div>
            <div class="x_panel">
                <div class="x_title border-0">
                    <h2>Truy vấn tìm kiếm</h2>
                </div>
                <div class="x_content">
                    @include('admin.pages.dashboard.blocks.search_term')
                </div>
            </div>
        </div>
    </div>
@endsection
@section('style')
    <!-- Datatables -->
    <link href="{{ asset('admin/vendors/datatables.net-bs/css/dataTables.bootstrap.min.css') }}" rel="stylesheet">
    <link href="{{ asset('admin/vendors/datatables.net-buttons-bs/css/buttons.bootstrap.min.css') }}" rel="stylesheet">
    <link href="{{ asset('admin/vendors/datatables.net-fixedheader-bs/css/fixedHeader.bootstrap.min.css') }}"
        rel="stylesheet">
    <link href="{{ asset('admin/vendors/datatables.net-responsive-bs/css/responsive.bootstrap.min.css') }}"
        rel="stylesheet">
    <link href="{{ asset('admin/vendors/datatables.net-scroller-bs/css/scroller.bootstrap.min.css') }}" rel="stylesheet">
    <style>
        .cursor-pointer {
            cursor: pointer;
        }

        .order-new table td,
        .order-new table th {
            border-color: #fff
        }
    </style>
@endsection
@section('script')
    <!-- ECharts -->
    <script src="{{ asset('admin/vendors/echarts/dist/echarts.min.js') }}"></script>
    <script src="{{ asset('admin/vendors/echarts/map/js/world.js') }}"></script>
    <!-- Datatables -->
    <script src="{{ asset('admin/vendors/datatables.net/js/jquery.dataTables.min.js') }}"></script>
    <script src="{{ asset('admin/vendors/datatables.net-bs/js/dataTables.bootstrap.min.js') }}"></script>
    <script src="{{ asset('admin/vendors/datatables.net-buttons/js/dataTables.buttons.min.js') }}"></script>
    <script src="{{ asset('admin/vendors/datatables.net-buttons-bs/js/buttons.bootstrap.min.js') }}"></script>
    <script src="{{ asset('admin/vendors/datatables.net-buttons/js/buttons.flash.min.js') }}"></script>
    <script src="{{ asset('admin/vendors/datatables.net-buttons/js/buttons.html5.min.js') }}"></script>
    <script src="{{ asset('admin/vendors/datatables.net-buttons/js/buttons.print.min.js') }}"></script>
    <script src="{{ asset('admin/vendors/datatables.net-fixedheader/js/dataTables.fixedHeader.min.js') }}"></script>
    <script src="{{ asset('admin/vendors/datatables.net-keytable/js/dataTables.keyTable.min.js') }}"></script>
    <script src="{{ asset('admin/vendors/datatables.net-responsive/js/dataTables.responsive.min.js') }}"></script>
    <script src="{{ asset('admin/vendors/datatables.net-responsive-bs/js/responsive.bootstrap.js') }}"></script>
    <script src="{{ asset('admin/vendors/datatables.net-scroller/js/dataTables.scroller.min.js') }}"></script>
    <script>
        $(document).ready(function() {
            var optionDataTable = {
                order: [
                    [3, 'desc']
                ],
                "columnDefs": [{
                    "targets": 'no-sort',
                    "orderable": false,
                }, ]
            };
            $('#datatable-viewer').DataTable(optionDataTable);
            $('#datatable-seller').DataTable(optionDataTable);
            $('#datatable-SearchTerm').DataTable({
                order: [
                    [2, 'desc']
                ]
            });
            if ("undefined" != typeof echarts) {
                var e = {
                    color: [
                        @foreach ($reportTotalByStatus as $status)
                            @php
                                $order_status_color = $orderStatus[$status->order_status]['color'];
                            @endphp
                                "{{ $order_status_color }}",
                        @endforeach
                    ]
                };
            };
            if ($("#echart_total").length) {
                echarts.init(document.getElementById("echart_total"), e).setOption({
                    tooltip: {
                        trigger: "item",
                        formatter: "{a} <br/>{b} : {c} ({d}%)"
                    },
                    legend: {
                        x: "center",
                        y: "bottom",
                        data: [
                            @foreach ($reportTotalByStatus as $status)
                                @php
                                    $order_status_name = $orderStatus[$status->order_status]['name'];
                                @endphp
                                    "{{ $order_status_name }}",
                            @endforeach
                        ]
                    },
                    toolbox: {
                        show: !0,
                        feature: {
                            magicType: {
                                show: !0,
                                type: ["pie", "funnel"],
                                option: {
                                    funnel: {
                                        x: "25%",
                                        width: "50%",
                                        funnelAlign: "left",
                                        max: 1548
                                    }
                                }
                            },
                            restore: {
                                show: !0,
                                title: "Restore"
                            },
                            saveAsImage: {
                                show: !0,
                                title: "Save Image"
                            }
                        }
                    },
                    calculable: !0,
                    series: [{
                        name: "Doanh số bán hàng",
                        type: "pie",
                        radius: "55%",
                        center: ["50%", "48%"],
                        data: [
                            @foreach ($reportTotalByStatus as $status)
                                @php
                                    $order_status_name = $orderStatus[$status->order_status]['name'];
                                    $total = $status->total;
                                @endphp {
                                    value: {{ $total }},
                                    name: "{{ $order_status_name }}"
                                },
                            @endforeach
                        ]
                    }]
                });
            }
            // Count
            if ("undefined" != typeof echarts) {
                var e = {
                    color: [
                        @foreach ($reportCountByStatus as $status)
                            @php
                                $order_status_color = $orderStatus[$status->order_status]['color'];
                            @endphp
                                "{{ $order_status_color }}",
                        @endforeach
                    ]
                };
            };
            if ($("#echart_count").length) {
                echarts.init(document.getElementById("echart_count"), e).setOption({
                    tooltip: {
                        trigger: "item",
                        formatter: "{a} <br/>{b} : {c} ({d}%)"
                    },
                    legend: {
                        x: "center",
                        y: "bottom",
                        data: [
                            @foreach ($reportCountByStatus as $status)
                                @php
                                    $order_status_name = $orderStatus[$status->order_status]['name'];
                                @endphp
                                    "{{ $order_status_name }}",
                            @endforeach
                        ]
                    },
                    toolbox: {
                        show: !0,
                        feature: {
                            magicType: {
                                show: !0,
                                type: ["pie", "funnel"],
                                option: {
                                    funnel: {
                                        x: "25%",
                                        width: "50%",
                                        funnelAlign: "left",
                                        max: 1548
                                    }
                                }
                            },
                            restore: {
                                show: !0,
                                title: "Restore"
                            },
                            saveAsImage: {
                                show: !0,
                                title: "Save Image"
                            }
                        }
                    },
                    calculable: !0,
                    series: [{
                        name: "Số lượng đơn hàng",
                        type: "pie",
                        radius: "55%",
                        center: ["50%", "48%"],
                        data: [
                            @foreach ($reportCountByStatus as $status)
                                @php
                                    $order_status_name = $orderStatus[$status->order_status]['name'];
                                    $number = $status->number;
                                @endphp {
                                    value: {{ $number }},
                                    name: "{{ $order_status_name }}"
                                },
                            @endforeach
                        ]
                    }]
                });

            }
            var a = {
                    normal: {
                        label: {
                            show: !1
                        },
                        labelLine: {
                            show: !1
                        }
                    }
                },
                t = {
                    normal: {
                        color: "rgba(0,0,0,0)",
                        label: {
                            show: !1
                        },
                        labelLine: {
                            show: !1
                        }
                    },
                    emphasis: {
                        color: "rgba(0,0,0,0)"
                    }
                }
        });
    </script>
@endsection
