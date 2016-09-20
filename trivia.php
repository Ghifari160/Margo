<?php
require_once "core.php";

$trivia = array(
	'df551f9f' => array(
		'5b20f762' => array(
			'q' => "The following is an excerpt from a book.\n<blockquote>"
				."\nHarry — yer a wizard.\n</blockquote>\nWhat is the title of"
				." this book?",
			'a' => array(
				'844c1b18' => '<i>Harry Potter and the Sorcerer\'s Stone</i>',
				'6375b72d' => '<i>Harry Potter and the Half Blood Prince</i>',
				'353e79d2' => '<i>Harry Potter and the Order of the Phoenix</i>',
				'71bab367' => '<i>Nightfall</i>'
			)
		),
		'1d49a5de' => array(
			'q' => "The following is an excerpt from <i>Harry Potter and the"
				." Sorcerer's Stone</i>\n<blockquote>\nHarry — yer a wizard.\n"
				."</blockquote>\nWho is the author of this book?",
			'a' => array(
				'b7dc514e' => "J. K. Rowling",
				'3ab1b5bf' => "Jake Halpern",
				'f42db9cd' => "Harper Lee",
				'7072b087' => "William Golding"
			)
		),
		'1e2b1343' => array(
			'q' => "The following is an excerpt from <i>Harry Potter and the"
				." Sorcerer's Stone</i> by J. K. Rowling\n<blockquote>\n"
				."Harry — yer a wizard.\n</blockquote>\nWhich character from"
				." the book uttered those words to Harry?",
			'a' => array(
				'05ddebeb' => 'Hagrid',
				'db98dc0a' => 'Dumbledore',
				'c344f092' => 'Voldemort',
				'416fcb92' => 'Snape'
			)
		)
	),
	'6b5696c9' => array(
		'6b25500c' => array(
			'q' => "The following is an excerpt\n<blockquote>\n<b>THE HOUSES MUST"
				." BE WITHOUT STAIN.<br>LEAVE THEM AS THEY WERE.<br>COVER YOUR"
				." SCENT.<br>FLEE THE NIGHT OR WE WILL COME FOR YOU.</b>\n"
				."</blockquote>\nWhat is the title of the book?",
			'a' => array(
				'9d18c022' => '<i>Nightfall</i>',
				'279fd9c7' => '<i>Dawnfall</i>',
				'c0cec434' => '<i>Twilightfall</i>',
				'bf24ac61' => '<i>Noonfall</i>'
			)
		),
		'4619b322' => array(
			'q' => "The following is an excerpt from <i>Nightfall</i>\n"
				."<blockquote>\n<b>THE HOUSES MUST BE WITHOUT STAIN.<br>LEAVE"
				." THEM AS THEY WERE.<br>COVER YOUR SCENT.<br>FLEE THE NIGHT OR"
				." WE WILL COME FOR YOU.</b>\n</blockquote>\nWho are the"
				." authors of <i>Nightfall</i>?",
			'a' => array(
				'495fcb2d' => "Jake Halpern and Peter Kujawinski",
				'd91ccd77' => "J.K Rowling and Marieke Nijkamp",
				'ec233d66' => "Charles Dickens and William Golding",
				'e57724f3' => "Paula Hawkins and Ruth Ware"
			)
		),
		'57e35aaf' => array(
			'q' => "The following is an excerpt from <i>Nightfall</i> by J."
				." Halpern and P. Kujawinski.\n<blockquote>\n<b>THE HOUSES MUST"
				." BE WITHOUT STAIN.<br>LEAVE THEM AS THEY WERE.<br>COVER YOUR"
				." SCENT.<br>FLEE THE NIGHT OR WE WILL COME FOR YOU.</b>\n"
				."</blockquote>\nThe three main characters of the book saw"
				." those words carved into the shield of the statue of a sea"
				." hag while fleeing from a creature. The creature and their"
				." kind built the town. What is the name of the town?",
			'a' => array(
				'2ddf9d01' => "Bliss",
				'1959cfad' => "Desert Lands",
				'6a29d90f' => "Northa",
				'80a3f2c1' => "Aurora"
			)
		)
	)
);

