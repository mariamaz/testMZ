@extends('layouts.app')

@section('content')
<div class="container">
    <div class="row">
        <div class="col-md-12">
                    @if (session('status'))
                        <div class="alert alert-success">
                            {{ session('status') }}
                        </div>
                    @endif

                   <div id="master"></div>
                   <script src="{{asset('js/app.js?kjhkjhjhjh')}}" ></script>
            </div>
        </div>
    </div>
</div>
@endsection
