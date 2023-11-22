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
                    <h3 class="m-0">Dashboard</h3>
                </div><!-- /.col -->
                <div class="col-sm-6">
                    <ol class="breadcrumb float-sm-right">
                        <li class="breadcrumb-item"><a href="{{ route('dashboard') }}">Home</a></li>
                        <li class="breadcrumb-item active">{{ $borrower->first_name }}</li>
                    </ol>
                </div><!-- /.col -->
            </div><!-- /.row -->
        </div><!-- /.container-fluid -->
    </div>
    <!-- /.content-header -->

    <!-- Main content -->
    <section class="content">




        <section style="background-color: #eee;">
            <div class="container py-5">
              <div class="row">
                <div class="col">
                  <nav aria-label="breadcrumb" class="bg-light rounded-3 p-3 mb-4">
                    <ol class="breadcrumb mb-0">
                      <li class="breadcrumb-item"><a href="#">Home</a></li>
                      <li class="breadcrumb-item"><a href="#">User</a></li>
                      <li class="breadcrumb-item active" aria-current="page">User Profile</li>
                    </ol>
                  </nav>
                </div>
              </div>
          
              <div class="row">
                <div class="col-lg-4">
                  <div class="card mb-4">
                    <div class="card-body text-center">
                    @if(auth()->user()->profilepic)
                      <img src="{{asset('attatchments_loans/'.auth()->user()->profilepic)}}" alt="avatar"
                        class="rounded-circle img-fluid" style="width: 150px;">
                        @else 
                        <img src="{{asset('avatar.png')}}" alt="avatar"
                        class="rounded-circle img-fluid" style="width: 150px;">
                        @endif
                      <h5 class="my-3">{{ $borrower->first_name.' '.$borrower->last_name }}</h5>
                      <p class="text-muted mb-1">{{ $borrower->email }}</p>
                      <p class="text-muted mb-1">{{ $borrower->gender }}</p>
                      <p class="text-muted mb-4">{{ $borrower->dob }}</p>
                      <p class="text-muted mb-4">{{ $borrower->occupation }}</p>
                      <div class="d-flex justify-content-center mb-2">
                        
                        <button type="button" class="btn btn-outline-primary ms-1"><a href="mailto:{{ $borrower->email }}">Message</a></button>
                      </div>
                    </div>
                  </div>
                </div>

                  <div class="card mb-4 col-lg-8">
                    <div class="card-body p-0">
                      <ul class="list-group list-group-flush rounded-3">
                        <li class="list-group-item d-flex justify-content-between align-items-center p-3">
                          Net Salary/Income
                          <p class="mb-0">ZMW </p>
                        </li>
                        <li class="list-group-item d-flex justify-content-between align-items-center p-3">
                         Company/Ministry
                          <p class="mb-0"></p>
                        </li>
                        <li class="list-group-item d-flex justify-content-between align-items-center p-3">
                         Employee-ID
                          <p class="mb-0"></p>
                        </li>
                        <li class="list-group-item d-flex justify-content-between align-items-center p-3">
                        Added on:
                          <p class="mb-0"> {{$borrower->created_at}}</p>
                        </li>
                        <li class="list-group-item d-flex justify-content-between align-items-center p-3">
                          Reference#
                          <p class="mb-0"></p>
                        </li>
                      </ul>
                    </div>
                  </div>                
              </div>


<div class="row">
                <div class="col-lg-4">
                  <div class="card mb-4">
                    <div class="card-body">
                      <div class="row">
                        <div class="col-sm-3">
                          <p class="mb-0">Full Name</p>
                        </div>
                        <div class="col-sm-9">
                          <p class="text-muted mb-0">{{ $borrower->first_name. ' '.$borrower->last_name }}</p>
                        </div>
                      </div>
                      <hr>
                      <div class="row">
                        <div class="col-sm-3">
                          <p class="mb-0">Email</p>
                        </div>
                        <div class="col-sm-9">
                          <p class="text-muted mb-0">{{ $borrower->email }}</p>
                        </div>
                      </div>
                      <hr>
                      <div class="row">
                        <div class="col-sm-3">
                          <p class="mb-0">Phone</p>
                        </div>
                        <div class="col-sm-9">
                          <p class="text-muted mb-0">{{ $borrower->phone }}</p>
                        </div>
                      </div>
                      <hr>
                      <div class="row">
                        <div class="col-sm-3">
                          <p class="mb-0">National ID</p>
                        </div>
                        <div class="col-sm-9">
                          <p class="text-muted mb-0"></p>
                        </div>
                      </div>
                      <hr>
                      <div class="row">
                        <div class="col-sm-3">
                          <p class="mb-0">Address</p>
                        </div>
                        <div class="col-sm-9">
                          <p class="text-muted mb-0">{{ $borrower->address }}</p>
                        </div>
                      </div>
                    </div>
                  </div>
                </div>

                  
                    <div class="col-lg-4">
                      <div class="card mb-4 mb-md-0">
                        <div class="card-body">
                          <p class="mb-4"><span class="text-primary font-italic me-1">Next of Kin</span> Details
                          </p>
                          <p class="mb-1" style="font-size: .77rem;">Names: </p>
                         
                           
                          
                          <p class="mt-4 mb-1" style="font-size: .77rem;">Relationship</p>
                          
                          
                          
                          <p class="mt-4 mb-1" style="font-size: .77rem;">Phone</p>
                          
                          
                          
                          <p class="mt-4 mb-1" style="font-size: .77rem;">Addrress</p>
                          
                          
                          
                         
                        </div>
                      </div>
                    </div>

                    <div class="col-lg-4">
                      <div class="card mb-4 mb-md-0">
                        <div class="card-body">
                          <p class="mb-4"><span class="text-primary font-italic me-1">Bank</span> Details
                          </p>
                          <p class="mb-1" style="font-size: .77rem;">Bank Name</p>
                         
                         
                         
                          <p class="mt-4 mb-1" style="font-size: .77rem;">BankBranch</p>
                         
                         
                          
                          <p class="mt-4 mb-1" style="font-size: .77rem;">Bank Account Number</p>
                         
                          
                         
                          <p class="mt-4 mb-1" style="font-size: .77rem;">Bank Account Name</p>
                         
                          
                          
                          <p class="mt-4 mb-1" style="font-size: .77rem;">Mobile Money</p>
                          
                           
                          
                        </div>
                      </div>
                    </div>
                  </div>

                </div>
              </div>
            
          </section>
          
           
    </section>
    <!-- /.content -->
</div>




@include('layouts.footer')
