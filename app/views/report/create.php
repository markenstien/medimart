<?php build('content') ?>
	
	<div class="card">
		<div class="card-header">
			<h4 class="card-title">Generate Report</h4>
			<?php Flash::show()?>
		</div>

		<div class="card-body">

			<div class="col-md-5 mx-auto">
				<?php if(!isset($_GET['filter'])) :?>
					<?php
						Form::open([
							'method' => 'GET'
						]);
					?>
					<div class="form-group row">
						<div class="col">
							<?php
								Form::label('Start Date');
								Form::date('start_date' , '' , [
									'class' => 'form-control'
								])
							?>
						</div>
						<div class="col">
							<?php
								Form::label('End Date');
								Form::date('end_date' , '' , [
									'class' => 'form-control'
								])
							?>
						</div>
					</div>


					<div class="form-group mt-2">
						<div class="col">
							<?php Form::label('Report Sections'); ?>

							<div class="form-control">
								<?php foreach($report_sections as $key => $row)  :?>
									<label>
										<input type="checkbox" name="report_sections[]" value="<?php echo $key?>">
										<?php echo $row?>
									</label>
								<?php endforeach?>
							</div>
						</div>
					</div>
					<hr>
					<?php Form::submit('filter', 'Generate Report')?>
					<?php Form::close()?>
				<?php endif?>
			</div>

			<div class="mt-2">
				<?php if(isset($_GET['filter'])) :?>
					<a href="?" class="btn btn-secondary btn-sm">Remove Filter</a>
					<a href="javascript:void(0)" class="btn btn-secondary btn-sm" onclick="window.print()">Print</a>
				<?php endif?>
			</div>

			<?php if(isset($items)) :?>
				<?php divider()?>
				<div class="text-center">
					<h5 class="mb-2">Start Date : <?php echo $_GET['start_date']?> TO End Date : <?php echo $_GET['end_date']?></h5>
					<?php if( $report_section_selected ) : ?>
						Filters Used: <?php echo implode(',' , $report_section_selected)?>
					<?php endif?>

					<p>Report Generated By : <span style="font-weight:bold"><?php echo whoIs('first_name') . ' '.whoIs('last_name')  ?></span></p>
					<small>As of : <?php echo date('Y-m-d H:i:s')?></small>
					<div class="mt-2"></div>
				</div>
				<div class="table-responsive">
					<table class="table table-bordered">
						<thead>
							<?php if( isEqual('summary_of_severity' , $report_section_selected) ) :?>
								<th>Total Mild Cases</th>
								<th>Total Moderate Cases</th>
								<th>Total Severe Cases</th>
							<?php endif?>
							<th>Deployed Patients</th>
							<th>Total Records</th>
						</thead>
						<tbody>
							<tr>
								<?php if( isEqual('summary_of_severity' , $report_section_selected) ) :?>
									<td><?php echo $items['summary']['total_miled_cases']?></td>
									<td><?php echo $items['summary']['total_moderate_cases']?></td>
									<td><?php echo $items['summary']['total_severe_cases']?></td>
								<?php endif?>
								<td><?php echo $items['summary']['total_deployed_cases']?></td>
								<td><?php echo $items['summary']['total_record']?></td>
							</tr>
						</tbody>

						<thead>
							<th>Recovered Cases</th>
							<th>Deceased Cases</th>
							
							<?php if( isEqual('hospital_quarantine ' , $report_section_selected) ) :?>
								<th>Hospital Quarantine</th>
							<?php endif?>

							<?php if( isEqual('home_quarantine' , $report_section_selected) ) :?>
								<th>Home Quarantine</th>
							<?php endif?>
						</thead>
						<tbody>
							<tr>
								<td><?php echo $items['summary']['recovered_cases']?></td>
								<td><?php echo $items['summary']['total_death']?></td>
								<?php if( isEqual('hospital_quarantine' , $report_section_selected) ) :?>
									<td><?php echo $items['summary']['number_of_hospital_quarantine']?></td>
								<?php endif?>

								<?php if( isEqual('home_quarantine' , $report_section_selected) ) :?>
									<td><?php echo $items['summary']['number_of_home_quarantine']?></td>
								<?php endif?>
							</tr>
						</tbody>
					</table>
				</div>
				<?php divider()?>

				<h5 class="text-center">Particulars</h5>
				<h6>Laboratories</h6>
				<div class="table-responsive">
				<table class="table table-bordered">
					<thead>
						<th>Reference</th>
						<th>Date Requested</th>
						<th>Date Reported</th>
						<th>Severity</th>
					</thead>

					<tbody>
						<?php foreach($items['laboratories'] as $row) : ?>
							<tr>
								<td><?php echo $row->reference?></td>
								<td><?php echo $row->date_requested?></td>
								<td><?php echo $row->date_reported?></td>
								<td><?php echo $row->severity?></td>
							</tr>
						<?php endforeach?>
					</tbody>
				</table>
				
				<?php divider()?>

				<h6>Records</h6>
				<div class="table-responsive">
				<table class="table table-bordered">
					<thead>
						<th>Reference</th>
						<th>Name</th>
						<th>Date</th>
						<th>Doctors Approval</th>
						<th>Deployed</th>
						<th>Status</th>
					</thead>

					<tbody>
						<?php foreach($items['records'] as $row) : ?>
							<tr>
								<td><?php echo $row->reference?></td>
								<td><?php echo $row->first_name , ' '. $row->last_name?></td>
								<td><?php echo $row->date?></td>
								<td><?php echo !is_null($row->doctors_approval) ? 'Approved' : 'For-Classification'?></td>
								<td><?php echo $row->is_deployed ? 'Patient Is Deployed' : 'Waiting For Deployment'?></td>
								<td><?php echo $row->report_status?></td>
							</tr>
						<?php endforeach?>
					</tbody>
				</table>

				<?php divider()?>

				<h6>Deployments</h6>
				<div class="table-responsive">
				<table class="table table-bordered">
					<thead>
						<th>Reference</th>
						<th>Name</th>
						<th>Hospital/Home</th>
						<th>Status</th>
					</thead>

					<tbody>
						<?php foreach($items['deployments'] as $row) : ?>
							<tr>
								<td><?php echo $row->reference?></td>
								<td><?php echo $row->first_name , ' '. $row->last_name?></td>
								<td>
									<?php if( isEqual($row->type , 'hospital')  ) :?>
										<?php echo $row->name ?? ''?>
									<?php else:?>
										Home Quarantine
									<?php endif?>
								</td>
								<td><?php echo $row->record_status?></td>
							</tr>
						<?php endforeach?>
					</tbody>
				</table>
			</div>
			<?php endif?>
		</div>
	</div>
<?php endbuild()?>
<?php loadTo()?>