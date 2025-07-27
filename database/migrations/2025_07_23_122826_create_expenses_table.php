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
        Schema::create('expenses', function (Blueprint $table) {
        $table->id();
        $table->unsignedBigInteger('deputy_id');
        $table->integer('year');
        $table->integer('month');
        $table->string('expense_type');
        $table->integer('document_code');
        $table->string('document_type');
        $table->integer('document_type_code');
        $table->date('document_date');
        $table->string('document_number');
        $table->float('gross_value');
        $table->string('document_url');
        $table->string('supplier_name');
        $table->string('supplier_cnpj_cpf');
        $table->float('net_value');
        $table->float('glosa_value');
        $table->string('reimbursement_number')->nullable();
        $table->integer('batch_code');
        $table->integer('installment');
        $table->timestamps();

        $table->foreign('deputy_id')->references('id')->on('deputies')->onDelete('cascade');
    });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('expenses');
    }
};
