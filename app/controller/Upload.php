<?php

namespace app\controller;

use app\BaseController;
use think\Request;

// Define DS if not already defined
if (!defined('DS')) {
    define('DS', DIRECTORY_SEPARATOR);
}

class Upload extends BaseController
{
    public function index()
    {
        // 获取上传的文件
        $files = request()->file('file');

        // 检查是否有文件上传
        if (empty($files)) {
            return json([
                "code" => 400,
                "msg" => "请选择文件",
                "data" => null,
            ]);
        }

        // 获取当前脚本所在的目录
        $currentDir = __DIR__;
        
        // 指定文件保存的目录
        $uploadDir = $currentDir . DS . '..' . DS . '..' . DS . 'public' . DS . 'uploads';

        // 保存文件并获取保存的文件名
        $fileNames = [];
        foreach ($files as $file) {
            // 使用原始文件名保存文件
            $info = $file->move($uploadDir, true);
        
            if ($info) {
                $fileNames[] = $info->getSaveName();
            } else {
                return json([
                    "code" => 500,
                    "msg" => $file->getError() ?: '文件上传失败',
                    "data" => null,
                ]);
            }
        }

        return json([
            "code" => 200,
            "msg" => "文件上传成功",
            "data" => $fileNames,
        ]);
    }
}
