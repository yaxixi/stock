<?php
//折线图
/*e.g.
	$cate=array('a','b');
	$data[]=array(
		'name'='pv',
		'data'=array(
			'a'=>'123',
			'b'=>'456'
		);
*/
class libDataView {
	private $_options = array(
			'chart' => array(
					'showFCMenuItem' => "0", 
					'lineThickness' => "2", 
					'showValues' => "0", 
					'anchorRadius' => "4", 
					'divLineAlpha' => "20", 
					'divLineColor' => "CC3300", 
					'divLineIsDashed' => "1", 
					'showAlternateHGridColor' => "1", 
					'alternateHGridAlpha' => "5", 
					'alternateHGridColor' => "CC3300", 
					'shadowAlpha' => "40", 
					'labelStep' => "2", 
					'numvdivlines' => "28", 
					'showAlternateVGridColor' => "1", 
					'chartsshowShadow' => "1", 
					'chartRightMargin' => "20", 
					'chartTopMargin' => "15", 
					'chartLeftMargin' => "0", 
					'chartBottomMargin' => "3", 
					'bgColor' => "DEE3F7", 
					'canvasBorderThickness' => "1", 
					'showBorder' => "0", 
					'legendBorderAlpha' => "0", 
					'bgAngle' => "360", 
					'showlegend' => "1", 
					'borderColor' => "DEF3F3", 
					'toolTipBorderColor' => "cccc99", 
					'canvasPadding' => "0", 
					'toolTipBgColor' => "ffffcc", 
					'legendShadow' => "0", 
					'baseFontSize' => "12", 
					'canvasBorderAlpha' => "20", 
					'outCnvbaseFontSize' => "10", 
					'outCnvbaseFontColor' => "000000", 
					'numberScaleValue' => "10000,1,1,1000", 
					'formatNumberScale' => "1", 
					'palette' => "2", 
					'numberScaleUnit' => " , ,w,kw", 
					'lineColor' => "AFD8F8",
					'outCnvBaseFontSize'=>'12'
			), 
			'definition' => array(
					'style' => array(
							array(
									'name' => 'CaptionFont', 
									'type' => 'font', 
									'size' => 12
							), 
							array(
									'name' => 'CaptionFont', 
									'type' => 'font', 
									'size' => 12
							)
					)
			), 
			'application' => array(
					'apply' => array(
							array(
									'toObject' => 'CAPTION', 
									'styles' => 'CaptionFont'
							), 
							array(
									'toObject' => 'SUBCAPTION', 
									'styles' => 'CaptionFont'
							), 
							array(
									'toObject' => 'Legend', 
									'styles' => 'myLegendFont'
							)
					)
			)
	);
	
	private $_categories = array();
	private $_data = array();
	
	public function setCategories($categories) {
		$this->_categories = $categories;
	}
	
	public function setData($data) {
		$this->_data = $data;
	}
	
