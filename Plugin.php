<?php
/**
* 网易云存储插件
*
* @package Nos
* @author 白宦成
* @version 1.0.0
* @link http://c.163.com
*/
require_once 'nos-php-sdk-1.0.0/autoload.php';

class Nos_Plugin implements Typecho_Plugin_Interface{
    
    /* 激活插件方法 */
    public static function activate(){
        Typecho_Plugin::factory('Widget_Upload')->uploadHandle = array('Nos_Plugin', 'uploadHandle');
        Typecho_Plugin::factory('Widget_Upload')->modifyHandle = array('Nos_Plugin', 'modifyHandle');
        Typecho_Plugin::factory('Widget_Upload')->deleteHandle = array('Nos_Plugin', 'deleteHandle');
        Typecho_Plugin::factory('Widget_Upload')->attachmentHandle = array('Nos_Plugin', 'attachmentHandle');
        return _t('插件已经激活，需先配置网易云存储的信息！');
    }
    
    /* 禁用插件方法 */
    public static function deactivate(){
        return _t('网易云存储插件已被禁用，附件将使用您的网站空间存储！');
    }
    
    /* 插件配置方法 */
    public static function config(Typecho_Widget_Helper_Form $form){
        
        $endPoint = new Typecho_Widget_Helper_Form_Element_Text('endPoint', null, 'nos-eastchina1.126.net', _t('endPoint:'));
        $form->addInput($endPoint->addRule('required', _t('“endPoint”不能为空！')));
        
        $bucket = new Typecho_Widget_Helper_Form_Element_Text('bucket', null, null, _t('Bucket名称：'));
        $form->addInput($bucket->addRule('required', _t('“Bucket名称”不能为空！')));
        
        $accessKeyId = new Typecho_Widget_Helper_Form_Element_Text('accessKeyId', null, null, _t('Access Key：'));
        $form->addInput($accessKeyId->addRule('required', _t('“Access Key”不能为空！')));
        
        $accessKeySecret = new Typecho_Widget_Helper_Form_Element_Text('accessKeySecret', null, null, _t('Access Secret：'));
        $form->addInput($accessKeySecret->addRule('required', _t('“Access Secret”不能为空！')));
        
        $savepath = new Typecho_Widget_Helper_Form_Element_Text('savepath', null, null, _t('保存路径'));
        $form->addInput($savepath->addRule('required', _t('“保存路径”不能为空！')));
    }
    
    /* 个人用户的配置方法 */
    public static function personalConfig(Typecho_Widget_Helper_Form $form){
        
    }

    /* 获取参数  */
    public static function getConfig()
    {
        return Typecho_Widget::widget('Widget_Options')->plugin('Nos');
    }

    /* 初始化NOS示例 */
    public static function  get_instance($accessKeyId,$accessKeySecret,$endPoint){
        $ins=new \NOS\NosClient($accessKeyId,$accessKeySecret,$endPoint);
        return $ins;
    }
    
    /* 上传文件 */
    public static function uploadFile($file,$content=null){
        $option =self::getConfig();
        $ins =self::get_instance($option->accessKeyId,$option->accessKeySecret,$option->endPoint);

        // 获取上传文件
        if (empty($file['name'])) return false;
        // 校验扩展名
        $part = explode('.', $file['name']);
        $ext = (($length = count($part)) > 1) ? strtolower($part[$length-1]) : '';
        if (!Widget_Upload::checkFileType($ext)) return false;
        // 计算上传的文件名
        $date = new Typecho_Date(Typecho_Widget::widget('Widget_Options')->gmtTime);
        $savepath = preg_replace(array('/\{year\}/', '/\{month\}/', '/\{day\}/'), array($date->year, $date->month, $date->day), $option->savepath);
        $savename = $savepath . sprintf('%u', crc32(uniqid())) . '.' . $ext;
        // 判断是否为更新文件
        if (isset($content))
        {
            $savename = $content['attachment']->path;
            $ins->deleteObject($option->bucket,$content['attachment']->path);
        }
        
        // 上传文件
        $filename = $file['tmp_name'];
        if (!isset($filename)) return false;
        
        try{
           $ins->uploadFile($option->bucket,$savename, $filename);
            
        } catch(NosException $e) {
            return false;
        }
        
        return array
        (
        'name'  =>  $file['name'],
        'path'  =>  $savename,
        'size'  =>  $file['size'],
        'type'  =>  $ext,
        'mime'  =>  Typecho_Common::mimeContentType($savename)
        );
        
        
    }
    
    // 上传文件处理函数
    public static function uploadHandle($file)
    {
        return self::uploadFile($file);
    }

    // 修改文件处理函数
    public static function modifyHandle($content, $file)
    {
        return self::uploadFile($file, $content);
    }

    // 删除文件
    public static function deleteHandle(array $content)
    {
        $option = self::getConfig();
        $ins =self::get_instance($option->accessKeyId,$option->accessKeySecret,$option->endPoint);
        
        try{
          $ins->deleteObject($option->bucket,$content['attachment']->path);
        } catch(\NOS\NosException $e) {
            return false;
        }
    }

    // 获取实际文件绝对访问路径
    public static function attachmentHandle(array $content)
    {
        $option = self::getConfig();
        return Typecho_Common::url($content['attachment']->path, $option->bucket.'.'.$option->endPoint);
    }
    
}