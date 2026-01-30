<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('import_log_aktifitas', function (Blueprint $table) {
            $table->id();

            // path file hasil upload
            $table->string('file');

            // processing | completed | failed
            $table->string('status', 20)->default('processing')->index();

            // ringkasan proses
            $table->unsignedInteger('total')->default(0);
            $table->unsignedInteger('success')->default(0);
            $table->unsignedInteger('failed')->default(0);

            // simpan detail error (json/text)
            $table->longText('errors')->nullable();

            $table->timestamps();

            // opsional: untuk query "yang paling baru"
            $table->index(['created_at']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('import_log_aktifitas');
    }
};