	public function addData($data, $multi = FALSE) {
		if ($multi) {
			$this->_data = array_merge($this->_data, $data);
		} else {
			$this->_data[] = $data;
		}
	}
	public function style($key,$val,$type='chart'){
		$this->_options[$type][$key]=$val;
	}
	public function display($charset = 'UTF-8') {
		$chart = "";
		foreach ( $this->_options['chart'] as $k => $v ) {
			$v = htmlspecialchars($v);
			$chart .= " {$k}=\"{$v}\"";
		}
		$categories = array();
		foreach ( $this->_categories as $k => $v ) {
			$v = htmlspecialchars($v);
			$categories[] = "<category label=\"{$v}\" />";
		}
		$categories = implode("", $categories);
		
		$dataset = array();
		
		$colors = array(
				'0033CC', 
				'00CC33', 
				'CC3300', 
				'AAAAAA', 
				'FF9900', 
				'00FF99', 
				'9900FF'
		);
		$len = count($colors);
		$i = -1;
		
		foreach ( $this->_data as $k => $v ) {
			$i++;
			$tmp['seriesName'] = isset($v['name']) ? $v['name'] : $k;
			$tmp['color'] = $colors[$i % $len];
			$tmp['anchorBorderColor'] = $colors[$i % $len];
			$tmp['data'] = array();
			$data = $v['data'];
			foreach ( $this->_categories as $ck ) {
				if (is_array($data[$ck])) {
					$value = $data[$ck]['value'];
					$desc = $data[$ck]['desc'];
				} else {
					$value = $data[$ck];
					$desc = '';
				}
				if (isset($data[$ck])) {
					$tmp['data'][] = "<set value=\"{$value}\" toolText=\"{$tmp['seriesName']} : {$value}   {$ck}  {$desc}\" />";
				} else {
					$tmp['data'][] = "<set value=\"\" toolText=\"\" />";
				}
			}
			$tmp['data'] = implode("", $tmp['data']);
			$dataset[] = "<dataset seriesName=\"{$tmp["seriesName"]}\" color=\"{$tmp["color"]}\" anchorBorderColor=\"{$tmp["anchorBorderColor"]}\" >{$tmp['data']}</dataset>";
		}
		
		$dataset = implode("", $dataset);
		
		$definition = array();
		foreach ( $this->_options['definition'] as $k => $v ) {
			foreach ( $v as $vk => $vv ) {
				foreach ( $vv as $vvk => $vvv ) {
					$vvv = htmlspecialchars($vvv);
					$vv[$vvk] = "{$vvk}=\"{$vvv}\"";
				}
				$definition[] = "<{$k} " . implode(" ", $vv) . " />";
			}
		}
		$definition = "<definition>" . implode("", $definition) . "</definition>";
		
		$application = array();
		foreach ( $this->_options['application'] as $k => $v ) {
			foreach ( $v as $vk => $vv ) {
				foreach ( $vv as $vvk => $vvv ) {
					$vvv = htmlspecialchars($vvv);
					$vv[$vvk] = "{$vvk}=\"{$vvv}\"";
				}
				$application[] = "<{$k} " . implode(" ", $vv) . " />";
			}
		}
		$application = "<application>" . implode("", $application) . "</application>";
		
		$str = "<chart{$chart}><categories>{$categories}</categories>{$dataset}<styles></styles>{$definition}{$application}</chart>";
		$str = mb_convert_encoding($str,'gbk','utf-8');
		$str = str_replace("><", ">\n<", $str);
		return $str;
	}
}

//柱状图
class ViewColumn2D{
	private $_options=array(
		'chart'=>array(
			'showValues'=>"1",
			'decimals'=>"0",
			'baseFontSize'=>"14",
			'formatNumberScale'=>"0",
			'bgColor' => "DEE3F7",
			'canvasBorderThickness'=>'0',
			'useRoundEdges'=>"1"
			)
	);
	private $_data=array();
	public function style($key,$value){
		$this->_options['chart'][$key]=$value;
	}
	public function setData($data){
		$this->_data=$data;
	}
	public function display(){
		$chart="";
		foreach ($this->_options['chart'] as $key => $val){
			$chart .= " $key='$val'";
		}
		$chart = "<chart ".$chart.">";
		foreach ($this->_data as $key => $val){
			$dataset .= "<set label='$key' value='$val' />";
		}
		$str = $chart.$dataset."</chart>";
		$str = str_replace("><", ">\n<", $str);
		echo $str;
	}
}

//堆栈图
class ViewStCol2D{
	private $_options=array(
		'chart'=>array(
			'palette'=>"2",
			'showValues'=>"0",
			'decimals'=>"0",
			'shownames'=>"1",
			'showvalues'=>"0",
			'showSum'=>"1",
			'useRoundEdges'=>"1"
			)
	);
	private $_categories=array();
	private $_data=array();
	private $_color=array(
		'AFD8F8',
		'F6BD0F',
		'8BBA00',
		'0033CC'
		);
	public function style($key,$val,$type='chart'){
		$this->_options[$type][$key]=$val;
	}
	public function setCategories($cate){
		$this->_categories=$cate;
	}
	public function setData($data){
		$this->_data=$data;
	}
	public function display(){
		$chart="";
		foreach ($this->_options['chart'] as $key => $val){
			$chart .= " $key='$val'";
		}
		$chart = "<chart ".$chart.">";
		foreach ($this->_categories as $val){
			$val = htmlspecialchars($val);
			$cate_str .= "<category label='$val'/>";
		}
		$categories = "<categories>$cate_str</categories>";
		$dataset="";
		foreach ($this->_data as $key => $val){			
			$key=htmlspecialchars($key);
			$set="";
			foreach ($val as $k => $v){
				$v=htmlspecialchars($v);
				$set .= "<set value='$v' />";
			}
			$color=current($this->_color);
			next($this->_color);
			$dataset .= "<dataset seriesName='$key' color='$color' >".$set."</dataset>";
		}
		$str = $chart.$categories.$dataset."</chart>";
		$str = str_replace("><", ">\n<", $str);
		echo $str;
	}
}

//3D饼图
class ViewPie3D{
	private $_options=array(
		'palette'=>"4",
		'decimals'=>"0",
		'enableSmartLabels'=>"1",
		'enableRotation'=>"0",
		'bgColor'=>"DEE3F7",
		'bgAlpha'=>"40,100",
		'bgRatio'=>"0,100",
		'bgAngle'=>"360",
		'howBorder'=>"1",
		'startingAngle'=>"70",
		'baseFontSize'=>"14"
		);
		
	private $_data=array();
	private $_slice=array();
	public function style($key,$value){
		$this->_options[$key]=$value;
	}
	public function setData($data){
		$this->_data=$data;
	}
	public function Slice($array){
		foreach ($array as $val){
			$this->_slice[$val]=1;
		}
	}
	public function display(){
		if (count($this->_slice) == 0) {
			$sum=array_sum($this->_data);
			foreach ($this->_data as $key => $val){
				if ($val/$sum < 0.1) {
					$this->_slice[$key]=1;
				}
			}
		}
		$chart="";
		foreach ($this->_options as $key => $val){
			$chart .= " $key='$val'";
		}
		$chart = "<chart ".$chart.">";
		foreach ($this->_data as $key => $val){
			$dataset .= "<set label='$key' value='$val' isSliced='{$this->_slice[$key]}'/>";
		}
		$str = $chart.$dataset."</chart>";
		$str = str_replace("><", ">\n<", $str);
		echo $str;
	}
}

//3D柱状图+折线图
/*	e.g.
	$cate=array('a','b');
	$data=array(
		'colunm'=>array(
			'style'=>array(
				'color'=>"0033cc",
				'showValues'=>"0",
				'parentYAxis'=>"S"
				),
			'data'=>array(
			'a'=>'11',
			'b'=>'22')
			),
		'line'=>array(
			'style'=>array(
				'color'=>"AFD8F8",
				'showValues'=>"1"
				),
			'data'=>array(
				'a'=>'77',
				'b'=>'88'
				)
			)
		);*/
class ViewCol3DLineDY{

	private $_options=array(
			'palette'=>"4",
			'shownames'=>"1",
			'showvalues'=>"0",
			'sYAxisValuesDecimals'=>"2",
			'connectNullData'=>"0",
			'numDivLines'=>"4",
			'formatNumberScale'=>"0",
			'bgColor'=>"DEE3F7",
			'baseFontSize'=>"14"
		);
	private $_data=array();
	private $_cate=array();
	public function style($key,$value){
		$this->_options[$key]=$value;
	}
	public function setCategories($cate){
		$this->_cate = $cate;
	}
	public function setData($data){
		$this->_data=$data;
	}
	public function display(){
		foreach ($this->_options as $key => $val){
			$chart .= "$key='$val' ";
		}
		$chart = "<chart ".$chart.">";
		foreach ($this->_cate as $key => $val){
			$cate .= "<category label='$val'/>";
		}
		$categories = "<categories>".$cate."</categories>";
		foreach ($this->_data as $series => $value){
			$style="seriesName='$series' ";
			foreach ($value['style'] as $key => $val){
				$style .= "$key='$val' ";
			}
			$dataset="";
			foreach ($value['data'] as $key => $val){
				$dataset .= "<set value='$val'/>";
			}
			$datasets .= "<dataset $style>".$dataset."</dataset>";
		}
		$str = $chart.$categories.$datasets."</chart>";
		$str = str_replace("><", ">\n<", $str);
		echo $str;
	}
}
?>
