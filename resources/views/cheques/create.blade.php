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
                        <li class="breadcrumb-item active">Add Cheque</li>
                    </ol>
                </div><!-- /.col -->
            </div><!-- /.row -->
        </div><!-- /.container-fluid -->
    </div>
    <!-- /.content-header -->

    <!-- Main content -->
    <section class="content">

        <div class="shadow p-3 mb-5 bg-white rounded">

            @if (session('warning'))
                <div class="alert alert-warning">
                    {{ session('message') }}
                </div>
            @endif

            <form action="{{ route('cheque.store') }}" method="POST">
                @csrf
                <div class="modal-body bg-white">
                    <div class="row">
                        <div class="col-6">
                            <div class="form-group">

                                <label>Check Number<strong class="text-danger">*</strong></label>
                                <div class="input-group">
                                    <div class="input-group-prepend">
                                        <span class="input-group-text"><i class="fa fa-list-ol"></i></span>
                                    </div>
                                    <input type="text" value="{{ old('check_number') }}" name="check_number"
                                        class="form-control @error('check_number') is-invalid @enderror"
                                        placeholder="Check Number">

                                </div>
                                @error('check_number')
                                    <span class="text-danger">{{ $message }}</span>
                                @enderror
                            </div>
                        </div>

                        <div class="col-6">
                            <div class="form-group">
                                <label>Amount<strong class="text-danger">*</strong></label>
                                <div class="input-group">
                                    <div class="input-group-prepend">
                                        <span class="input-group-text"><i class="fa fa-money-bill"></i></span>
                                    </div>
                                    <input type="number" value="{{ old('amount') }}" name="amount"
                                        class="form-control @error('amount') is-invalid @enderror"
                                        placeholder="Amount of cheque">
                                </div>
                                @error('amount')
                                    <span class="text-danger">{{ $message }}</span>
                                @enderror
                            </div>
                        </div>

                        <div class="col-6">
                            <div class="form-group">
                                <label>Beneficiary<strong class="text-danger">*</strong></label>
                                <div class="input-group">
                                    <div class="input-group-prepend">
                                        <span class="input-group-text"><i class="fa fa-users"></i></span>
                                    </div>
                                    <input type="text" value="{{ old('beneficiary') }}" name="beneficiary"
                                        class="form-control @error('beneficiary') is-invalid @enderror"
                                        placeholder="Beneficiary of cheque">
                                </div>
                                @error('beneficiary')
                                    <span class="text-danger">{{ $message }}</span>
                                @enderror
                            </div>
                        </div>
                    </div>

                    <div class="modal-footer bg-white">
                        <a href="{{ route('cheque.index') }}" class="btn btn-default">Back</a>

                        <button type="submit" name="add_client" class="btn btn-primary text-bold"><i
                                class="fa fa-check mr-2"></i>Save Changes</button>
                    </div>
            </form>
        </div>
    </section>
    <!-- /.content -->
</div>
@include('layouts.footer')
