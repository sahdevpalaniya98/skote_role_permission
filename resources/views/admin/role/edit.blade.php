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
	            	<form action="{{ route('admin.role.store') }}" name="addfrm" id="addfrm" method="POST" enctype="multipart/form-data">
	            	    @csrf
                        <input type="hidden" name="id" id="id" value="{{ isset($role->id) ? $role->id : '' }}">
                        @if($role->name=='super-admin' || $role->name=='admin')
                            @if($role->name=='super-admin')
                                @php
                                    $checkbox = 'onclick="return false;"';
                                    $readonly = 'readonly';
                                @endphp
                            @else
                                @php
                                    $checkbox = '';
                                    $readonly = 'readonly';
                                @endphp
                            @endif
                        @else
                            @php
                                $checkbox = '';
                                $readonly = 'readonly';
                            @endphp
                        @endif
                        @if(isset($permissions) && count($permissions)>0)
                        <div class="row mb-3">
                            <div class="col-md-12">
                                <div class="form-group @error('permissions') is-invalid @enderror">
                                    <label class="control-label">Permission <span style="color: red">*</span></label>
                                    <div class="controls">
                                        <div class="row">
                                            @foreach($permissions as $key => $value)
                                                <div class="col-md-3">
                                                    <div class="form-check form-checkbox-outline form-check-primary">
                                                        @if($checkbox!='')
                                                        <input class="form-check-input" type="checkbox" name="permissions[]" id="{{$value->name}}" value="{{$value->name}}" {!! $checkbox !!}  checked>
                                                        <label class="form-check-label" for="{{$value->name}}">{{$value->display_name}}</label>
                                                        @else
                                                        <input class="form-check-input" type="checkbox" name="permissions[]" id="{{$value->name}}" value="{{$value->name}}" {!! $checkbox !!}  {{ ($role->hasPermissionTo($value->name))?'checked':'' }}>
                                                        <label class="form-check-label" for="{{$value->name}}">{{$value->display_name}}</label>
                                                        @endif
                                                    </div>
                                                </div>
                                            @endforeach
                                        </div>
                                        @error('permissions')
                                            <span class="invalid-feedback" role="alert">
                                                {{ $message }}
                                            </span>
                                        @enderror
                                    </div>
                                </div>
                            </div>
                        </div>
                        @endif
	                    <div class="mb-3">
	                        <label class="form-label">Name</label>
	                        <input type="text" class="form-control" name="name" id="name" value="{{ old( 'name', isset($role->display_name) ? $role->display_name : '') }}" {{ $readonly }} maxlength="150">
	                        @error('name')
	                            <span class="invalid-feedback" role="alert">
	                                {{ $message }}
	                            </span>
	                        @enderror
	                    </div>
                        <div class="mb-3">
	                        <label class="form-label">Description</label>
	                        <input type="text" class="form-control" name="description" id="description" value="{{ old( 'description', isset($role->description) ? $role->description : '') }}" maxlength="150">
	                        @error('description')
	                            <span class="invalid-feedback" role="alert">
	                                {{ $message }}
	                            </span>
	                        @enderror
	                    </div>
	                    <div class="d-flex flex-wrap gap-2 fr-button">
	                        <button type="submit" class="btn btn-primary waves-effect waves-light">
	                            Save
	                        </button>
	                        <a href="{{ route('admin.role.index')}}" class="btn btn-secondary waves-effect">
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
                errorPlacement: function(error, element) {
                    if(element.attr("type") == "checkbox"){
                        $(element).parents('.controls').append(error);
                    } else{
                        error.insertAfter(element);
                    }
                },
	            rules: {
                    'permissions[]':{
                        required:true,
                        minlength: 1
                    },
                    name:{
                        required:true,
                        remote: {
                            url: "{!! route('admin.role.exists') !!}",
                            type: "POST",
                            data:{id:$("#id").val()},
                            headers: {
                                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                            }
                        }
                    }
                },
                messages:{
                    'permissions[]':{
                        required:"Please select at least 1 permission"
                    },
                    name:{
                        required:"The name field is required.",
                        remote: "The name has already been taken."
                    }
                },
                submitHandler: function(e) {
                    e.submit()
                }
	        });
	    });
	</script>
@endsection
