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
	<title>Task 2.2 - Query Result</title>

	<meta name="viewport" content="width=device-width, initial-scale=1">
	<meta charset='UTF-8'>
	<?php include 'style.php'; ?>
</head>

<body>
	<div id='header'>
		<?php include 'menu.php'; ?>
	</div>

	<div class='content'>
		<div class="h3 header text-center">Task 2.2</div>
		<div class="row">
			<div class="col-md-12">
				<?php

				$str = '';

				// $query = "SELECT time_ref,count(value) as trade_value FROM Task2.gsquarterlySeptember20 GROUP BY time_ref order by trade_value desc LIMIT 10";
				// $query = "SELECT c.country_label,'Goods' as product_type,import_value - export_value as deficit_value, 'F' as status" .
				// 	" FROM " .
				// 	" ( " .
				// 	" SELECT country_code,SUM(value) as import_value " .
				// 	" FROM Task2.gsquarterlySeptember20  " .
				// 	" WHERE status = 'F' and account = 'Imports' and product_type = 'Goods' " .
				// 	" and cast(LEFT(time_ref,4) as numeric) between 2014 and 2016 " .
				// 	" GROUP BY country_code " .
				// 	" ) I " .
				// 	" FULL JOIN " .
				// 	" ( " .
				// 	" SELECT country_code,SUM(value) as export_value " .
				// 	" FROM Task2.gsquarterlySeptember20  " .
				// 	" WHERE status = 'F' and account = 'Exports' and product_type = 'Goods' " .
				// 	" and cast(LEFT(time_ref,4) as numeric) between 2014 and 2016 " .
				// 	" GROUP BY country_code " .
				// 	" ) E " .
				// 	" ON I.country_code = E.country_code " . //and I.product_type = E.product_type " .
				// 	" LEFT JOIN Task2.country_classification C " .
				// 	" ON C.country_code = I.country_code " .
				// 	" ORDER BY deficit_value DESC " .
				// 	" LIMIT 50 ";
				$query = <<<ENDSQL
				SELECT c.country_label,I.product_type
					--,import_value, export_value
					,(import_value - export_value) as deficit_value
					,'F' as status
					FROM
					(SELECT country_code,product_type,sum(value) as import_value
					FROM Task2.gsquarterlySeptember20 
					where status='F' and account ='Imports' and product_type = 'Goods'
					and cast(left(time_ref,4) as numeric) between 2014 and 2016
					group by country_code,product_type
					) I
					full join
					(
					SELECT country_code,product_type,sum(value) as export_value
					FROM Task2.gsquarterlySeptember20 
					where status='F' and account ='Exports' and product_type = 'Goods'
					and cast(left(time_ref,4) as numeric) between 2014 and 2016
					group by country_code,product_type
					) E
					on I.country_code = E.country_code and I.product_type = E.product_type
					left join Task2.country_classification C
					on C.country_code = I.country_code
					order by deficit_value desc 
					limit 50
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
					"<th scope='col'>Country Label</th>" .
					"<th scope='col'>Product Type</th>" .
					"<th scope='col'>Trade deficient Value</th>" .
					"<th scope='col'>Status</th>" .
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