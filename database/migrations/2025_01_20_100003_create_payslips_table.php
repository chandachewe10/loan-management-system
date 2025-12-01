<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('payslips', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('organization_id')->nullable();
            $table->unsignedBigInteger('branch_id')->nullable();
            $table->unsignedBigInteger('payroll_run_id');
            $table->unsignedBigInteger('employee_id');
            $table->string('payslip_number')->unique();
            
            // Earnings
            $table->decimal('basic_salary', 10, 2);
            $table->decimal('housing_allowance', 10, 2)->default(0);
            $table->decimal('transport_allowance', 10, 2)->default(0);
            $table->decimal('medical_allowance', 10, 2)->default(0);
            $table->decimal('other_allowances', 10, 2)->default(0);
            $table->decimal('gross_salary', 10, 2);
            
            // Deductions
            $table->decimal('paye', 10, 2)->default(0);
            $table->decimal('napsa', 10, 2)->default(0);
            $table->decimal('nhima', 10, 2)->default(0); // National Health Insurance
            $table->decimal('other_deductions', 10, 2)->default(0);
            $table->decimal('total_deductions', 10, 2);
            
            // Net Pay
            $table->decimal('net_pay', 10, 2);
            
            // Status
            $table->boolean('payslip_sent')->default(false);
            $table->dateTime('payslip_sent_at')->nullable();
            $table->string('payslip_file_path')->nullable();
            
            $table->timestamps();
            
            $table->foreign('payroll_run_id')->references('id')->on('payroll_runs')->onDelete('cascade');
            $table->foreign('employee_id')->references('id')->on('employees')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('payslips');
    }
};

