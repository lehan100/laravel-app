@extends('admin.layouts.default')
@php
use App\Helpers\Template as Template;
use App\Helpers\Category as Category;
$configPath = config('image.path.category');
$imageSize = config("image.admin.category");
$inputHiddenId = html()->hidden('select_id',$id)->attributes(['id'=>'select_id']);
@endphp
@section('title', $metaTitle)
@section('content')
{!!$inputHiddenId!!}
<div class="page-title row">
    <div class="col">
        <a id="addRoot" href="javascript:;" class="btn btn-warning"><i class="fa fa-plus-circle mr-2"></i>Thêm Root</a>
        <a id="addChildren" href="javascript:;" class="btn btn-success"><i class="fa fa-folder-open mr-2"></i>Thêm mới</a>
    </div>
    <div class="title_right col-auto">
        <a href="javascript:onSubmitActonRollback('appForm')" class="btn btn-success "><i class="fa fa-save mr-2"></i>Lưu</a>
        <a id="btnDelete" href="javascript:;" class="btn btn-danger "><i class="fa fa-trash-o mr-2"></i>Xóa</a>
    </div>
</div>
<div class="row">
    <div id="menutree" class="col-12 col-md-3" style="display: none">
        {!!$navigation!!}
    </div>
    <div class="col">
        @include ('admin.templates.notify')
        <div id="ajaxContent">
            Loading....
        </div>
    </div>
</div>
@endsection
@section('style')
<link href="{{asset('admin/vendors/vakata-jstree/dist/themes/default/style.css')}}" rel="stylesheet"> 
@endsection
@section("script")
<script src="{{asset('admin/vendors/vakata-jstree/dist/jstree.min.js')}}"></script>
<script type="text/javascript">
 const Category = {
    call:function(url,parent_id = 0){
        url = url+'?_token={{csrf_token()}}&parent_id='+parent_id;
        $.ajax({
                url: url,
                type: "POST",
                contentType: false,
                dataType: "json",
                beforeSend: function () {
                    Loading.show();
                },
                success: function (f) {
                    Loading.hide();
                    $("#ajaxContent").html(f.html);
                    $('#menutree').show();
                    var elems = Array.prototype.slice.call(document.querySelectorAll('.js-switch'));
                    elems.forEach(function(html) {
                      var switchery = new Switchery(html);
                    });
                    Category.pushState(f.url);
                }
            });
    },
    sort:function(id, pos, parent,select_id = 0 ,url_get = null){
        url = '{{route("category/sort")}}';
        $.ajax({
                url: url,
                type: "POST",
                data:{'_token':'{{csrf_token()}}','id':id,'pos':pos,'parent_id':parent},
                dataType: "json",
                beforeSend: function () {
                    if(select_id > 0 && url != null){
                         Loading.show();
                    }
                },
                success: function (f) {
                   var parent_id =  $("#select_id").val();
                    if(f.id ==select_id){
                         Category.pushState(f.url);  
                    }
                     if(select_id > 0 && url != null){
                        Category.call(url_get,select_id);
                    } 
                }
            });
             
    },
    pushState:function(url){
        window.history.pushState({path: url}, '', url);
    }
 };
 $(document).ready(function() {
     @if($id >0)
        $('#menutree li#{{$id}}').attr("data-jstree",'{"opened" : true }');
        $('#menutree li#{{$id}}').parents("li").attr("data-jstree",'{"opened" : true }');
    @endif
    $('#menutree').on("changed.jstree", function (e, data) {
            if(data.selected.length) {
                var url = data.instance.get_node(data.selected[0]).a_attr.href;
                var parent_id = data.instance.get_node(data.selected[0]).a_attr.id;
                $("#select_id").val(parent_id);
                Category.call(url, parent_id);
            }
    }).jstree({
            "core" : {
            "animation" : 0,
            "check_callback" : true,
        },
        "plugins" : [
             "dnd", "types"
        ]
    });
    $(document).on('dnd_start.vakata', function (event, data) {
        sel = "li#"+ data.data.nodes[0] +".jstree-node";
        Parent = $('#menutree').jstree(true).get_node(data.data.nodes[0]).parent;
        Pos = $(sel).index();
    });

    $(document).on('dnd_stop.vakata', function (event, data) {
        node = data.data.origin.get_node(data.data.nodes[0]);
        if (node.type == "root")
        {
            return false;
        }

//        if (confirm("Voulez vous vraiment deplacer le fichier ou le dossier ?") === false)
//        {
//            $('#tree').jstree(true).move_node(node,Parent,Pos);
//            return false;
//        }
        sel = "li#"+ data.data.nodes[0] +".jstree-node";
       var select_id = $('#menutree').jstree(true).get_node(data.data.nodes[0]).id;
       var parent_id = $('#menutree').jstree(true).get_node(data.data.nodes[0]).a_attr.id;
       var url = $('#menutree').jstree(true).get_node(data.data.nodes[0]).a_attr.href;
       newPos =  $(sel).index();
       newParent = node.parent; 
       Category.sort(select_id, newPos,newParent,parent_id,url);
       
       var elm = $(sel).parent("ul");
       elm.find('li').each(function(){
            id = $(this).attr("id");
            newPos =  $(this).index();
            Category.sort(id, newPos,newParent);
        });
    });
    @if($id >0)
        $('#menutree a#{{$id}}').trigger("click");
    @else
        $('#menutree>ul>li>ul>li:first-child>a').trigger("click");
    @endif
    
    $("#addRoot").click(function(){
        var url = '{{route('category/formajax')}}' ;
        Category.call(url);
    });
    $("#addChildren").click(function(){
        var url = '{{route('category/formajax')}}' ;
        var parent_id =  $("#select_id").val();
        Category.call(url, parent_id);
    });
    $("#btnDelete").click(function(){
        var parent_id =  $("#select_id").val();
        var url = '{{route('category/delete')}}/'+ parent_id ;
       document.location.href= url;
    });
});
</script>
@endsection