<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class DownloadLog extends Model
{
    protected $fillable = [
        'student_id',
        'downloadable_file_id',
        'ip_address',
        'user_agent',
    ];

    public function student()
    {
        return $this->belongsTo(Student::class);
    }

    public function downloadableFile()
    {
        return $this->belongsTo(DownloadableFile::class);
    }
}
