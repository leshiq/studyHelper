<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class FileAccess extends Model
{
    protected $fillable = [
        'student_id',
        'downloadable_file_id',
        'expires_at',
    ];

    protected function casts(): array
    {
        return [
            'expires_at' => 'datetime',
        ];
    }

    public function student()
    {
        return $this->belongsTo(Student::class);
    }

    public function downloadableFile()
    {
        return $this->belongsTo(DownloadableFile::class);
    }
}
