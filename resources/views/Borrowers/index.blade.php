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
                        <li class="breadcrumb-item active">Borrowers</li>
                    </ol>
                </div><!-- /.col -->
            </div><!-- /.row -->
        </div><!-- /.container-fluid -->
    </div>
    <!-- /.content-header -->

    <!-- Main content -->
    <section class="content">




        <div class="container">
            <h4>All Borrowers</h4>
            <br>
            <button class="btn btn-info"><a href="{{route('borrower.create')}}" style="color:white"> New
                    Borrower</a></button>
            <br><br>
            <table class="table table-striped" id="borrowers">
                <thead>
                    <tr>
                        <th scope="col">No</th>
                        <th scope="col">First Name</th>
                        <th scope="col">Last Name</th>
                        <th scope="col">DOB</th>
                        <th scope="col">Action</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach ($borrowers as $borrower)
                        <tr>

                            <th scope="row">{{ $borrower->id }}</th>
                            <td>{{ $borrower->first_name }}</td>
                            <td>{{ $borrower->last_name }}</td>
                            <td>{{ $borrower->dob }}</td>                           

                            <td>
                                
                                    <a href="{{route('borrower.show',$borrower->id)}}" class="btn btn-info btn-sm"><i
                                            class="fa fa-eye"></i> View</a>
                               
                               
                                    <a href=""
                                        class="btn btn-primary btn-sm"><i class="fa fa-edit"></i> Edit</a>
                                    <button type="button" class="btn btn-danger btn-sm" data-toggle="modal"
                                        data-target="#passwordConfirmationModal{{ $borrower->id }}">
                                        <i class="fa fa-trash"></i> Delete
                                    </button>
                               
                            </td>


                        </tr>
                        @include('deletes.borrower')
                    @endforeach
                </tbody>
            </table>
        </div>

        <script>
            $('#borrowers').DataTable({
                "lengthChange": false,
                dom: 'Bfrtip',

                buttons: [{
                        extend: 'pdfHtml5',
                        className: 'btn btn-info',
                        title: 'Borrowers',
                    },
                    {
                        extend: 'csvHtml5',
                        className: 'btn btn-success',
                        title: 'Borrowers',
                    },
                    {
                        extend: 'copyHtml5',
                        className: 'btn btn-primary',
                        title: 'Borrowers',
                    },
                    {
                        extend: 'excelHtml5',
                        className: 'btn btn-secondary',
                        title: 'Borrowers',
                    },


                ]

            });
        </script>



    </section>
    <!-- /.content -->
</div>




@include('layouts.footer')
