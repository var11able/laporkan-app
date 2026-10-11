<?php

namespace Config;

use App\Services\AuditLog;
use App\Services\AuthService;
use App\Services\LaporanService;
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

    public static function laporan(bool $getShared = true): LaporanService
    {
        if ($getShared) {
            return static::getSharedInstance('laporan');
        }

        return new LaporanService(upload: static::upload(), auditLog: static::auditLog());
    }

    public static function tanggapan(bool $getShared = true): TanggapanService
    {
        if ($getShared) {
            return static::getSharedInstance('tanggapan');
        }

        return new TanggapanService(upload: static::upload(), auditLog: static::auditLog());
    }
}
