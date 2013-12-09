<?php
namespace DTUXBase\Util;
/**
 * FileSystem utils
 * @category DTUXBase
 * @package DTUXBase
 * @subpackage Controller
 * @author Diego Pereira Grassato <diego.grassato@gmail.com>
 * @data 31/10/13 15:39
 */
class Filesystem
{
    /**
     * Creates path with subdirs from filename letters
     *
     * @param str $parentDirecory
     * @param str $filename
     * @param int $subdirsDepth
     * @param bool $subdirsFromBeginning
     * @param int $mode
     * @param bool $create
     *
     * @return string Creater path or FALSE
     */
    public static function createPathWithSubdirs($parentDirecory, $filename = '', $subdirsDepth = 0,
                                                 $subdirsFromBeginning = true, $mode = 0777, $create = true)
    {
        $fileNameLen = strlen($filename);
        $subdirsDepth = min($fileNameLen, $subdirsDepth);
        $subdir = '';

        for ($i = 0; $i < $subdirsDepth; $i++) {
            if ($subdirsFromBeginning)
                $subdir .= '/' . substr($filename, $i, 1);
            else
                $subdir .= '/' . substr($filename, $fileNameLen - $i - 1, 1);
        }

        $parentDirecory = $parentDirecory . $subdir;

        if ($create && !is_dir($parentDirecory))
            if (!@mkdir($parentDirecory, $mode, true))
                return false;

        $parentDirecory = $parentDirecory . '/' . $filename;

        return $parentDirecory;
    }

    /**
     * Get temporary filename
     */
    public static function getTempFilePath($parentDirectory, $extension = '')
    {
        do $tmpPath = $parentDirectory . '/' .
            Strings::createRandomString(null, Strings::ALPHABET_ALPHANUMERICAL_LOWCASE, 64)
            . ($extension == '' ? '' : ('.' . $extension));
        while (file_exists($tmpPath));

        return $tmpPath;
    }

    /**
     * Replace illegal characters in the filename
     *
     * @param sring $filename
     * @param string $replaceWith
     * @return string
     */
    public static function cleanFilename($filename, $replaceWith = '_')
    {
        return preg_replace('#\\\\|/|:|\\*|\\?|"|<|>#u', $replaceWith, $filename);
    }

    /**
     * Get remote file size by http protocol
     *
     * @param string $url
     * @param string &$effectiveUrl
     * @param int $maxRedirs
     * @param string $cookie
     * @return int On error returns FALSE
     */
    public static function httpFileSize(
        $url,
        &$effectiveUrl = null,
        $maxRedirs = 20,
        $cookie = null)
    {
        $ch = curl_init($url);

        curl_setopt($ch, \CURLOPT_NOBODY, true);
        curl_setopt($ch, \CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, \CURLOPT_HEADER, true);
        curl_setopt($ch, \CURLOPT_FOLLOWLOCATION, true);
        curl_setopt($ch, \CURLOPT_MAXREDIRS, $maxRedirs);
        curl_setopt($ch, \CURLOPT_COOKIE, $cookie);

        $data = curl_exec($ch);
        $contentLenght = curl_getinfo($ch, \CURLINFO_CONTENT_LENGTH_DOWNLOAD);
        $effectiveUrl = curl_getinfo($ch, \CURLINFO_EFFECTIVE_URL);
        $httpCode = curl_getinfo($ch, \CURLINFO_HTTP_CODE);
        curl_close($ch);

        // Check HTTP response code
        if (substr((string)$httpCode, 0, 1) != 2)
            return false; // Bad HTTP response code

        if ($contentLenght < 0)
            return false; // Can't get content length

        return $contentLenght;
    }
}