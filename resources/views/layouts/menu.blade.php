 <!-- Main Sidebar Container -->
 <aside class="main-sidebar sidebar-dark-primary elevation-4">
     <!-- Brand Logo -->
     <a href="{{ route('dashboard') }}" class="brand-link">
         <img src="{{ asset('assets/dist/img/AdminLTELogo.png') }}" class="brand-image img-circle elevation-3"
             style="opacity: .8">
         <span class="brand-text font-weight-light">Dashboard</span>
     </a>

     <!-- Sidebar -->
     <div class="sidebar">
         <!-- Sidebar user panel (optional) -->
         <div class="user-panel mt-3 pb-3 mb-3 d-flex">
             <div class="image">
                 <img src="{{ asset('assets/dist/img/user2-160x160.jpg') }}" class="img-circle elevation-2"
                     alt="User Image">
             </div>
             <div class="info">
                 <a href="" class="d-block">{{ auth()->user()->name }}</a>
             </div>
         </div>

         <!-- SidebarSearch Form -->
         <div class="form-inline">
             <div class="input-group" data-widget="sidebar-search">
                 <input class="form-control form-control-sidebar" type="search" placeholder="Search"
                     aria-label="Search">
                 <div class="input-group-append">
                     <button class="btn btn-sidebar">
                         <i class="fas fa-search fa-fw"></i>
                     </button>
                 </div>
             </div>
         </div>

         <!-- Sidebar Menu -->
         <nav class="mt-2">
             <ul class="nav nav-pills nav-sidebar flex-column" data-widget="treeview" role="menu"
                 data-accordion="false">
                 <!-- Add icons to the links using the .nav-icon class
               with font-awesome or any other icon font library -->
                 <li class="nav-item menu-open">
                     <a href="#" class="nav-link {{ request()->routeIs('borrower.*') ? 'active' : '' }}">
                         <i class="nav-icon fas fa-building"></i>
                         <p>
                             Borrowers
                             <i class="right fas fa-angle-left"></i>
                         </p>
                     </a>
                     <ul class="nav nav-treeview">
                         
                             <li class="nav-item">
                                 <a href="{{route('borrower.create')}}"
                                     class="nav-link {{ request()->routeIs('borrower.create') ? 'active' : '' }}">
                                     <i class="far fa-circle nav-icon"></i>
                                     <p>Add Borrower</p>
                                 </a>
                             </li>
                             <li class="nav-item">
                                <a href=""
                                    class="nav-link {{ request()->routeIs('vendors.create') ? 'active' : '' }}">
                                    <i class="far fa-circle nav-icon"></i>
                                    <p>Borrowers Bulk Upload</p>
                                </a>
                            </li>
                       

                         
                             <li class="nav-item">
                                 <a href=""
                                     class="nav-link {{ request()->routeIs('vendors.index') ? 'active' : '' }}">
                                     <i class="far fa-circle nav-icon"></i>
                                     <p>All Borowers</p>
                                 </a>
                             </li>
                       

                     </ul>
                 </li>


                 <li class="nav-item">
                     <a href="#"
                         class="nav-link {{ request()->routeIs('inventory_category.*') || request()->routeIs('inventory.*') ? 'active' : '' }}">
                         <i class="nav-icon fas fa-store"></i>
                         <p>
                             Loans
                             <i class="right fas fa-angle-left"></i>
                         </p>
                     </a>
                     <ul class="nav nav-treeview">
                        
                             <li class="nav-item">
                                 <a href=""
                                     class="nav-link {{ request()->routeIs('inventory_category.create') ? 'active' : '' }}">
                                     <i class="far fa-circle nav-icon"></i>
                                     <p>Add Loan Type</p>
                                 </a>
                             </li>
                        
                                                   
                         
                             <li class="nav-item">
                                 <a href=""
                                     class="nav-link {{ request()->routeIs('inventory.*') ? 'active' : '' }}">
                                     <i class="far fa-circle nav-icon"></i>
                                     <p>Add Loan</p>
                                 </a>
                             </li>

                             <li class="nav-item">
                                <a href=""
                                    class="nav-link {{ request()->routeIs('inventory.*') ? 'active' : '' }}">
                                    <i class="far fa-circle nav-icon"></i>
                                    <p>Pending Loans</p>
                                </a>
                            </li>
                            <li class="nav-item">
                                <a href=""
                                    class="nav-link {{ request()->routeIs('inventory.*') ? 'active' : '' }}">
                                    <i class="far fa-circle nav-icon"></i>
                                    <p>Active Loans</p>
                                </a>
                            </li>
                            <li class="nav-item">
                                <a href=""
                                    class="nav-link {{ request()->routeIs('inventory.*') ? 'active' : '' }}">
                                    <i class="far fa-circle nav-icon"></i>
                                    <p>Denied Loans</p>
                                </a>
                            </li>
                            <li class="nav-item">
                                <a href=""
                                    class="nav-link {{ request()->routeIs('inventory.*') ? 'active' : '' }}">
                                    <i class="far fa-circle nav-icon"></i>
                                    <p>Fully Paid Loans</p>
                                </a>
                            </li>
                            <li class="nav-item">
                                <a href=""
                                    class="nav-link {{ request()->routeIs('inventory.*') ? 'active' : '' }}">
                                    <i class="far fa-circle nav-icon"></i>
                                    <p>Overdue Loans</p>
                                </a>
                            </li>
                            <li class="nav-item">
                                <a href=""
                                    class="nav-link {{ request()->routeIs('inventory.*') ? 'active' : '' }}">
                                    <i class="far fa-circle nav-icon"></i>
                                    <p>Defaulted Loans</p>
                                </a>
                            </li>
                            <li class="nav-item">
                                <a href=""
                                    class="nav-link {{ request()->routeIs('inventory.*') ? 'active' : '' }}">
                                    <i class="far fa-circle nav-icon"></i>
                                    <p>Loan Reschedules</p>
                                </a>
                            </li>
                            <li class="nav-item">
                                <a href=""
                                    class="nav-link {{ request()->routeIs('inventory.*') ? 'active' : '' }}">
                                    <i class="far fa-circle nav-icon"></i>
                                    <p>Loan Penalty</p>
                                </a>
                            </li>
                            <li class="nav-item">
                                <a href=""
                                    class="nav-link {{ request()->routeIs('inventory.*') ? 'active' : '' }}">
                                    <i class="far fa-circle nav-icon"></i>
                                    <p>Payments Updates</p>
                                </a>
                            </li>
                            <li class="nav-item">
                                <a href=""
                                    class="nav-link {{ request()->routeIs('inventory.*') ? 'active' : '' }}">
                                    <i class="far fa-circle nav-icon"></i>
                                    <p>Loan Agreement Forms</p>
                                </a>
                            </li>
                         

                     </ul>
                 </li>




                 <li class="nav-item">
                     <a href="#" class="nav-link">
                         <i class="nav-icon fas fa-dollar-sign"></i>
                         <p>
                             Approvals
                             <i class="fas fa-angle-left right"></i>
                         </p>
                     </a>
                     <ul class="nav nav-treeview">
                         
                             <li class="nav-item">
                                 <a href="" class="nav-link">
                                     <i class="far fa-circle nav-icon"></i>
                                     <p>Review Loan - Step 1</p>
                                 </a>
                             </li>
                        
                        
                             <li class="nav-item">
                                 <a href="" class="nav-link">
                                     <i class="far fa-circle nav-icon"></i>
                                     <p>Review Loan - Step 2</p>
                                 </a>
                             </li>
                        
                         
                             <li class="nav-item">
                                 <a href="" class="nav-link">
                                     <i class="far fa-circle nav-icon"></i>
                                     <p>Review Loan - Step 3</p>
                                 </a>
                             </li>
                             <li class="nav-item">
                                <a href="" class="nav-link">
                                    <i class="far fa-circle nav-icon"></i>
                                    <p>Upload Settlements</p>
                                </a>
                            </li>
                         

                     </ul>
                 </li>





                 <li class="nav-item">
                    <a href="#" class="nav-link">
                        <i class="nav-icon fas fa-dollar-sign"></i>
                        <p>
                            Branches
                            <i class="fas fa-angle-left right"></i>
                        </p>
                    </a>
                    <ul class="nav nav-treeview">
                        
                            <li class="nav-item">
                                <a href="" class="nav-link">
                                    <i class="far fa-circle nav-icon"></i>
                                    <p>Create New Branch</p>
                                </a>
                            </li>
                       
                       
                            <li class="nav-item">
                                <a href="" class="nav-link">
                                    <i class="far fa-circle nav-icon"></i>
                                    <p>All Branches</p>
                                </a>
                            </li>
                       
                        
                            <li class="nav-item">
                                <a href="" class="nav-link">
                                    <i class="far fa-circle nav-icon"></i>
                                    <p>Switch Branch</p>
                                </a>
                            </li>
                                                 

                    </ul>
                </li>









                 <li class="nav-item">
                     <a href="#"
                         class="nav-link {{ request()->routeIs('expense_category.*') || request()->routeIs('expense.*') ? 'active' : '' }}">
                         <i class="nav-icon fas fas fa-money-check"></i>
                         <p>
                             Expenses
                             <i class="fas fa-angle-left right"></i>
                         </p>
                     </a>
                     <ul class="nav nav-treeview">
                         
                             <li class="nav-item">
                                 <a href=""
                                     class="nav-link {{ request()->routeIs('expense_category.create') ? 'active' : '' }}">
                                     <i class="far fa-circle nav-icon"></i>
                                     <p>Category</p>
                                 </a>
                             </li>
                        
                             <li class="nav-item">
                                 <a href=""
                                     class="nav-link {{ request()->routeIs('expenses.create') ? 'active' : '' }}">
                                     <i class="far fa-circle nav-icon"></i>
                                     <p>Add Expense</p>
                                 </a>
                             </li>
                        
                             <li class="nav-item">
                                 <a href=""
                                     class="nav-link {{ request()->routeIs('expenses.index') ? 'active' : '' }}">
                                     <i class="far fa-circle nav-icon"></i>
                                     <p>All Expenses</p>
                                 </a>
                             </li>
                         
                     </ul>
                 </li>






                 <li class="nav-item">
                    <a href="#"
                        class="nav-link {{ request()->routeIs('budget_categories.*') ? 'active' : '' }}">
                        <i class="nav-icon fas fa-calculator"></i>
                        <p>
                            Subscriptions
                            <i class="fas fa-angle-left right"></i>
                        </p>
                    </a>
                    <ul class="nav nav-treeview">
                        
                            <li class="nav-item">
                                <a href=""
                                    class="nav-link {{ request()->routeIs('budget_categories.create') ? 'active' : '' }}">
                                    <i class="far fa-circle nav-icon"></i>
                                    <p>System Subscriptions</p>
                                </a>
                            </li>
                       
                            <li class="nav-item">
                                <a href=""
                                    class="nav-link {{ request()->routeIs('budget_categories.index') ? 'active' : '' }}">
                                    <i class="far fa-circle nav-icon"></i>
                                    <p>Bulk SMS Subscriptions</p>
                                </a>
                            </li> 

                       
                                                
                </li>
            </ul>













                 <li class="nav-item">
                     <a href="#"
                         class="nav-link {{ request()->routeIs('budget_categories.*') ? 'active' : '' }}">
                         <i class="nav-icon fas fa-calculator"></i>
                         <p>
                             Budget
                             <i class="fas fa-angle-left right"></i>
                         </p>
                     </a>
                     <ul class="nav nav-treeview">
                         
                             <li class="nav-item">
                                 <a href=""
                                     class="nav-link {{ request()->routeIs('budget_categories.create') ? 'active' : '' }}">
                                     <i class="far fa-circle nav-icon"></i>
                                     <p>Budget Category</p>
                                 </a>
                             </li>
                        
                             <li class="nav-item">
                                 <a href=""
                                     class="nav-link {{ request()->routeIs('budget_categories.index') ? 'active' : '' }}">
                                     <i class="far fa-circle nav-icon"></i>
                                     <p>All Categories</p>
                                 </a>
                             </li>
                       
                             <li class="nav-item">
                                 <a href=""
                                     class="nav-link {{ request()->routeIs('budgets.create') ? 'active' : '' }}">
                                     <i class="far fa-circle nav-icon"></i>
                                     <p>
                                         Create Budget

                                     </p>
                                 </a>
                             </li>

                             <li class="nav-item">
                                 <a href=""
                                     class="nav-link {{ request()->routeIs('budgets.csv') ? 'active' : '' }}">
                                     <i class="far fa-circle nav-icon"></i>
                                     <p>Budget Bulk Upload</p>
                                 </a>
                             </li>
                        
                             <li class="nav-item">
                                 <a href=""
                                     class="nav-link {{ request()->routeIs('budgets.index') ? 'active' : '' }}">
                                     <i class="far fa-circle nav-icon"></i>
                                     <p>All Budgets</p>
                                 </a>
                             </li>
                       
                 </li>
             </ul>




             <li class="nav-item">
                 <a href="#" class="nav-link {{ request()->routeIs('wallets.*') ? 'active' : '' }}">
                     <i class="nav-icon fas fa-book"></i>
                     <p>
                         Bank Accounts
                         <i class="fas fa-angle-left right"></i>
                     </p>
                 </a>
                 <ul class="nav nav-treeview">
                    
                         <li class="nav-item">
                             <a href=""
                                 class="nav-link {{ request()->routeIs('cashbook.create') ? 'active' : '' }}">
                                 <i class="far fa-circle nav-icon"></i>
                                 <p>Add Account</p>
                             </a>
                         </li>
                     
                         <li class="nav-item">
                             <a href=""
                                 class="nav-link {{ request()->routeIs('wallets.index') ? 'active' : '' }}">
                                 <i class="far fa-circle nav-icon"></i>
                                 <p>All Accounts</p>
                             </a>
                         </li>
                     
                         <li class="nav-item">
                             <a href=""
                                 class="nav-link {{ request()->routeIs('create_transfer') ? 'active' : '' }}">
                                 <i class="far fa-circle nav-icon"></i>
                                 <p>Bank Transfers</p>
                             </a>
                         </li>
                    
                         <li class="nav-item">
                             <a href=""
                                 class="nav-link {{ request()->routeIs('all_transactions') ? 'active' : '' }}">
                                 <i class="far fa-circle nav-icon"></i>
                                 <p>All Transactions</p>
                             </a>
                         </li>
                    
                 </ul>
             </li>





             

             <li class="nav-item">
                 <a href="#" class="nav-link {{ request()->routeIs('roles-management.*') ? 'active' : '' }}">
                     <i class="nav-icon fas fa-user"></i>
                     <p>
                         Roles & Permissions
                         <i class="fas fa-angle-left right"></i>
                     </p>
                 </a>
                 <ul class="nav nav-treeview">
                    
                         <li class="nav-item">
                             <a href=""
                                 class="nav-link  {{ request()->routeIs('roles-management.index') ? 'active' : '' }}">
                                 <i class="far fa-circle nav-icon"></i>
                                 <p>Roles and Permissions</p>
                             </a>
                         </li>
                         <li class="nav-item">
                             <a href=""
                                 class="nav-link {{ request()->routeIs('roles-management.create') ? 'active' : '' }}">
                                 <i class="far fa-circle nav-icon"></i>
                                 <p>Add Roles</p>
                             </a>
                         </li>
                     </ul>
                 </li>
                 <li class="nav-item">
                     <a href="#" class="nav-link {{ request()->routeIs('users-management.*') ? 'active' : '' }}">
                         <i class="nav-icon fas fa-cog"></i>
                         <p>
                             Users & System
                             <i class="fas fa-angle-left right"></i>
                         </p>
                     </a>
                     <ul class="nav nav-treeview">
                         <li class="nav-item">
                             <a href=""
                                 class="nav-link {{ request()->routeIs('users-management.index') ? 'active' : '' }}">
                                 <i class="far fa-circle nav-icon"></i>
                                 <p>User Management</p>
                             </a>
                         </li>
                        
                    
                         <li class="nav-item">
                             <a href=""
                                 class="nav-link {{ request()->routeIs('branch.index') ? 'active' : '' }}">
                                 <i class="far fa-circle nav-icon"></i>
                                 <p>Manage Branches</p>
                             </a>
                         </li>
                    
                     
                         <li class="nav-item">
                             <a href=""
                                 class="nav-link {{ request()->routeIs('logs.index') ? 'active' : '' }}">
                                 <i class="far fa-circle nav-icon"></i>
                                 <p>User Activity</p>
                             </a>
                         </li>
                    
                     <li class="nav-item">
                         <a href=""
                             class="nav-link {{ request()->routeIs('profile.edit') ? 'active' : '' }}">
                             <i class="far fa-circle nav-icon"></i>
                             <p>Profile Management</p>
                         </a>
                     </li>

                 </ul>
             </li>



             <li class="nav-item">
                 <a href="#" class="nav-link {{ request()->routeIs('swift_sms.*') ? 'active' : '' }}">
                     <i class="nav-icon fas fa-cog"></i>
                     <p>
                         Third Party
                         <i class="fas fa-angle-left right"></i>
                     </p>
                 </a>
                 <ul class="nav nav-treeview">
                     
                         <li class="nav-item">
                             <a href=""
                                 class="nav-link {{ request()->routeIs('swift_sms.create') ? 'active' : '' }}">
                                 <i class="far fa-circle nav-icon"></i>
                                 <p>Bulk SMS</p>
                             </a>
                         </li>
                     

                 </ul>
             </li>










             </ul>
         </nav>
         <!-- /.sidebar-menu -->
     </div>
     <!-- /.sidebar -->
 </aside>
