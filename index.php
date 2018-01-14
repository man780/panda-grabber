<?
	require('curl/curl.php');
	require('phpquery/phpQuery/phpQuery.php');
	
	function marketCapUp($param){
		$market_cap_td_arr = explode('<br>', $param);
		$param = $market_cap_td_arr[0];
		if(trim($param) == '-'){
			return 0;
		}
		$param = str_replace("$","",trim($param));
		$result = str_replace(",","",$param)*1;
		return $result;
	}
	
	$pdo = new PDO("mysql:host=localhost;dbname=panda_grabber", 'root', '');

	$curl = new Curl();
	$response = $curl->get('https://whattomine.com/coins?utf8=%E2%9C%93&adapt_q_280x=0&adapt_q_380=0&adapt_q_fury=0&adapt_q_470=0&adapt_q_480=0&adapt_q_570=0&adapt_q_580=0&adapt_q_vega56=0&adapt_q_vega64=0&adapt_q_750Ti=0&adapt_q_1050Ti=0&adapt_q_10606=6&adapt_10606=true&adapt_q_1070=0&adapt_q_1080=0&adapt_q_1080Ti=0&eth=true&factor%5Beth_hr%5D=135.0&factor%5Beth_p%5D=540.0&grof=true&factor%5Bgro_hr%5D=123.0&factor%5Bgro_p%5D=540.0&x11gf=true&factor%5Bx11g_hr%5D=43.2&factor%5Bx11g_p%5D=540.0&cn=true&factor%5Bcn_hr%5D=2580.0&factor%5Bcn_p%5D=420.0&eq=true&factor%5Beq_hr%5D=1620.0&factor%5Beq_p%5D=540.0&lre=true&factor%5Blrev2_hr%5D=121800.0&factor%5Blrev2_p%5D=540.0&ns=true&factor%5Bns_hr%5D=3000.0&factor%5Bns_p%5D=540.0&lbry=true&factor%5Blbry_hr%5D=1020.0&factor%5Blbry_p%5D=540.0&bk2bf=true&factor%5Bbk2b_hr%5D=5940.0&factor%5Bbk2b_p%5D=480.0&bk14=true&factor%5Bbk14_hr%5D=9300.0&factor%5Bbk14_p%5D=540.0&pas=true&factor%5Bpas_hr%5D=3480.0&factor%5Bpas_p%5D=540.0&skh=true&factor%5Bskh_hr%5D=108.0&factor%5Bskh_p%5D=540.0&factor%5Bl2z_hr%5D=420.0&factor%5Bl2z_p%5D=300.0&factor%5Bcost%5D=0.1&sort=Profitability24&volume=0&revenue=24h&factor%5Bexchanges%5D%5B%5D=&factor%5Bexchanges%5D%5B%5D=abucoins&factor%5Bexchanges%5D%5B%5D=bitfinex&factor%5Bexchanges%5D%5B%5D=bittrex&factor%5Bexchanges%5D%5B%5D=bleutrade&factor%5Bexchanges%5D%5B%5D=cryptopia&factor%5Bexchanges%5D%5B%5D=hitbtc&factor%5Bexchanges%5D%5B%5D=poloniex&factor%5Bexchanges%5D%5B%5D=yobit&dataset=Main&commit=Calculate');
		
	$doc = phpQuery::newDocument($response->body);
	$trs = $doc->find('table:eq(0) tbody tr');
	
	// set the PDO error mode to exception
	$pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

	// prepare sql and bind parameters
	$stmt = $pdo->prepare("INSERT INTO `coins` (`name`, `rev24up`, `rev24down`, `nethash`, `exrate`, `market_cap_up`, `market_cap_down`, `queue`, `time`) 
					VALUE (:name, :rev24up, :rev24down, :nethash, :exrate, :market_cap_up, :market_cap_down, :queue, :time)");
	$stmt->bindParam(':name', $name);
	$stmt->bindParam(':rev24up', $rev24up);
	$stmt->bindParam(':rev24down', $rev24down);
	$stmt->bindParam(':nethash', $nethash);
	$stmt->bindParam(':exrate', $exrate);
	$stmt->bindParam(':market_cap_up', $market_cap_up);
	$stmt->bindParam(':market_cap_down', $market_cap_down);
	$stmt->bindParam(':queue', $queue);
	$stmt->bindParam(':time', $time);
	
	foreach($trs as $k => $tr){
		if($k == 7) break;
	
		$tr = pq($tr);
		
		// insert a row
		$first_td = $tr->find('td:eq(0)');
		
		$rev_td = $tr->find('td:eq(6)')->text();
		while ( strpos($rev_td,'  ')!==false )
		{
			$rev_td = str_replace('  ',' ',$rev_td);
		};
		$rev_td_arr = explode(' ', trim($rev_td));
		
		$net_hash_td = $tr->find('td:eq(2)>div>div.small_text')->html();
		$net_hash_arr = explode('<br>', $net_hash_td);
		
		$exrate_td = $tr->find('td:eq(4)>div>div.small_text')->html();
		
		$market_cap_td = $tr->find('td:eq(5)')->html();
		
		$market_cap_td_down = $tr->find('td:eq(5) strong')->html();
		
		$name = trim($first_td->find('div:eq(1)')->text());
		$rev24up = $rev_td_arr[0]*1000;
		$rev24down = $rev_td_arr[1]*1000;
		$nethash = trim($net_hash_arr[1])*1;
		$exrate = trim($exrate_td)*1;
		$market_cap_up = marketCapUp($market_cap_td);
		$market_cap_down = trim($market_cap_td_down)*1;
		$queue = date('ymdHi')*1;
		$time = time();
		$stmt->execute();
		
	}
	echo "Сработала!";
$pdo = null;	
?>