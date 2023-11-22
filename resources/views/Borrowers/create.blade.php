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
                        <li class="breadcrumb-item active">Add Borrower</li>
                    </ol>
                </div><!-- /.col -->
            </div><!-- /.row -->
        </div><!-- /.container-fluid -->
    </div>
    <!-- /.content-header -->

    <!-- Main content -->
    <section class="content">

        <div class="shadow p-3 mb-5 bg-white rounded">

            <form>




                <div class="modal-body bg-white">

                    <ul class="nav nav-pills nav-justified mb-3">
                        <li class="nav-item">
                            <a class="nav-link active" data-toggle="pill" href="#pills-details">Details</a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link " data-toggle="pill" href="#pills-nextofkin">Next of Kin</a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link" data-toggle="pill" href="#pills-bank">Bank Details</a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link" data-toggle="pill" href="#pills-files">Files</a>
                        </li>

                    </ul>

                    <hr>

                    <div class="tab-content">

                        <div class="tab-pane fade show active" id="pills-details">
                            <div class="row">
                                <div class="col-6">
                                    <div class="form-group">

                                        <label>Borrower First Name<strong class="text-danger">*</strong></label>
                                        <div class="input-group">
                                            <div class="input-group-prepend">
                                                <span class="input-group-text"><i class="fa fa-umbrella"></i></span>
                                            </div>
                                            <input type="text" name="first_name"
                                                class="form-control @error('first_name') is-invalid @enderror"
                                                placeholder="First Name">

                                        </div>
                                        @error('first_name')
                                            <span class="text-danger">{{ $message }}</span>
                                        @enderror
                                    </div>
                                </div>

                                <div class="col-6">
                                    <div class="form-group">

                                        <label>Borrower Last Name<strong class="text-danger">*</strong></label>
                                        <div class="input-group">
                                            <div class="input-group-prepend">
                                                <span class="input-group-text"><i class="fa fa-umbrella"></i></span>
                                            </div>
                                            <input type="text" name="last_name"
                                                class="form-control @error('last_name') is-invalid @enderror"
                                                placeholder="Name of tourist attraction">

                                        </div>
                                        @error('last_name')
                                            <span class="text-danger">{{ $message }}</span>
                                        @enderror
                                    </div>
                                </div>


                                <div class="col-6">
                                    <div class="form-group">
                                        <label for="gender">Gender</label> <span class="text-danger">*</span>
                                        <select id="gender"
                                            class="form-control @error('gender') is-invalid @enderror" name="gender">
                                            <option value="">Select Gender</option>

                                            <option value="MALE">Male</option>
                                            <option value="FEMALE">Female</option>

                                        </select>
                                        @error('gender')
                                            <div class="alert alert-danger">{{ $message }}</div>
                                        @enderror
                                    </div>
                                </div>








                                <div class="col-6">
                                    <div class="form-group">
                                        <label for="dbo">Date of Birth (DOB)</label> <span
                                            class="text-danger">*</span>
                                        <input id="dbo" type="date" name="dob" value="{{ old('dob') }}"
                                            class="form-control @error('dob') is-invalid @enderror">
                                        @error('dob')
                                            <div class="alert alert-danger">{{ $message }}</div>
                                        @enderror
                                    </div>
                                </div>

                                <div class="col-6">
                                    <div class="form-group">
                                        <label for="occupation">Occupation</label> <span class="text-danger">*</span>
                                        <select id="occupation"
                                            class="form-control @error('occupation') is-invalid @enderror"
                                            name="occupation">
                                            <option value="">Select occupation</option>

                                            <option value="Civil Servant">Civil Servant</option>
                                            <option value="Private Sector">Private Sector</option>
                                            <option value="Self Employed">Self Employed</option>
                                            <option value="Student">Student</option>
                                            <option value="Business Man/Woman">Business Man/Woman</option>
                                            <option value="Unemployed">Unemployed</option>

                                        </select>
                                        @error('occupation')
                                            <div class="alert alert-danger">{{ $message }}</div>
                                        @enderror
                                    </div>
                                </div>




                                <div class="col-6">
                                    <div class="form-group">
                                        <label for="identification">National ID </label> <span
                                            class="text-danger">*</span>
                                        <input id="identification" type="text" name="identification"
                                            value="{{ old('identification') }}"
                                            class="form-control @error('identification') is-invalid @enderror">
                                        @error('identification')
                                            <div class="alert alert-danger">{{ $message }}</div>
                                        @enderror
                                    </div>

                                </div>



                                <div class="col-lg-6 col-md-6">
                                    <div class="form-group">
                                        <label for="mobile">Borrower Mobile</label> <span
                                            class="text-danger">*</span>
                                        <input id="mobile" type="text" name="mobile"
                                            value="{{ old('mobile') }}"
                                            class="form-control @error('mobile') is-invalid @enderror">
                                        @error('mobile')
                                            <div class="alert alert-danger">{{ $message }}</div>
                                        @enderror
                                    </div>
                                </div>



                                <div class="col-6">
                                    <div class="form-group">
                                        <label for="email">Borrower Email</label>
                                        <input id="email" type="text" name="email"
                                            value="{{ old('email') }}"
                                            class="form-control @error('email') is-invalid @enderror">
                                        @error('email')
                                            <div class="alert alert-danger">{{ $message }}</div>
                                        @enderror
                                    </div>

                                </div>
                                <div class="col-6">
                                    <div class="form-group">
                                        <label for="address">Borrower Address</label> <span
                                            class="text-danger">*</span>
                                        <input id="address" type="text" name="address"
                                            value="{{ old('address') }}"
                                            class="form-control @error('address') is-invalid @enderror">
                                        @error('address')
                                            <div class="alert alert-danger">{{ $message }}</div>
                                        @enderror
                                    </div>
                                </div>
                                <div class="col-6">


                                    <div class="form-group">
                                        <label for="city">Borrower City</label> <span class="text-danger">*</span>
                                        <input id="city" type="text" name="city"
                                            value="{{ old('city') }}"
                                            class="form-control @error('city') is-invalid @enderror">
                                        @error('city')
                                            <div class="alert alert-danger">{{ $message }}</div>
                                        @enderror
                                    </div>

                                </div>
                                <div class="col-6">

                                    <div class="form-group">
                                        <label for="province">Borrower Province</label> <span
                                            class="text-danger">*</span>
                                        <input id="province" type="text" name="province"
                                            value="{{ old('province') }}"
                                            class="form-control @error('province') is-invalid @enderror">
                                        @error('province')
                                            <div class="alert alert-danger">{{ $message }}</div>
                                        @enderror
                                    </div>

                                </div>
                                <div class="col-6">
                                    <div class="form-group">
                                        <label for="zipcode">Zip Code</label>
                                        <input id="zipcode" type="text" name="zipcode"
                                            value="{{ old('zipcode') }}"
                                            class="form-control @error('zipcode') is-invalid @enderror">
                                        @error('zipcode')
                                            <div class="alert alert-danger">{{ $message }}</div>
                                        @enderror
                                    </div>

                                </div>

                            </div>

                        </div>


                        <div class="tab-pane fade active" id="pills-nextofkin">

                            <div class="row">
                                <div class="col-6">

                                    <div class="form-group">
                                        <label for="nexOfKinFirstName">Next of Kin First Name</label>
                                        <input id="nexOfKinFirstName" type="text" name="next_of_kin_first_name"
                                            value="{{ old('next_of_kin_first_name') }}"
                                            class="form-control @error('next_of_kin_first_name') is-invalid @enderror">
                                        @error('next_of_kin_first_name')
                                            <div class="alert alert-danger">{{ $message }}</div>
                                        @enderror
                                    </div>
                                </div>



                                <div class="col-6">
                                    <div class="form-group">
                                        <label for="nexOfKinLastName">Next of Kin Last Name</label>
                                        <input id="nexOfKinLastName" type="text" name="next_of_kin_last_name"
                                            value="{{ old('next_of_kin_last_name') }}"
                                            class="form-control @error('next_of_kin_last_name') is-invalid @enderror">
                                        @error('next_of_kin_last_name')
                                            <div class="alert alert-danger">{{ $message }}</div>
                                        @enderror
                                    </div>
                                </div>


                                <div class="col-6">
                                    <div class="form-group">
                                        <label for="nexOfKinRelationship">Relationship to Next of
                                            Kin</label>
                                        <input id="nexOfKinRelationship" type="text" name="next_of_kin_last_name"
                                            value="{{ old('next_of_kin_last_name') }}"
                                            class="form-control @error('next_of_kin_last_name') is-invalid @enderror">
                                        @error('next_of_kin_last_name')
                                            <div class="alert alert-danger">{{ $message }}</div>
                                        @enderror
                                    </div>
                                </div>

                                <div class="col-6">

                                    <div class="form-group">
                                        <label for="nexOfKinPhone">Phone Number For Next of Kin
                                        </label>
                                        <input id="nexOfKinPhone" type="text" name="next_of_kin_phone"
                                            value="{{ old('next_of_kin_phone') }}"
                                            class="form-control @error('next_of_kin_phone') is-invalid @enderror">
                                        @error('next_of_kin_phone')
                                            <div class="alert alert-danger">{{ $message }}</div>
                                        @enderror
                                    </div>

                                </div>

                            </div>
                        </div>



                        <div class="tab-pane fade active" id="pills-bank">
                            <div class="row">
                                <div class="col-6">
                                    <div class="form-group">
                                        <label for="bankName">Bank Name</label>
                                        <input id="bankName" type="text" name="bank_name"
                                            value="{{ old('bank_name') }}"
                                            class="form-control @error('bank_name') is-invalid @enderror">
                                        @error('bank_name')
                                            <div class="alert alert-danger">{{ $message }}</div>
                                        @enderror
                                    </div>
                                </div>

                                <div class="col-6">
                                    <div class="form-group">
                                        <label for="bankBranch">Bank Branch</label>
                                        <input id="bankName" type="text" name="bank_branch"
                                            value="{{ old('bank_branch') }}"
                                            class="form-control @error('bank_branch') is-invalid @enderror">
                                        @error('bank_branch')
                                            <div class="alert alert-danger">{{ $message }}</div>
                                        @enderror
                                    </div>
                                </div>


                                <div class="col-6">
                                    <div class="form-group">
                                        <label for="bankSortCode">Bank Sort Code</label>
                                        <input id="bankSortCode" type="text" name="bank_sort_code"
                                            value="{{ old('bank_sort_code') }}"
                                            class="form-control @error('bank_sort_code') is-invalid @enderror">
                                        @error('bank_sort_code')
                                            <div class="alert alert-danger">{{ $message }}</div>
                                        @enderror
                                    </div>
                                </div>



                                <div class="col-6">
                                    <div class="form-group">
                                        <label for="bankAccountName">Bank Account Name</label>
                                        <input id="bankAccountName" type="text" name="bank_account_name"
                                            value="{{ old('bank_account_name') }}"
                                            class="form-control @error('bank_account_name') is-invalid @enderror">
                                        @error('bank_account_name')
                                            <div class="alert alert-danger">{{ $message }}</div>
                                        @enderror
                                    </div>
                                </div>

                                <div class="col-6">
                                    <div class="form-group">
                                        <label for="bankAccountNumber">Bank Account Number</label>
                                        <input id="bankAccountNumber" type="text" name="bank_account_number"
                                            value="{{ old('bank_account_number') }}"
                                            class="form-control @error('bank_account_number') is-invalid @enderror">
                                        @error('bank_account_number')
                                            <div class="alert alert-danger">{{ $message }}</div>
                                        @enderror
                                    </div>
                                </div>

                            </div>

                        </div>



                        <div class="tab-pane fade active" id="pills-files">



                            <div class="row">


                                
                                <div class="col-12">
                                    <div class="">
                                        <div class="">
                                            <i class="fas fa-folder-open fa-4x text-blue-700"></i>
                                            <span class="block text-gray-400 font-normal">Attach
                                                your files here</span>
                                        </div>
                                    </div>
                                    <input type="file" multiple class="form-control" name="files[]">
                                </div>
                            </div>







                        </div>

                    </div>
                    <div class="modal-footer bg-white">
                        <button type="submit" name="add_client" class="btn btn-primary text-bold"><i
                                class="fa fa-check mr-2"></i>Save Changes</button>


                    </div>
            </form>

        </div>




    </section>
    <!-- /.content -->
</div>
@include('layouts.footer')
