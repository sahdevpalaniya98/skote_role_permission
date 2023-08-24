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
	            	<form action="{{ route('admin.permission.store') }}" name="addfrm" id="addfrm" method="POST" enctype="multipart/form-data">
	            	    @csrf
	                    <div class="mb-3">
	                        <label class="form-label">Name</label>
	                        <input type="text" class="form-control" name="name" id="name" value="{{ old( 'name') }}" maxlength="150">
	                        @error('name')
	                            <span class="invalid-feedback" role="alert">
	                                {{ $message }}
	                            </span>
	                        @enderror
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
                errorPlacement: function(error, element) {
                    if(element.attr("type") == "checkbox"){
                        $(element).parents('.controls').append(error);
                    } else{
                        error.insertAfter(element);
                    }
                },
	            rules: {
                    name:{
                        required:true,
                        remote: {
                            url: "{!! route('admin.permission.exists') !!}",
                            type: "POST",
                            headers: {
                                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                            }
                        }
                    }
                },
                messages:{
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
