<?php
session_start();
?>
<?php
session_start();
require_once __DIR__ . '/vendor/autoload.php';
require_once __DIR__ . '/config.php';
?>
<!DOCTYPE html>
<html>

<head>
	<title>Task 2.1 - Query Result</title>
	<meta name="viewport" content="width=device-width, initial-scale=1">
	<meta charset='UTF-8'>
	<?php include 'style.php'; ?>
</head>

<body>
	<div id='header'>
		<?php include 'menu.php'; ?>
	</div>

	<div class='content'>
		<div class="h3 header text-center">Task 2.1</div>
		<div class="row">
			<div class="col-md-12">
				<?php

				$query = <<<ENDSQL
				SELECT time_ref,sum(value) as trade_value
					FROM `Task2.gsquarterlySeptember20`
					GROUP BY time_ref
					ORDER BY 2 DESC
					LIMIT 10
				ENDSQL;

				$jobConfig = $bigQuery->query($query);
				$job = $bigQuery->startQuery($jobConfig);

				$backoff->execute(function () use ($job) {
					//print('Waiting for job to complete' . PHP_EOL);
					$job->reload();
					if (!$job->isComplete()) {
						throw new Exception('Job has not yet completed', 500);
					}
				});
				$queryResults = $job->queryResults();


				$str = "<table class='table'>" .
					"<tr>" .
					"<th scope='col'>Time Ref</th>" .
					"<th scope='col'>Trade Value</th>" .
					"</tr>";
				$i = 0;
				foreach ($queryResults as $row) {
					++$i;
					$str .= "<tr>";
					foreach ($row as $column => $value) {
						$str .= "<td>" . $value . "</td>";
						// printf('%s: %s' . PHP_EOL, $column, json_encode($value));
					}
					$str .= "</tr>";
				}

				$str .= '</table>';

				echo $str;

				//printf('Found %s row(s)' . PHP_EOL, $i);

				?>
			</div>
		</div>
	</div>
	<script src="https://code.jquery.com/jquery-3.2.1.slim.min.js" integrity="sha384-KJ3o2DKtIkvYIK3UENzmM7KCkRr/rE9/Qpg6aAZGJwFDMVNA/GpGFF93hXpG5KkN" crossorigin="anonymous"></script>
	<script src="https://cdnjs.cloudflare.com/ajax/libs/popper.js/1.12.9/umd/popper.min.js" integrity="sha384-ApNbgh9B+Y1QKtv3Rn7W3mgPxhU9K/ScQsAP7hUibX39j7fakFPskvXusvfa0b4Q" crossorigin="anonymous"></script>
	<script src="https://maxcdn.bootstrapcdn.com/bootstrap/4.0.0/js/bootstrap.min.js" integrity="sha384-JZR6Spejh4U02d8jOt6vLEHfe/JQGiRRSQQxSfFWpi1MquVdAyjUar5+76PVCmYl" crossorigin="anonymous"></script>
</body>

</html>