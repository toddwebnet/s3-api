<?php

namespace App\Services;

class S3AppService
{
    public function dirTree($typeFilter = null)
    {

        list($files, $folders) = $this->getObjects($typeFilter);
        return $this->buildDirs('/', $files, $folders);

    }

    private function getObjects($typeFilter, $pathFilter = null)
    {
        $s3 = new S3StorageService();
        $fileList = $s3->getObjectList();
        $files = [];
        $folders = [];
        foreach ($fileList as $file) {
            $arFile = explode('/', $file);
            $maxIndex = count($arFile) - 1;
            $fileName = $arFile[$maxIndex];
            unset ($arFile[$maxIndex]);
            $folders = $this->appendToFolders($arFile, $folders);
            $folderIndex = '/' . implode('/', $arFile);
            if($pathFilter === null || $pathFilter == $folderIndex) {
                if (!array_key_exists($folderIndex, $files)) {
                    $files[$folderIndex] = [];
                }
                if ($this->isValidFile($fileName, $typeFilter)) {
                    $files[$folderIndex][] = $fileName;
                }
            }
        }
        return [$files, $folders];
    }

    private function isValidFile($fileName, $typeFilter)
    {
        if ($typeFilter == 'image') {
            $validExts = [
                'jpg', 'jpeg',
                'png', 'gif'
            ];
            return in_array(pathinfo($fileName, PATHINFO_EXTENSION), $validExts);
        }
        return true;

    }

    private function buildDirs($curDir, $files, $folders)
    {
        $dirs[] = $this->buildDir($curDir, $files, $folders);
        if ($curDir == '/') {
            $curDir = '';
        }
        ksort($folders);
        foreach ($folders as $key => $value) {
            $curDir .= '/' . $key;
            $dirs = array_merge($dirs, $this->buildDirs($curDir, $files, $value));
        }

        return $dirs;

    }

    private function buildDir($dirPath, $files, $folders)
    {
        $numFiles = array_key_exists($dirPath, $files) ? count($files[$dirPath]) : 0;
        $numFolders = count($folders);
        return [
            'p' => '/fileman' . $dirPath,
            'f' => $numFiles,
            'd' => $numFolders
        ];
    }

    private function appendToFolders($arFile, $folders)
    {
        $pointer = &$folders;
        for ($x = 0; $x < count($arFile); $x++) {
            if (!array_key_exists($arFile[$x], $folders)) {
                $pointer[$arFile[$x]] = [];
            }
            $pointer = &$pointer[$arFile[$x]];
        }
        return $folders;
    }

    public function fileList($dir, $typeFilter)
    {
        list($files, $folders) = $this->getObjects($typeFilter, $dir);
        dd($files);
        if (array_key_exists($dir, $files)) {
            dd($files[$dir]);
        } else {
            return [];
        }
    }
}
