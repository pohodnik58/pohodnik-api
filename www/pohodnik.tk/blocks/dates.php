<?


	function smartDate($time, $show_time = true){
	
	$mo = array(1=>"января",2=>"февраля",3=>"марта",
							4=>"апреля",5=>"мая",6=>"июня",
							7=>"июля",8=>"августа",9=>"сентября",
							10=>"октября",11=>"ноября",12=>"декабря");
	
		if(date('YmdH', $time)===date('YmdH')){return $show_time?date('H:i', $time):'Сегодня';}
		else if(date('Ymd', $time)===date('Ymd')){return $show_time?"Сегодня в ".date('H:i', $time):"Сегодня";}
		else if(date('Ymd', $time)===date('Ymd', time()-86400)){return $show_time?"Вчера в ".date('H:i', $time):"Вчера";}
		else if(date('Ymd', $time)===date('Ymd', time()+86400)){
			return $show_time?"Завтра в ".date('H:i', $time):"Завтра";}
		else if(date('Y', $time)===date('Y')){
		$month = $mo[(date('n', $time))];
			return $show_time? date('d', $time)." ".$month." в ".date('H:i', $time):date('d', $time)." ".$month;}
		else { return $show_time?date('d.m.Y в H:i', $time):date('d.m.Y', $time); }
	}
?>