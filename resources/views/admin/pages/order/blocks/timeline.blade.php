@php
    use Illuminate\Support\Carbon;
    $dataTimeLine = json_decode($timeline->comments, true);
@endphp
{{ html()->form('POST', route("$controllerName/post-timeline"))->attributes([
    'accept-charset' => 'UTF-8',
    'enctype' => 'multipart/form-data',
    'id' => 'appTimeline',
])->open() }}
@php
    $txtTimeLine = html()->textarea('comment', '')->attributes( ['class' => 'form-control rounded', 'rows' => 2, 'cols' => 54]);
    $inputHiddenID = html()->hidden('id', $id);
@endphp
<div class="col-12 col-md-4 d-print-none">
    <div class="form-group row">
        <label>Comment</label>
        {!! $inputHiddenID !!}
        {!! $txtTimeLine !!}
        <button type="submit" id="btn-comment" class="btn btn-success mt-3"><i class="fa fa-check mr-2"></i>Submit
            Comment</button>
    </div>
</div>
{{ html()->form()->close() }}
<ul id="list-timeline" class="d-print-none list-unstyled timeline my-4">
    @foreach ($dataTimeLine as $item)
        @php
            $comment = $item['comment'];
            $modify = $item['modify'];
            $createdAt = Carbon::parse($item['date']);
            $created_at = $createdAt->format('d/m/Y h:m:s');
        @endphp
        <li>
            <div class="block">
                <div class="block_content">
                    <h2 class="title">
                        <a>{{ $comment }}</a>
                    </h2>
                    <div class="byline">
                        <span>{{ $created_at }}</span> by <a>{{ $modify }}</a>
                    </div>
                </div>
            </div>
        </li>
    @endforeach
</ul>
@section('script')
    <script>
        const Timeline = {
            selector: "#list-timeline",
            domHTML: function(data) {
                let HTML = "";
                data.forEach(function(item) {
                    HTML += `
                    <li>
                        <div class="block">
                            <div class="block_content">
                                <h2 class="title">
                                    <a>${item.comment}</a>
                                </h2>
                                <div class="byline">
                                    <span>${item.date}</span> by <a>${item.modify}</a>
                                </div>
                            </div>
                        </div>
                    </li>
                    `;
                });
                $(this.selector).html(HTML);
            },
            init: function() {
                $("#appTimeline").on('submit', (function(e) {
                    e.preventDefault();
                    var link = $(this).attr("action");
                    $.ajax({
                        url: link,
                        type: "POST",
                        data: new FormData(this),
                        contentType: false,
                        cache: false,
                        processData: false,
                        dataType: "json",
                        beforeSend: function() {
                            $("#loading").show();
                        },
                        success: function(res) {
                            $("#loading").hide();
                            if (res.status == true) {
                                Timeline.domHTML(res.data);
                                $('#appTimeline')[0].reset();
                            }
                            if (res.status == false) {
                                $("#appTimeline .form-control").removeClass("is-invalid");
                                $("#appTimeline .invalid-feedback").remove();
                                if (res.error.comment) {
                                    $("#appTimeline .form-control").addClass("is-invalid")
                                        .after(
                                            `<div class="invalid-feedback">${res.error.comment}</div>`
                                        );
                                }
                            }
                        }
                    });
                }));
            }
        };
        const Order = {
            selector: {
                success: "#btn-shipping-success",
                cancel: "#btn-shipping-cancel",
                payment_success: "#btn-payment-success",
                form: "#appForm"
            },
            submit: function(link) {
                $(this.selector.form).attr("action", link);
                $(this.selector.form).submit();
            },
            init: function() {
                $(document).on("click", this.selector.success, function() {
                    var link = $(this).data("link");
                    Order.submit(link);
                });
                $(document).on("click", this.selector.cancel, function() {
                    var link = $(this).data("link");
                    Order.submit(link);
                });
                $(document).on("click", this.selector.payment_success, function() {
                    var link = $(this).data("link");
                    Order.submit(link);
                });
            }
        };
        $(document).ready(function() {
            Timeline.init();
            Order.init();
        })
    </script>
    <style>
        @media print {
            .col-print-12 {
                -ms-flex: 0 0 100%;
                flex: 0 0 100%;
                max-width: 100%;
            }

            .col-print-6 {
                -ms-flex: 0 0 50%;
                flex: 0 0 50%;
                max-width: 50%;
            }

            .right_col {
                min-height: 0 !important
            }
            .x_panel{border:none!important; padding: 0 15px;}
            table{margin-bottom: 0!important}
        }
    </style>
@endsection
