@inject('data', 'App\Lib\locationDistance')
@extends('layouts.app')
@push('css')
	<style>
		.user-pic {
			height: 58px !important;
			width: 58px !important;
		}
	</style>
@endpush
@section('content')

<div class="container">
    <div class="row justify-content-center">
        <div class="col-md-12">
            <div class="card">
                <div class="card-header">{{ __('User List') }}</div>

                <div class="card-body">
                    @if (session('status'))
                        <div class="alert alert-success" role="alert">
                            {{ session('status') }}
                        </div>
                    @endif
                    @if (session('match'))
                    <div class="alert alert-success alert-dismissible fade show">
                        <strong>{{ session('match') }}</strong>
                        <button type="button" class="close" data-dismiss="alert">&times;</button>
                    </div>
                    @endif
                    {{-- {{ __('You are logged in!') }} --}}
                    <div class="table-responsive">
                        <table class="table">
                            <caption>List of users</caption>
                            <thead>
                              <tr>
                                <th scope="col">Srl</th>
                                <th scope="col">Name</th>
                                <th scope="col">Image</th>
                                <th scope="col">Distance</th>
                                <th scope="col">Gender</th></th>
                                <th scope="col">Age</th>
                                <th scope="col">Action</th>
                              </tr>
                            </thead>
                            <tbody>
                                @foreach ($users as $user)
                                @if($data->distance(auth()->user()->latitude,auth()->user()->longitude,$user->latitude, $user->longitude,$unit)<=5)
                                <tr>
                                    <input type="hidden" value="{{$user->name}}" id="receiver_id">
                                    <th scope="row">{{ $loop->iteration  }}</th>
                                    <td>{{ $user->name }}</td>
                                    <td><img src="{{ asset('storage/users/'.$user->image) }} " class="user-pic" alt="{{ $user->name }}"></td>
                                    <td>{{ $data->distance(auth()->user()->latitude,auth()->user()->longitude,$user->latitude, $user->longitude,$unit) }}</td>
                                    <td>{{ $user->gender ==0 ? 'Male':'Female' }}</td>
                                    <td>{{ Carbon\Carbon::parse($user->dob)->age }}</td>
                                    
                                    <td><a href="{{ route('user-like',['id'=>$user->id]) }}" class="btn btn-success"><i class="fa fa-pencil-square-o" aria-hidden="true"></i> Like </a>  <a href="{{ route('user-dislike',['id'=>$user->id]) }}" class="btn btn-danger"><i class="fa fa-pencil-square-o" aria-hidden="true"></i> Dislike </a></td>
                                  </tr> 
                                  @endif
                                @endforeach
                            </tbody>
                        </table>
                      </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
@push('js')
 
@endpush