function trivia_getSession()
{
	if(isset($_SESSION['mgame_trivia_session']))
	{
		return $_SESSION['mgame_trivia_session'];
	}
	else
	{
		$session_id = genid();
		$_SESSION['mgame_trivia_session'] = $session_id;

		return $session_id;
	}
}

function trivia_getState($state)
{
	if(isset($_SESSION['mgame_trivia_'.$state]))
	{
		$json = base64_decode($_SESSION['mgame_trivia_'.$state]);
		$rJson = json_decode($json, true);

		return $rJson;
	}
	else
		return false;
}

function trivia_createState($rJson, $prevState = "{N.A}")
{
	$state_id = genid();
	$json = json_encode($rJson);

	$_SESSION['mgame_trivia_'.$state_id] = base64_encode($json);

	if($prevState !== "{N.A}")
		unset($_SESSION['mgame_trivia_'.$prevState]);

	return $state_id;
}

function trivia_reset()
{
	unset($_SESSION['mgame_trivia_session']);
}

function trivia_err($param)
{
	header('location: /e?p='.($param + 6).'');
}

$path = getPath();

$pat = "<form action=\"%s\" method=\"POST\">\n";
$pat .= "<div class=\"trivia\">\n";
$pat .= "<div class=\"question\">\n%s\n</div>\n";
$pat .= "<div class=\"answers\" id=\"%s\">\n%s";
$pat .= "<input type=\"hidden\" name=\"%s\">\n";
$pat .= "</div>\n";
$pat .= "<div class=\"submit\">\n";
$pat .= "<div class=\"m-btn hidden\">Next Question</div>\n";
$pat .= "</div>\n";
$pat .= "</div>\n";
$pat .= "</form>\n";

$apat = "<div class=\"answer\" id=\"%s\">\n";
$apat .= "<div class=\"radio\"><div class=\"outer-circle\">";
$apat .= "<div class=\"inner-circle\"></div></div></div>\n";
$apat .= "<div class=\"label\">\n%s\n</div>\n";
$apat .= "</div>\n";

$upat = "/trivia/%s/%s";

if($path == "/trivia" || $path == "/trivia/")
	header('location: /e');
