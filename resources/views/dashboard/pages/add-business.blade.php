@extends('dashboard.layout.master')

@section('content')

@if ($message = Session::get('success'))
<div class="alert alert-success alert-block">
    <strong>{{ $message }}</strong>
</div>
@endif

<form action="{{ route('admin.business.stroe') }}" method="post"  enctype="multipart/form-data">
    @csrf
<div class="mb-3">
  <label for="" class="form-label">BusinessName</label>

  <input type="text" name="name" id="" class="form-control" >

  <label for="" class="form-label">address</label>

  <input type="text" name="address" id="" class="form-control" >

  <label for="" class="form-label">location</label>

  <input type="text" name="location" id="" class="form-control" >

  <label for="" class="form-label">city</label>

  <input type="text" name="city" id="" class="form-control" >



  <label for="" class="form-label">Image</label>

  <input type="file" name="images" id="" class="form-control">

</div>
<button type="submit" class="btn btn-primary">Add</button>
</form>


@endsection
