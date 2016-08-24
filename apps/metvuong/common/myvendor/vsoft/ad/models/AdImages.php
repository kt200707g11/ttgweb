<?php

namespace vsoft\ad\models;

use Yii;
use yii\helpers\Url;
use vsoft\express\components\StringHelper;
use common\models\AdImages as AI;
use vsoft\express\components\AdImageHelper;

class AdImages extends AI
{
	const SIZE_THUMB = 'thumb';
	const SIZE_MEDIUM = 'medium';
	const SIZE_LARGE = 'large';
	
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'product_id' => 'Product ID',
            'file_name' => 'File Name',
            'uploaded_at' => 'Uploaded At',
        ];
    }
    
    public function getUrl($size = self::SIZE_MEDIUM) {
    	return self::getImageUrl($this->folder, $this->file_name, $size);
    }
    
    public static function getImageUrl($folder, $fileName, $size = self::SIZE_MEDIUM) {
    	if($folder) {
    		$sizeFolder = AdImageHelper::makeFolderName(AdImageHelper::$sizes[$size]);
    		 
    		return "/store/$folder/$sizeFolder/$fileName";
    	} else {
    		$defaultSize = '745x510';
    		
    		if($size == 'thumb') {
    			$s = '350x280';
    		} else {
    			$s = $defaultSize;
    		}
    		
    		return str_replace($defaultSize, $s, $fileName);
    	}
    }
    
    public static function defaultImage() {
    	return '/images/default-ads.jpg';
    }
    
    public function afterDelete() {
    	$original = \Yii::getAlias('@store') . DIRECTORY_SEPARATOR . $this->folder . DIRECTORY_SEPARATOR . $this->file_name;
    	unlink($original);
    	
    	foreach(AdImageHelper::$sizes as $size) {
    		unlink(\Yii::getAlias('@store') . DIRECTORY_SEPARATOR . $this->folder . DIRECTORY_SEPARATOR . AdImageHelper::makeFolderName($size) . DIRECTORY_SEPARATOR . $this->file_name);
    	}
    }
}


