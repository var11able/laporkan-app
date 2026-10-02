<?php

namespace Config;

use App\Services\AuditLog;
use App\Services\AuthService;
use App\Services\PengaduanService;
use App\Services\TanggapanService;
use App\Services\UploadService;
use CodeIgniter\Config\BaseService;

class Services extends BaseService
{
    public static function auth(bool $getShared = true): AuthService
    {
        if ($getShared) {
            return static::getSharedInstance('auth');
        }

        return new AuthService(static::session());
    }

    public static function upload(bool $getShared = true): UploadService
    {
        if ($getShared) {
            return static::getSharedInstance('upload');
        }

        return new UploadService();
    }

    public static function auditLog(bool $getShared = true): AuditLog
    {
        if ($getShared) {
            return static::getSharedInstance('auditLog');
        }

        return new AuditLog();
    }

    public static function pengaduan(bool $getShared = true): PengaduanService
    {
        if ($getShared) {
            return static::getSharedInstance('pengaduan');
        }

        return new PengaduanService(upload: static::upload(), auditLog: static::auditLog());
    }

    public static function tanggapan(bool $getShared = true): TanggapanService
    {
        if ($getShared) {
            return static::getSharedInstance('tanggapan');
        }

        return new TanggapanService(upload: static::upload(), auditLog: static::auditLog());
    }
}
