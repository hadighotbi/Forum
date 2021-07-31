@extends('layouts.app')

@section('content')
    <div class="container">
        <div class="row justify-content-center">
            <div class="col-md-8">
                <div class="card">
                    <div class="card-header">Create a new Thread</div>
                    <div class="card-body">
                        <form action="/threads" method="POST">
                            {{csrf_field()}}
                            <select class="form-control" id="channel_id" name="channel_id" required>
                                <option value="">Choose One...</option>
                                @foreach($channels as $channel)
                                    <option value="{{ $channel->id }}" {{ old('channel_id') == $channel->id ? 'selected' : ''}}>
                                        {{$channel->name}}
                                    </option>
                                @endforeach
                            </select>

                            <div class="form-group">
                                <label for="title">Title:</label>
                                <input type="text" class="form-control" id="title" name="title" placeholder="Input..."
                                       value="{{old('title')}}" required>
                            </div>

                            <div class="form-group">
                                <label for="title">Body:</label>
                                <textarea name="body" type="text" class="form-control" rows="8"
                                          placeholder="Input..." required>{{old('body')}}</textarea>
                            </div>

                            <div class="form-group"></div>
                            <button type="submit" class="btn btn-primary">Submit</button>

                            @if(count($errors))
                                <ul class="alert alert-danger" style="margin-top: 10px">
                                    @foreach($errors->all() as $error)
                                        <li>{{$error}}</li>
                                    @endforeach
                                </ul>
                            @endif
                        </form>

                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection
