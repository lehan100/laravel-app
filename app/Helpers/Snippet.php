<?php
namespace App\Helpers;
class Snippet {
	protected $SCHEMA;
	protected $SCHEMADATA;
	public function __construct()
    {
		$this->SCHEMA .= '<script type="application/ld+json">';	
		$this->SCHEMADATA = [];
    }
	public function setSchema($key, $val){
		$this->SCHEMADATA[$key] = $val;
		return $this;
	}
	public function searchSchema(){
		$potentialAction = [
			'@type'=>'SearchAction',
			'target'=>[
				'@type'=>'EntryPoint',
				'urlTemplate'=>'https://ukimua.com/product/search/{keyword}',
			],
			'query-input'=>'required name=keyword'
		];
		$this->setSchema("@context","https://schema.org")
		->setSchema("@type","WebSite")
		->setSchema("url","https://www.ukimua.com/")
		->setSchema("potentialAction",$potentialAction)
		;
		return $this;
	}
	public function getSchemaData(){
		return $this->SCHEMADATA;
	}
	public function endScript(){
		$this->SCHEMA .= '</script>';
	}
}