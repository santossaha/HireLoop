@extends('layouts.app')

@section('content')
<div class="container-fluid">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h1 class="h3 mb-0 text-gray-800">Add New Company</h1>
        <a href="{{ route('companies.index') }}" class="btn btn-secondary">
            <i class="fas fa-arrow-left"></i> Back
        </a>
    </div>

    <div class="card shadow mb-4">
        <div class="card-body">
{{--            @if ($errors->any())--}}
{{--            <div class="alert alert-danger">--}}
{{--                <strong>Whoops!</strong> There were some problems with your input.<br><br>--}}
{{--                <ul>--}}
{{--                    @foreach ($errors->all() as $error)--}}
{{--                    <li>{{ $error }}</li>--}}
{{--                    @endforeach--}}
{{--                </ul>--}}
{{--            </div>--}}
{{--            @endif--}}

            <form action="{{ route('companies.store') }}" method="POST">
                @csrf
                <div class="form-group">
                    <label for="name">Company Name <span class="text-danger">*</span></label>
                    <input type="text" class="form-control" id="name" name="name" value="{{ old('name') }}" required>
                </div>

                <div class="form-group mt-2">
                    <label for="detail">Detail</label>
                    <textarea class="form-control" id="detail" name="detail" rows="3">{{ old('detail') }}</textarea>
                </div>
                <br/>

                <button type="submit" class="btn btn-primary">Submit</button>
            </form>
        </div>
    </div>
</div>
@endsection 