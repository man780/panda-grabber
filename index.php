<?
	require('curl/curl.php');
	require('phpquery/phpQuery/phpQuery.php');
	
	$pdo = new PDO("mysql:host=localhost;dbname=panda_grabber", 'root', '');

	$curl = new Curl();
	$response = $curl->get('https://whattomine.com/coins?utf8=%E2%9C%93&adapt_q_280x=0&adapt_q_380=0&adapt_q_fury=0&adapt_q_470=0&adapt_q_480=0&adapt_q_570=0&adapt_q_580=0&adapt_q_vega56=0&adapt_q_vega64=0&adapt_q_750Ti=0&adapt_q_1050Ti=0&adapt_q_10606=6&adapt_10606=true&adapt_q_1070=0&adapt_q_1080=0&adapt_q_1080Ti=0&eth=true&factor%5Beth_hr%5D=135.0&factor%5Beth_p%5D=540.0&grof=true&factor%5Bgro_hr%5D=123.0&factor%5Bgro_p%5D=540.0&x11gf=true&factor%5Bx11g_hr%5D=43.2&factor%5Bx11g_p%5D=540.0&cn=true&factor%5Bcn_hr%5D=2580.0&factor%5Bcn_p%5D=420.0&eq=true&factor%5Beq_hr%5D=1620.0&factor%5Beq_p%5D=540.0&lre=true&factor%5Blrev2_hr%5D=121800.0&factor%5Blrev2_p%5D=540.0&ns=true&factor%5Bns_hr%5D=3000.0&factor%5Bns_p%5D=540.0&lbry=true&factor%5Blbry_hr%5D=1020.0&factor%5Blbry_p%5D=540.0&bk2bf=true&factor%5Bbk2b_hr%5D=5940.0&factor%5Bbk2b_p%5D=480.0&bk14=true&factor%5Bbk14_hr%5D=9300.0&factor%5Bbk14_p%5D=540.0&pas=true&factor%5Bpas_hr%5D=3480.0&factor%5Bpas_p%5D=540.0&skh=true&factor%5Bskh_hr%5D=108.0&factor%5Bskh_p%5D=540.0&factor%5Bl2z_hr%5D=420.0&factor%5Bl2z_p%5D=300.0&factor%5Bcost%5D=0.1&sort=Profitability24&volume=0&revenue=24h&factor%5Bexchanges%5D%5B%5D=&factor%5Bexchanges%5D%5B%5D=abucoins&factor%5Bexchanges%5D%5B%5D=bitfinex&factor%5Bexchanges%5D%5B%5D=bittrex&factor%5Bexchanges%5D%5B%5D=bleutrade&factor%5Bexchanges%5D%5B%5D=cryptopia&factor%5Bexchanges%5D%5B%5D=hitbtc&factor%5Bexchanges%5D%5B%5D=poloniex&factor%5Bexchanges%5D%5B%5D=yobit&dataset=Main&commit=Calculate');
		
	$doc = phpQuery::newDocument($response->body);
	$trs = $doc->find($doc->find('table table-hover table-vcenter tbody tr'));
	
	foreach($trs as $tr){
		$pq = pq($tr);
		$tds = $pq->find('td');
		foreach($tds as $key => $td){
			echo trim(pq($td)->text());
			//echo " ";
		}
		$params = array(':name' => 1, ':rev24up' => 2, ':rev24down' => 3, ':nethash' => 4, ':exrate' => 4, ':market_cap_up' => 4, ':market_cap_down' => 4, ':queue' => 4, ':time' => 4);
 
		$pdo->prepare('INSERT INTO `coins` (`name`, `rev24up`, `rev24down`, `nethash`, `exrate`, `market_cap_up`, `market_cap_down`, `queue`, `time`) 
						VALUE (:name, :rev24up, :rev24down, :nethash, :exrate, :market_cap_up, :market_cap_down, :queue, :time`)');
		 
		$pdo->execute($params);
		
		echo "<br>";
		
	}
?>