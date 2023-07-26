@extends('dashboard.layout.master')

@section('content')
    <div class="container-fluid py-4">
        <div class="row">
            <div class="col-12">
                @foreach($reels as $reel)
                    <div class="card my-4">
                       <p>{{$reel->reel_title}}</p>
                        <video width="320" height="240" controls >
                            <source src={{$reel->reel_url }} >
                        </video>

                    </div>
                @endforeach

            </div>
        </div>
    </div>
@endsection