else
{
	$raw = substr($path, 8);

	if($raw == "new")
	{
		$session = trivia_getSession();

		// Generate Section Order
		$sections = array();
		foreach($trivia as $k=>$v)
		{
			array_push($sections, $k);
		}
		shuffle($sections);

		// Get Question Orders
		$qs = array();
		foreach($trivia[$sections[0]] as $k=>$v)
		{
			array_push($qs, $k);
		}

		$qid = $qs[0];

		// State
		$state = array(
			'section_orders' => $sections,
			'section_id' => $sections[0],
			'questions' => $qs,
			'question' => array(
				'id' => $qid,
				'ca_id' => key($trivia[$sections[0]][$qid]['a']),
				'text' => $trivia[$sections[0]][$qid]['q'],
				'answers' => shuffle_assoc($trivia[$sections[0]][$qid]['a'])
			)
		);

		// Store state
		$state_id = trivia_createState($state);

		// Trivia Interface
		$url = sprintf($upat, $session, $state_id);
		$answers = "";
		$trivia_interface = "";

		foreach($state['question']['answers'] as $k=>$v)
		{
			$answers .= sprintf($apat, $k, $v);
		}

		$trivia_interface = sprintf($pat, $url, $state['question']['text'],
			$qid, $answers, $qid);

		echo $trivia_interface;
	}
	else
	{
		$ids = explode("/", $raw);
		$session_id = $ids[0];
		$state_id = $ids[1];

		$state = trivia_getState($state_id);

		// Verify Session
		if($session_id !== trivia_getSession())
		{
			trivia_reset();
			trivia_err(0);
		}

		// Verify State
		if($state === false)
		{
			trivia_reset();
			trivia_err(1);
		}

		$qid = $state['question']['id'];
		$aid = "";

		if(isset($_POST[$qid]))
			$aid = $_POST[$qid];
		else if(isset($_GET[$qid]))
			$aid = $_GET[$qid];

		if($aid == $state['question']['ca_id'])
		{
			echo "<div class=\"trivia-result correct\">\nCorrect!\n"
				."<script>\n"
				."setTimeout(function(){\n"
				."$('.m-game .trivia-result').attr('data-mgame', 'hidden');\n"
				."}, 1500);\n"
				."setTimeout(function(){\n"
				."$('.m-game .trivia-result').remove();\n"
				."}, 1800);\n"
				."</script>\n"
				."\n</div>";
		}
		else
		{
			echo "<div class=\"trivia-result wrong\">\n"
				."Wrong! The correct answer was "
				.$trivia[$state['section_id']][$state['question']['id']]['a'][
					$state['question']['ca_id']
				]
				.".\n"
				."<script>\n"
				."setTimeout(function(){\n"
				."$('.m-game .trivia-result').attr('data-mgame', 'hidden');\n"
				."}, 1500);\n"
				."setTimeout(function(){\n"
				."$('.m-game .trivia-result').remove();\n"
				."}, 1800);\n"
				."</script>\n"
				."</div>\n";
		}

		$questions = $state['questions'];
		array_shift($questions);

		if(count($questions) > 0)
		{
			$nState = array(
				'section_orders' => $state['section_orders'],
				'section_id' => $state['section_id'],
				'questions' => $questions,
				'question' => array(
					'id' => $questions[0],
					'ca_id' => key($trivia[$state['section_id']][$questions[0]]['a']),
					'text' => $trivia[$state['section_id']][$questions[0]]['q'],
					'answers' => shuffle_assoc($trivia[$state['section_id']][$questions[0]]['a'])
				)
			);

			// Store state
			$state_id = trivia_createState($nState, $state_id);

			// Trivia Interface
			$url = sprintf($upat, $session_id, $state_id);
			$answers = "";
			$trivia_interface = "";

			foreach($nState['question']['answers'] as $k=>$v)
			{
				$answers .= sprintf($apat, $k, $v);
			}

			$trivia_interface = sprintf($pat, $url, $nState['question']['text'],
				$questions[0], $answers, $questions[0]);

			echo $trivia_interface;
		}
		else
		{
			$so = $state['section_orders'];
			array_shift($so);

			if(count($so) > 0)
			{
				$qs = array();
				foreach($trivia[$so[0]] as $k=>$v)
				{
					array_push($qs, $k);
				}

				$qid = $qs[0];

				$nState = array(
					'section_orders' => $so,
					'section_id' => $so[0],
					'questions' => $qs,
					'question' => array(
						'id' => $qid,
						'ca_id' => key($trivia[$so[0]][$qid]['a']),
						'text' => $trivia[$so[0]][$qid]['q'],
						'answers' => shuffle_assoc($trivia[$so[0]][$qid]['a'])
					)
				);

				// Store state
				$state_id = trivia_createState($nState, $state_id);

				// Trivia Interface
				$url = sprintf($upat, $session_id, $state_id);
				$answers = "";
				$trivia_interface = "";

				foreach($nState['question']['answers'] as $k=>$v)
				{
					$answers .= sprintf($apat, $k, $v);
				}

				$trivia_interface = sprintf($pat, $url, $nState['question']['text'],
					$qid, $answers, $qid);

				echo $trivia_interface;
			}
			else
			{
				unset($_SESSION['mgame_trivia_session'],
					$_SESSION['mgame_trivia_'.$state_id]);
				echo "<pre class=\"m-code\">{TRIVIA.NEXTGAME}</pre>\n"
					."<script>mgameCheckForCodes();</script>\n";
			}
		}
	}
}
