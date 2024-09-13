@extends('layouts.app')

@section('content')
<div class="container">
    <div class="row justify-content-center">
        <div class="col-md-8">
            <div class="card">
                <div class="card-header">{{ __('Dashboard') }}</div>

                <div class="card-body">
                    @if (session('status'))
                    <div class="alert alert-success" role="alert">
                        {{ session('status') }}
                    </div>
                    @endif

                    {{ __('You are logged in!') }}
                </div>
            </div>
        </div>
    </div>
</div>
<hr>
<div class="container">
    <div class="row justigy-content-center">
        <div class="col-12"><h1>List Buku Terbaru</h1></div>
        @if($data->isEmpty())
            <div class="col-12">
                <h1>Tidak Ada Buku</h1>
            </div>
        @else
        @foreach($data as $x)
        <div class="col-12 col-md-6 col-lg-3">
            <div class="card">
                <img class="card-img-top" src="https://encrypted-tbn0.gstatic.com/images?q=tbn:ANd9GcQWz9tftw9qculFH1gxieWkxL6rbRk_hrXTSg&s" alt="Card image" style="width:100%">
                <div class="card-body">
                    <h4 class="card-title">{{ $x->title }}</h4>
                    <p class="card-text">Lorem ipsum dolor sit amet, consectetur adipisicing elit. Amet dicta facere in ipsa, nisi nemo voluptate earum fugit cumque? Reprehenderit aut odio eos provident cumque sapiente aliquid quia numquam vel?</p>
                    <a href="#" class="btn btn-primary">See Book</a>
                </div>
                <div class="card-footer">{{ $x->author->name }} | {{ $x->published_at }}</div>
            </div>
        </div>
        @endforeach
        @endif
    </div>
</div>
@endsection