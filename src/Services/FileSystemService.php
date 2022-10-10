<?php

namespace App\Services;

use Safe;
use ZipArchive;

final class FileSystemService
{
    public static function delete(string $path, string $name = null): void
    {
        if (self::exist($path, $name)) {
            Safe\unlink(sprintf('%s%s', $path, $name));
        }
    }

    public static function exist(string $path, string $name = null): bool
    {
        if (file_exists(sprintf('%s%s', $path, $name))) {
            return true;
        }

        return false;
    }

    /**
     * @return null|string
     */
    public static function find(string $path, ?string $name)
    {
        if (!(self::exist($path, null))) {
            return null;
        }

        $scandir = Safe\scandir($path);

        foreach ($scandir as $file) {
            if ($name == $file) {
                return sprintf('%s%s', $path, $file);
            }
        }

        $scandir = Safe\scandir($path);

        foreach ($scandir as $file) {
            if (Safe\preg_match($name ?? '', $file)) {
                return sprintf('%s%s', $path, $file);
            }
        }

        return null;
    }

    public static function download(?string $url, string $name, string $path): void
    {
        if (is_null($url)) {
            return;
        }
        self::createDirectoryIfDontExist($path);
        Safe\file_put_contents(sprintf('%s%s', $path, $name), Safe\fopen($url, 'r'));
    }

    public static function createDirectoryIfDontExist(string $path): void
    {
        if (!(self::exist($path, null))) {
            Safe\mkdir($path, 0777, true);
        }
    }

    /**
     * @return bool|string
     */
    public static function unzip(string $zipfile, string $extractPath)
    {
        $zip = new ZipArchive();

        if ('true' != $zip->open($zipfile)) {
            return false;
        }

        $unzip = $zip->getNameIndex(0);

        $zip->extractTo($extractPath);
        $zip->close();

        return $unzip;
    }

    /**
     * @return false|string
     */
    public static function getFile(string $path, string $name = null)
    {
        if (self::exist($path, $name)) {
            return Safe\file_get_contents(sprintf('%s%s', $path, $name));
        }

        return false;
    }

    /**
     * @return string
     */
    public static function stripAccents(string $str)
    {
        $str = Safe\preg_replace('/[^a-zA-Z0-9\\s]/', '', $str);

        return strtr(utf8_decode($str), utf8_decode('àáâãäçèéêëìíîïñòóôõöùúûüýÿÀÁÂÃÄÇÈÉÊËÌÍÎÏÑÒÓÔÕÖÙÚÛÜÝ'), 'aaaaaceeeeiiiinooooouuuuyyAAAAACEEEEIIIINOOOOOUUUUY');
    }
}