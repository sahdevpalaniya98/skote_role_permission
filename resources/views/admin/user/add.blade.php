@extends('layouts.app')
@if(isset($page_title) && $page_title!='')
    @section('title', $page_title.' | '.config('app.name'))
@else
    @section('title', config('app.name'))
@endif


@section('content')
	<div class="row">
	    <div class="col-12">
	        <div class="card">
	            <div class="card-body">
	            	<form action="{{ route('admin.user.store') }}" name="addfrm" id="addfrm" method="POST" enctype="multipart/form-data">
	            	    @csrf
	                    <div class="mb-3">
	                        <label class="form-label" for="role_id">Role</label>
	                        <select class="form-select" name="role_id" id="role_id">
                                <option value="">Please select role</option>
                                @if(isset($roles) && count($roles)>0)
                                    @foreach($roles as $key => $value)
                                        <option value="{{$value->id}}" @if (old('role_id') == $value->id) selected="selected" @endif>{{ $value->display_name }}</option>
                                    @endforeach
                                @endif
                            </select>
                            @error('role_id')
                                <span class="invalid-feedback" role="alert">
                                    {{ $message }}
                                </span>
                            @enderror
	                    </div>
                        <div class="mb-3">
	                        <label class="form-label" for="name">Full Name</label>
	                        <input type="text" class="form-control" name="name" id="name" value="{{ old( 'name') }}" maxlength="150">
	                        @error('name')
	                            <span class="invalid-feedback" role="alert">
	                                {{ $message }}
	                            </span>
	                        @enderror
	                    </div>
                        <div class="mb-3">
	                        <label class="form-label" for="email">Email Address</label>
	                        <input type="email" class="form-control" name="email" id="email" value="{{ old( 'email') }}" maxlength="150">
	                        @error('email')
	                            <span class="invalid-feedback" role="alert">
	                                {{ $message }}
	                            </span>
	                        @enderror
	                    </div>
                        <div class="mb-3">
	                        <label class="form-label" for="password">Password</label>
	                        <input type="password" class="form-control" name="password" id="password" value="{{ old( 'password') }}" maxlength="150">
	                        @error('password')
	                            <span class="invalid-feedback" role="alert">
	                                {{ $message }}
	                            </span>
	                        @enderror
	                    </div>
                        <div class="mb-3">
	                        <label class="form-label" for="password_confirmation">Confirm Password</label>
	                        <input type="password" class="form-control" name="password_confirmation" id="password_confirmation" value="{{ old( 'password_confirmation') }}" maxlength="150">
	                        @error('password_confirmation')
	                            <span class="invalid-feedback" role="alert">
	                                {{ $message }}
	                            </span>
	                        @enderror
	                    </div>
                        <div class="mb-3">
                            <div class="form-group @error('status') is-invalid @enderror">
                                <label class="control-label">Status <span style="color: red">*</span></label>
                                <div class="radio-list">
                                    <label class="radio-inline me-2">
                                        <div class="form-check form-radio-primary mb-3">
                                            <input type="radio" class="form-check-input" name="status" id="active-radio" value="1" checked>
                                            <label class="form-check-label" for="active-radio">Active</label>
                                        </div>
                                    </label>
                                    <label class="radio-inline">
                                        <div class="form-check form-radio-primary mb-3">
                                            <input type="radio" class="form-check-input" name="status" id="inactive-radio" value="0">
                                            <label class="form-check-label" for="inactive-radio">InActive</label>
                                        </div>
                                    </label>
                                    @error('status')
                                        <span class="invalid-feedback" role="alert">
                                            {{ $message }}
                                        </span>
                                    @enderror
                                </div>
                            </div>
                        </div>
	                    <div class="d-flex flex-wrap gap-2 fr-button">
	                        <button type="submit" class="btn btn-primary waves-effect waves-light">
	                            Save
	                        </button>
	                        <a href="{{ route('admin.permission.index')}}" class="btn btn-secondary waves-effect">
	                        	Cancel
	                        </a>
	                    </div>
	                </form>
	            </div>
	        </div>
	    </div>
	</div>
@endsection
@section('scripts')
    @parent
	<script type="text/javascript">
	    $(document).ready(function() {
	        setTimeout(function(){ $(".invalid-feedback").hide(); }, 7000);
	        $("#addfrm").validate({
	            ignore: [],
	            errorElement: 'span',
                errorPlacement: function (error, element) {
                    if(element.hasClass('dropify')){
                        error.insertAfter(element.closest('div'));
                    } else if(element.hasClass('select2-hidden-accessible')) {
                        error.insertAfter(element.next('span'));
                    } else if (element.attr("type") == "radio") {
                        $(element).parents('.radio-list').append(error)
                    } else {
                        error.insertAfter(element);
                    }
                },
	            rules: {
                    role_id:{
                        required:true
                    },
                    name:{
                        required:true
                    },
                    email:{
                        required:true,
                        email: true,
                        remote: {
                            url: "{!! route('admin.user.exists') !!}",
                            type: "POST",
                            data:{id:$("#id").val()},
                            headers: {
                                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                            }
                        }
                    },
                    password: {
                        required: {
                            depends: function () {
                                return $('#id').val() == '';
                            }
                        },
                        minlength:8,
                    },
                    password_confirmation: {
                        required: {
                            depends: function () {
                                return $('#id').val() == '';
                            }
                        },
                        equalTo: "#password",
                        minlength:8,
                    },
                    status:{
                        required:true
                    }
                },
                messages:{
                    role_id:{
                        required:"The role field is required."
                    },
                    name:{
                        required:"The fullname field is required."
                    },
                    email:{
                        required:"The email field is required.",
                        remote: "The email has already been taken."
                    },
                    password: {
                        required: "The password field is required.",
                        minlength: "Your password must be at least 8 characters long"
                    },
                    password_confirmation: {
                        required: "The confirm password field is required.",
                        equalTo: "Enter Confirm Password Same as Password"
                    },
                    status:{
                        required:"The status field is required."
                    }
                },
                submitHandler: function(e) {
                    e.submit()
                }
	        });
	    });
	</script>
@endsection
