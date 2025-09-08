    <?php

    use Illuminate\Database\Migrations\Migration;
    use Illuminate\Database\Schema\Blueprint;
    use Illuminate\Support\Facades\Schema;

    return new class extends Migration
    {
        public function up(): void
        {
            Schema::create('payments', function (Blueprint $table) {
                $table->id();
                $table->foreignId('credit_id')->constrained()->onDelete('cascade');
                $table->date('tanggal_bayar');
                $table->decimal('jumlah_bayar', 15, 2);
                $table->unsignedInteger('tenor_ke');
                $table->decimal('denda', 15, 2)->default(0);
                $table->enum('metode', ['cash', 'transfer']);
                $table->string('bukti_url')->nullable();
                $table->enum('status', ['paid', 'late'])->default('paid');
                $table->timestamps();
            });
        }

        public function down(): void
        {
            Schema::dropIfExists('payments');
        }
    };
