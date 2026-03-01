@extends('default.layouts.2-column-new')
@section('content')
    @if ($item)
        <div class="page-new-view px-3 py-4 mb-4">
            @php
                $name = $item->name;
            @endphp
            <h1 class="title mb-3">{{ $name }}</h1>
            <div class="main">
                @if ($newItem)
                    <div class="mt-4">
                        @php
                            $content = $newItem->contents->content;
                        @endphp
                        {!! $content !!}
                    </div>
                @endif
                <div class="my-5">
                    {{ html()->form('POST', route("contact/post-contact"))->attributes([
                        'accept-charset' => 'UTF-8',
                        'enctype' => 'multipart/form-data',
                        'id' => 'formContact',
                    ])->open() }}
                    @php
                        $inputName = html()->text('name', '')->attributes(['class' => 'form-control', 'placeholder' => 'Ví dụ: Nguyễn Văn A']);
                        $inputPhone = html()->text('phone', '')->attributes(['class' => 'form-control', 'placeholder' => 'Số điện thoại của quý khách']);
                        $inputEmail = html()->text('email', '')->attributes(['class' => 'form-control', 'placeholder' => 'Địa chỉ email của quý khách']);
                        $inputTitle = html()->text('title', '')->attributes(['class' => 'form-control', 'placeholder' => 'Yêu cầu của quý khách']);
                        $inputContent = html()->textarea('message', '')->attributes(['class' => 'form-control rounded', 'rows' => 4, 'cols' => 54, 'placeholder' => 'Chi tiết lời nhắn, câu hỏi của quý khách']);
                    @endphp
                    <div class="input-group mb-4 row">
                        <div class="col-12 col-md-3 control-label">
                            Họ Tên <span class="required">*</span>
                        </div>
                        <div class="col-12 col-md-6">
                            <div class="form-input">
                                {!! $inputName !!}
                            </div>
                        </div>
                    </div>
                    <div class="input-group mb-4 row">
                        <div class="col-12 col-md-3 control-label">
                            Số điện thoại <span class="required">*</span>
                        </div>
                        <div class="col-12 col-md-6">
                            <div class="form-input">
                                {!! $inputPhone !!}
                            </div>
                        </div>
                    </div>
                    <div class="input-group mb-4 row">
                        <div class="col-12 col-md-3 control-label">
                            Địa chỉ email <span class="required">*</span>
                        </div>
                        <div class="col-12 col-md-6">
                            <div class="form-input">
                                {!! $inputEmail !!}
                            </div>
                        </div>
                    </div>
                    <div class="input-group mb-4 row ">
                        <div class="col-12 col-md-3 control-label">
                            Tiêu đề <span class="required">*</span>
                        </div>
                        <div class="col-12 col-md-6">
                            <div class="form-input">
                                {!! $inputTitle !!}
                            </div>
                        </div>
                    </div>
                    <div class="input-group mb-4 row">
                        <div class="col-12 col-md-3 control-label">
                            Câu hỏi <span class="required">*</span>
                        </div>
                        <div class="col-12 col-md-6">
                            <div class="form-input">
                                {!! $inputContent !!}
                            </div>
                        </div>
                    </div>
                    <div class="input-group row">
                        <div class="col-12 col-md-3 text-end">
                            &nbsp;
                        </div>
                        <div class="col-12 col-md-6">
                            <button type="submit" disabled class="btn btn-custom hangle-button btn-checkout py-2">Gửi
                                yêu cầu</button>
                        </div>
                    </div>
                    {{ html()->form()->close() }}
                </div>
            </div>
        </div>
    @endif
@endsection
@section('sitebar')
    @include('default.pages.news.blocks.category_sitebar_detail')
@endsection
@section('styles')
    <link href="{{ asset('default/css/news.css') }}" rel="stylesheet" />
@endsection
@section('script')
    <script defer="defer" async="async" type="text/javascript" src="{{ asset('default/js/contact.js') }}"></script>
@endsection
