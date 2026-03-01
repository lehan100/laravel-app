
@if (count($listSearchTerm) > 0)
    <table id="datatable-SearchTerm" class="table table-striped table-bordered dt-responsive nowrap" cellspacing="0"
        width="100%">
        <thead>
            <tr>
                <th>Từ khóa</th>
                <th>Kết quả tìm thấy</th>
                <th>Số lần tìm kiếm</th>
            </tr>
        </thead>
        <tbody>
            @foreach ($listSearchTerm as $search)
                @php
                    $query_text = $search->query_text;
                    $num_results = $search->num_results;
                    $popularity = $search->popularity;
                @endphp
                <tr>
                    <td>{{ $query_text }}</td>
                    <td>{{ $num_results }}</td>
                    <td>{{ $popularity }}</td>
                </tr>
            @endforeach
        </tbody>
    </table>
@else
    <p class="alert alert-danger">Không tìm thấy dữ liệu</p>
@endif
