<div id='container'>
	<p>The total # of users who selected one or more theme, and/or MarketWatch:</p>
	<ul>
		<li>Members: <?php echo $members; ?></li>
		<li>Employees: <?php echo $employees; ?></li>
	</ul>
	<p>Of those total user subscriptions...</p>
	<div style='margin-left:30px;'>
	<p>Users who selected one or more theme</p>
	<ul>
		<li>Members: <?php echo $members_no_market_watch; ?></li>
		<li>Employees: <?php echo $employees_no_market_watch; ?></li>
	</ul>
	<p>Users who selected MarketWatch</p>
	<ul>
		<li>Members: <?php echo $members_market_watch; ?></li>
		<li>Employees: <?php echo $employees_market_watch; ?></li>
	</ul>
	<h2>Themes Report</h2>
	<table class='formatted_table'>
		<tr>
			<th>Theme</th>
			<th>Number of emails sent out</th>
			<th>Email subjects</th>
		</tr>
		<?php for ($i=0; $i<count($themes_report['theme']); $i++): ?>
			<tr>
				<td><?php echo $themes_report['theme'][$i]; ?></td>
				<td><?php echo $themes_report['num_emails'][$i]; ?></td>
				<td>
					<?php 
						for ($j=0; $j<count($themes_report['subjects'][$i]); $j++)
							echo "<p>".$themes_report['subjects'][$i][$j]."</p>";
					?>				
				</td>
			</tr>
		<?php endfor; ?>
	</table>
	<h2>Subscribers</h2>
	<table>
		<?php foreach ($subscribers as $subscriber=>$subscriptions): ?>
			<tr>
				<th><?php echo  $subscriber; ?></th>
				<th><?php echo $subscriptions[0]; ?></th>
				<?php for ($i=1; $i<count($subscriptions); $i++): ?>
					<tr>
						<td></td>
						<td><?php echo $subscriptions[$i]; ?></td>
					</tr>
				<?php endfor; ?>
			</tr>
		<?php endforeach; ?>
	</table>
	<a href='admin'>Back to administration</a>
</div>