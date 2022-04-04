<?php
/**
 * Talents come from diligence, and knowledge is gained by accumulation.
 *
 * @author: 晋<657306123@qq.com>
 */

namespace Xin\Contracts\Uploader;

interface Factory
{
    /**
     * @param string $scene
     * @return \Xin\Contracts\Uploader\Uploader
     */
    public function uploader($scene = null);
}