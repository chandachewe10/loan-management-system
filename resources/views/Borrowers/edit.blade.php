@include('layouts.header')
@include('layouts.navigation')
@include('layouts.menu')


<!-- Content Wrapper. Contains page content -->
<div class="content-wrapper">
    <!-- Content Header (Page header) -->
    <div class="content-header">
        <div class="container-fluid">
            <div class="row mb-2">
                <div class="col-sm-6">
                    <h1 class="m-0">Dashboard</h1>
                </div><!-- /.col -->
                <div class="col-sm-6">
                    <ol class="breadcrumb float-sm-right">
                        <li class="breadcrumb-item"><a href="{{ route('dashboard') }}">Home</a></li>
                        <li class="breadcrumb-item active">Edit Borrower, {{ $borrower->first_name }}</li>
                    </ol>
                </div><!-- /.col -->
            </div><!-- /.row -->
        </div><!-- /.container-fluid -->
    </div>
    <!-- /.content-header -->

    <!-- Main content -->
    <section class="content">







        <form action="{{route('borrower.update',$borrower->id)}}" method="POST"
            class="shadow p-3 mb-5 bg-white rounded">
            @csrf
            @method('put')
            <div class="row">
                <div class="col-lg-6 col-md-6">
                    <div class="form-group">
                        <label for="firstName">Borrower First Name </label> <span class="text-danger">*</span>
                        <input type="text" id="firstName" name="first_name"
                            value="{{ old('first_name', $borrower->first_name) }}"
                            class="form-control @error('first_name') is-invalid @enderror"
                            placeholder="Enter First Name">
                        @error('first_name')
                            <div class="alert alert-danger">{{ $message }}</div>
                        @enderror
                    </div>
                </div>

                <div class="col-lg-6 col-md-6">
                    <div class="form-group">
                        <label for="lastName">Borrower Last Name</label> <span class="text-danger">*</span>
                        <input type="text" id="lastName" name="lastName"
                            value="{{ old('lastName', $borrower->last_name) }}"
                            class="form-control @error('lastName') is-invalid @enderror" placeholder="Enter Code">
                        @error('lastName')
                            <div class="alert alert-danger">{{ $message }}</div>
                        @enderror
                    </div>
                </div>

                <div class="col-lg-6 col-md-6">
                    <div class="form-group">
                        <label for="gender">Gender</label> <span class="text-danger">*</span>
                        @php
                            $genders = ['MALE', 'FEMALE'];
                        @endphp
                        <select id="gender" class="form-control @error('gender') is-invalid @enderror"
                            name="gender">
                            @foreach ($genders as $gender)
                                <option {{$gender === $borrower->gender}} value="{{old('gender',$borrower->gender)}}">{{$borrower->gender}}</option>
                            @endforeach


                        </select>

                    </div>
                    @error('gender')
                        <div class="alert alert-danger">{{ $message }}</div>
                    @enderror
                </div>

                <div class="col-lg-6 col-md-6">
                    <div class="form-group">
                        <label for="dbo">Date of Birth (DOB)</label> <span class="text-danger">*</span>
                        <input id="dbo" type="date" name="dob" value="{{ old('dob', $borrower->dob) }}"
                            class="form-control @error('dob') is-invalid @enderror">
                        @error('dob')
                            <div class="alert alert-danger">{{ $message }}</div>
                        @enderror
                    </div>
                </div>


                <div class="col-lg-6 col-md-6">
                    <div class="form-group">
                        <label for="occupation">Occupation</label> <span class="text-danger">*</span>
                        @php
                            $occupations = ['Civil Servant', 'Private Sector', 'Self Employed', 'Student', 'Business Man/Woman', 'Unemployed'];
                        @endphp
                        <select id="occupation" class="form-control @error('occupation') is-invalid @enderror"
                            name="occupation">
                           
                            @foreach ($occupations as $ccupation)
                                <option {{ $ccupation === $borrower->occupation ? 'selected' : '' }}
                                    value="{{ $ccupation->occupation }}">{{ $ccupation->occupation }}</option>
                            @endforeach

                        </select>

                    </div>
                    @error('occupation')
                        <div class="alert alert-danger">{{ $message }}</div>
                    @enderror
                </div>



                <div class="col-lg-6 col-md-6">
                    <div class="form-group">
                        <label for="identification">National ID </label> <span class="text-danger">*</span>
                        <input id="identification" type="text" name="identification"
                            value="{{ old('identification',$borrower->identification) }}"
                            class="form-control @error('identification') is-invalid @enderror">
                        @error('identification')
                            <div class="alert alert-danger">{{ $message }}</div>
                        @enderror
                    </div>
                </div>


                <div class="col-lg-6 col-md-6">
                    <div class="form-group">
                        <label for="mobile">Borrower Mobile</label> <span class="text-danger">*</span>
                        <input id="mobile" type="text" name="mobile" value="{{ old('mobile',$borrower->borrower) }}"
                            class="form-control @error('mobile') is-invalid @enderror">
                        @error('mobile')
                            <div class="alert alert-danger">{{ $message }}</div>
                        @enderror
                    </div>
                </div>




                <div class="col-lg-6 col-md-6">
                    <div class="form-group">
                        <label for="email">Borrower Email</label>
                        <input id="email" type="text" name="email" value="{{ old('email',$borrower->email) }}"
                            class="form-control @error('email') is-invalid @enderror">
                        @error('email')
                            <div class="alert alert-danger">{{ $message }}</div>
                        @enderror
                    </div>
                </div>


                <div class="col-lg-6 col-md-6">
                    <div class="form-group">
                        <label for="address">Borrower Address</label> <span class="text-danger">*</span>
                        <input id="address" type="text" name="address" value="{{ old('address',$borrower->address) }}"
                            class="form-control @error('address') is-invalid @enderror">
                        @error('address')
                            <div class="alert alert-danger">{{ $message }}</div>
                        @enderror
                    </div>
                </div>


                <div class="col-lg-6 col-md-6">
                    <div class="form-group">
                        <label for="city">Borrower City</label> <span class="text-danger">*</span>
                        <input id="city" type="text" name="city" value="{{ old('city',$borrower->city) }}"
                            class="form-control @error('city') is-invalid @enderror">
                        @error('city')
                            <div class="alert alert-danger">{{ $message }}</div>
                        @enderror
                    </div>
                </div>




                <div class="col-lg-6 col-md-6">
                    <div class="form-group">
                        <label for="province">Borrower Province</label> <span class="text-danger">*</span>
                        <input id="province" type="text" name="province" value="{{ old('province',$borrower->province) }}"
                            class="form-control @error('province') is-invalid @enderror">
                        @error('province')
                            <div class="alert alert-danger">{{ $message }}</div>
                        @enderror
                    </div>
                </div>

                <div class="col-lg-6 col-md-6">
                    <div class="form-group">
                        <label for="zipcode">Zip Code</label>
                        <input id="zipcode" type="text" name="zipcode" value="{{ old('zipcode',$borrower->zipcode) }}"
                            class="form-control @error('zipcode') is-invalid @enderror">
                        @error('zipcode')
                            <div class="alert alert-danger">{{ $message }}</div>
                        @enderror
                    </div>
                </div>








            </div>


            <button type="submit" class="btn btn-primary">Update</button>
        </form>
        <br><br>
    </section>
    <!-- /.content -->
</div>
@include('layouts.footer')
