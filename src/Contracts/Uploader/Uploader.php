<?php
/**
 * Talents come from diligence, and knowledge is gained by accumulation.
 *
 * @author: 晋<657306123@qq.com>
 */

namespace Xin\Contracts\Uploader;

interface Uploader
{
    /**
     * 上传文件
     * @param string $scene
     * @param \SplFileInfo $file
     * @param array $options
     * @return array
     */
    public function file($scene, \SplFileInfo $file, array $options = []);

    /**
     * 获取上传令牌
     * @param string $scene
     * @param string $filename
     * @param array $options
     * @return array
     */
    public function token($scene, $filename, array $options = []);

}