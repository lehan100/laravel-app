@extends('admin.layouts.default')
@section('title', $metaTitle)
@section('content')
    <div class="x_panel p-0 border-0">
        <div class="x_content p-0 m-0">
            <div class="alert alert-success m-0">
                <h5 class="alert-heading text-uppercase"><strong><i class="fa fa-check mr-2"></i>Tạo sitemap thành
                        công!</strong></h5>
                <hr>
                <p class="mb-0"><a onclick="copyToClipboard(this,'{{ asset('storage/sitemap.xml') }}')" class="btn btn-success" href="javascript:;"><i class="mr-2 fa fa-clipboard"></i> Copy liên kết đã tạo</a></p>
            </div>
        </div>
    </div>
    <script>
        function copyToClipboard(elem,text) {
            var $temp = $("<input>");
            $("body").append($temp);
            $(elem).html("<i class='mx-2 fa fa-check'></i>Đã sao chép");
            $temp.val(text).select();
            document.execCommand("copy");
            $temp.remove();
        }
    </script>
@endsection
