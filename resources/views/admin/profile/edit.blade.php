@extends('layouts.app')
@if (isset($page_title) && $page_title != '')
    @section('title', $page_title . ' | ' . config('app.name'))
@else
    @section('title', config('app.name'))
@endif

@section('content')
    <div class="row">
        <div class="col-xl-6">
            <div class="card">
                <div class="card-body">
                    <h4 class="card-title mb-4">Profile Details</h4>
                    <form action="{{ route('admin.profile.submit') }}" name="profileform" id="profileform"
                        method="POST" enctype="multipart/form-data" autocomplete="off">
                        @csrf
                        <div class="row mb-4">
                            <label for="name" class="col-sm-3 col-form-label">Full name</label>
                            <div class="col-sm-9">
                                <input type="text" class="form-control" name="name" id="name" value="{{ old('name', isset($user->name) ? $user->name : '') }}" placeholder="Full Name Here" maxlength="150" required>
                                @error('name')
                                    <span class="invalid-feedback" role="alert">
                                        {{ $message }}
                                    </span>
                                @enderror
                            </div>
                        </div>
                        <div class="row mb-4">
                            <label for="horizontal-email-input" class="col-sm-3 col-form-label">Email</label>
                            <div class="col-sm-9">
                                <input type="email" name="email" class="form-control" id="email" value="{{ old( 'email', isset($user->email) ? $user->email : '') }}" placeholder="Email Address" maxlength="150" required>
                                @error('email')
                                    <span class="invalid-feedback" role="alert">
                                        {{ $message }}
                                    </span>
                                @enderror
                            </div>
                        </div>
                        <div class="row justify-content-end">
                            <div class="col-sm-9">
                                <div>
                                    <button type="submit" class="btn btn-primary w-md">Update</button>
                                </div>
                            </div>
                        </div>
                    </form>
                </div>
                <!-- end card body -->
            </div>
            <!-- end card -->
        </div>
        <!-- end col -->

        <div class="col-xl-6">
            <div class="card">
                <div class="card-body">
                    <h4 class="card-title mb-4">Change Password</h4>
                    <form action="{{ route('admin.password.submit') }}" name="passwordform" id="passwordform" method="POST" enctype="multipart/form-data" autocomplete="off">
                        @csrf
                        <div class="row mb-4">
                            <label for="name" class="col-sm-3 col-form-label">Current Password</label>
                            <div class="col-sm-9">
                                <input type="password" class="form-control" name="current_password" id="current_password" placeholder="Current Password" maxlength="30" required>
                                @error('current_password')
                                    <span class="invalid-feedback" role="alert">
                                        {{ $message }}
                                    </span>
                                @enderror
                            </div>
                        </div>
                        <div class="row mb-4">
                            <label for="horizontal-email-input" class="col-sm-3 col-form-label">New Password</label>
                            <div class="col-sm-9">
                                <input type="password" class="form-control" name="password" id="password" placeholder="New Password" maxlength="30" autocomplete="new-password" required>
                                @error('password')
                                    <span class="invalid-feedback" role="alert">
                                        {{ $message }}
                                    </span>
                                @enderror
                            </div>
                        </div>
                        <div class="row mb-4">
                            <label for="horizontal-email-input" class="col-sm-3 col-form-label">Confirm Password</label>
                            <div class="col-sm-9">
                                <input type="password" class="form-control" name="password_confirmation" id="password_confirmation" placeholder="Confirm Password" maxlength="30" autocomplete="new-password" required>
                                @error('password_confirmation')
                                    <span class="invalid-feedback" role="alert">
                                        {{ $message }}
                                    </span>
                                @enderror
                            </div>
                        </div>
                        <div class="row justify-content-end">
                            <div class="col-sm-9">
                                <div>
                                    <button type="submit" class="btn btn-primary w-md">Update</button>
                                </div>
                            </div>
                        </div>
                    </form>
                </div>
                <!-- end card body -->
            </div>
            <!-- end card -->
        </div>
        <!-- end col -->
    </div>
@endsection
@section('page-script')
    <script type="text/javascript">
        $(document).ready(function() {
            setTimeout(function(){ $(".invalid-feedback").hide(); }, 7000);
            $("#profileform").validate({
                rules: {
                    name:{
                        required:true
                    },
                    email:{
                        required:true,
                        email: true,
                        remote: {
                            url: "{!! route('admin.user.exists') !!}",
                            type: "POST",
                            data:{
                                id:"{{ Auth::id() }}"
                            },
                            headers: {
                                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                            }
                        }
                    }
                },
                messages: {
                    name:{
                        required:"The fullname field is required."
                    },
                    email:{
                        required:"The email field is required.",
                        remote: "The email has already been taken."
                    }
                },
                submitHandler: function(e) {
                    e.submit()
                }
            });
            $("#passwordform").validate({
                rules: {
                    current_password:{
                        required:true
                    },
                    password:{
                        required:true,
                        minlength:8
                    },
                    password_confirmation:{
                        required:true,
                        equalTo : "#password"
                    }
                 },
                messages: {
                    current_password:{
                        required:"The current password field is required."
                    },
                    password:{
                        required:"The new password field is required."
                    },
                    password_confirmation:{
                        required:"The confirm password field is required.",
                        equalTo:"Confirm password not matched with new password"
                    }
                },
                submitHandler: function(e) {
                    e.submit()
                }
            });
        });
    </script>
@endsection
