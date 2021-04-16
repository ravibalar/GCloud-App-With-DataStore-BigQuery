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
	<title>Task 2.3 - Query Result</title>

	<meta name="viewport" content="width=device-width, initial-scale=1">
	<meta charset='UTF-8'>
	<?php include 'style.php'; ?>
</head>

<body>
	<div id='header'>
		<?php include 'menu.php'; ?>
	</div>

	<div class='content'>
		<div class="h3 header text-center">Task 2.3</div>
		<div class="row">
			<div class="col-md-12">
				<?php

				$query = <<<ENDSQL
				SELECT
						T23_I.code as service_code,
						s.service_label as service_Label,
						export_value - import_value as surplus_value
					FROM
						(
							SELECT
								T231.code,
								sum(T231.value) as import_value
							FROM
								`Task2.gsquarterlySeptember20` T231
								INNER JOIN (
									SELECT
										time_ref,
										sum(value) as trade_value
									FROM
										`Task2.gsquarterlySeptember20`
									GROUP BY
										time_ref
									ORDER BY
										2 DESC
									LIMIT
										10
								) T21 ON T231.time_ref = T21.time_ref
								INNER JOIN (
									SELECT
										I.country_code,
										(import_value - export_value) as deficit_value
									FROM
										(
											SELECT
												country_code,
												product_type,
												sum(value) as import_value
											FROM
												`Task2.gsquarterlySeptember20`
											WHERE
												status = 'F'
												AND account = 'Imports'
												AND product_type = 'Goods'
												AND cast(left(time_ref, 4) as numeric) between 2014
												AND 2016
											GROUP BY
												country_code,
												product_type
										) I full
										join (
											SELECT
												country_code,
												product_type,
												sum(value) as export_value
											FROM
												`Task2.gsquarterlySeptember20`
											WHERE
												status = 'F'
												AND account = 'Exports'
												AND product_type = 'Goods'
												AND cast(left(time_ref, 4) as numeric) between 2014
												AND 2016
											GROUP BY
												country_code,
												product_type
										) E ON I.country_code = E.country_code
										AND I.product_type = E.product_type
									ORDER BY
										deficit_value desc
									LIMIT
										50
								) T22 ON T231.country_code = T22.country_code
							WHERE
								T231.account = 'Imports'
							GROUP BY
								T231.code
						) T23_I
						LEFT JOIN (
							SELECT
								T231.code,
								sum(T231.value) as export_value
							FROM
								`Task2.gsquarterlySeptember20` T231
								INNER JOIN (
									SELECT
										time_ref,
										sum(value) as trade_value
									FROM
										`Task2.gsquarterlySeptember20`
									GROUP BY
										time_ref
									ORDER BY
										2 DESC
									LIMIT
										10
								) T21 ON T231.time_ref = T21.time_ref
								INNER JOIN (
									SELECT
										I.country_code,
										(import_value - export_value) as deficit_value
									FROM
										(
											SELECT
												country_code,
												product_type,
												sum(value) as import_value
											FROM
												`Task2.gsquarterlySeptember20`
											WHERE
												status = 'F'
												AND account = 'Imports'
												AND product_type = 'Goods'
												AND cast(left(time_ref, 4) as numeric) between 2014
												AND 2016
											GROUP BY
												country_code,
												product_type
										) I FULL
										JOIN (
											SELECT
												country_code,
												product_type,
												sum(value) as export_value
											FROM
												`Task2.gsquarterlySeptember20`
											WHERE
												status = 'F'
												AND account = 'Exports'
												AND product_type = 'Goods'
												AND cast(left(time_ref, 4) as numeric) between 2014
												AND 2016
											GROUP BY
												country_code,
												product_type
										) E ON I.country_code = E.country_code
										AND I.product_type = E.product_type
									ORDER BY
										deficit_value desc
									LIMIT
										50
								) T22 ON T231.country_code = T22.country_code
							WHERE
								T231.account = 'Exports'
							GROUP BY
								T231.code
						) T23_E ON T23_I.code = T23_E.code
						LEFT JOIN `Task2.services_classification` s ON T23_I.code = s.code
					ORDER BY
						surplus_value DESC
					LIMIT
						30
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
					"<th scope='col'>Service Code</th>" .
					"<th scope='col'>Label</th>" .
					"<th scope='col'>Surplus Value</th>" .
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
				// foreach ($queryResults as $row) {
				// 	printf('--- Row %s ---' . PHP_EOL, ++$i);
				// 	foreach ($row as $column => $value) {
				// 		printf('%s: %s' . PHP_EOL, $column, json_encode($value));
				// 	}
				// }
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