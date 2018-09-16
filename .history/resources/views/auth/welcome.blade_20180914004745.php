@extends('layouts.app')

@section('content')
<div class="container">
    <div class="row justify-content-center">
        <div class="col-md-8">
            <div class="card">
                <div class="card-header">{{ __('Login') }}</div>

                <div class="card-body">
                    <p style="text-align: center; color: green;">You registered successfully.</p>
                    <p style="text-align: center;">An email sent to you.</p>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
