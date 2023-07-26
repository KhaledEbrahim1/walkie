@extends('dashboard.layout.master')

@section('content')

@if ($message = Session::get('success'))
<div class="alert alert-success alert-block">
    <strong>{{ $message }}</strong>
</div>
@endif

<form action="{{ route('admin.product.stroe') }}" method="post"  enctype="multipart/form-data">
    @csrf
<div class="mb-3">
  <label for="" class="form-label">productName</label>

  <input type="text" name="product_title" id="" class="form-control" >




  <select class="form-select" aria-label="Default select example" name="Businesses_id">

    <option selected>Business name</option>
     @foreach ($Businesses as $Business )
    <option value={{ $Business->id }}>{{ $Business->name }}</option>
@endforeach
  </select>

  <label for="" class="form-label">Image</label>

  <input type="file" name="product_image" id="" class="form-control">

</div>
<button type="submit" class="btn btn-primary">Add</button>
</form>
@endsection
