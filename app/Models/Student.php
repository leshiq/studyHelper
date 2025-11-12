<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

class Student extends Authenticatable
{
    use Notifiable;

    protected $fillable = [
        'name',
        'email',
        'password',
        'is_active',
        'is_admin',
    ];

    protected $hidden = [
        'password',
        'remember_token',
    ];

    protected function casts(): array
    {
        return [
            'is_active' => 'boolean',
            'is_admin' => 'boolean',
            'password' => 'hashed',
        ];
    }

    public function fileAccesses()
    {
        return $this->hasMany(FileAccess::class);
    }

    public function downloadableFiles()
    {
        return $this->belongsToMany(DownloadableFile::class, 'file_accesses')
            ->withPivot('expires_at')
            ->withTimestamps();
    }

    public function downloadLogs()
    {
        return $this->hasMany(DownloadLog::class);
    }
}
