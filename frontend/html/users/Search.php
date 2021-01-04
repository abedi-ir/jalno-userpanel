<?php
use packages\userpanel;
use packages\userpanel\{user, Authentication};
use themes\clipone\utility;

$this->the_header();

if ($this->canExport) {
?>
	<div class="row">
		<div class="col-md-3 col-md-offset-9 form-group">
			<a class="btn btn-info btn-block" href="<?php echo userpanel\url("users", array_merge($this->getFormData(), array("download" => "csv"))); ?>">
				<div class="btn-icons"> <i class="fa fa-download"></i> </div>
				<?php echo t("userpanel.users.export.csv"); ?>
			</a>
		</div>
	</div>
<?php } ?>
<div class="panel panel-default">
	<div class="panel-heading">
		<i class="fa fa-users"></i> <?php echo t("users"); ?>
		<div class="panel-tools">
			<a class="btn btn-xs btn-link tooltips" title="<?php echo t("user.add"); ?>" href="<?php echo userpanel\url("users/add"); ?>"><i class="clip-user-plus"></i></a>
			<a class="btn btn-xs btn-link tooltips" title="<?php echo t("user.search"); ?>" data-toggle="modal" href="#users-search"><i class="fa fa-search"></i></a>
			<a class="btn btn-xs btn-link panel-collapse collapses" href="#"></a>
		</div>
	</div>
	<div class="panel-body">
		<div class="table-responsive">
			<table class="table table-hover">
				<?php
				$hasButtons = $this->hasButtons();
				?>
				<thead>
					<tr>
						<th class="center">#</th>
						<th><?php echo t("user.name"); ?></th>
						<th><?php echo t("user.type.name"); ?></th>
						<th><?php echo t("user.email"); ?><br><?php echo t("user.cellphone"); ?></th>
						<th><?php echo t("user.country") . " - " . t("user.city"); ?></th>
						<th><?php echo t("user.status"); ?></th>
						<?php if($hasButtons){ ?><th></th><?php } ?>
					</tr>
				</thead>
				<tbody>
					<?php
					$me = Authentication::getID();
					foreach ($this->getDataList() as $row) {
						$this->setButtonParam("view", "link", userpanel\url("users/view/" . $row->id));
						$this->setButtonParam("edit", "link", userpanel\url("users/edit/" . $row->id));
						$this->setButtonParam("delete", "link", userpanel\url("users/delete/" . $row->id));
						$this->setButtonActive("delete", ($this->canDelete and $row->id != $me));
						$statusClass = Utility::switchcase($row->status, array(
							"label label-inverse" => User::deactive,
							"label label-success" => User::active,
							"label label-warning" => User::suspend
						));
						$statusTxt = Utility::switchcase($row->status, array(
							"user.status.deactive" => User::deactive,
							"user.status.active" => User::active,
							"user.status.suspend" => User::suspend
						));
						$country = "";
						if ($row->country) {
							$country = $row->country->name;
						}
						if ($row->city) {
							$country .= ($country ? " - " : "") . $row->city;
						}
					?>
					<tr>
						<td class="center"><?php echo $row->id; ?></td>
						<td><?php echo $row->getFullName(); ?></td>
						<td><?php echo $row->type->title; ?></td>
						<td><?php echo $row->email; ?><br><?php echo $row->cellphone; ?></td>
						<td class="center"><?php echo $country ? $country : "-"; ?></td>
						<td><span class="<?php echo $statusClass; ?>"><?php echo t($statusTxt); ?></span></td>
						<?php
						if($hasButtons){
							echo("<td class=\"center\">".$this->genButtons()."</td>");
						}
						?>
						</tr>
					<?php
					}
					?>
				</tbody>
			</table>
		</div>
		<?php $this->paginator(); ?>
	</div>
</div>
<div class="modal fade" id="users-search" tabindex="-1" data-show="true" role="dialog">
	<div class="modal-header">
		<button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>
		<h4 class="modal-title"><?php echo t("users.search"); ?></h4>
	</div>
	<div class="modal-body">
		<form id="users-search-form" action="<?php echo userpanel\url("users"); ?>" method="GET" class="form-horizontal">
			<?php
			$this->setHorizontalForm("sm-3","sm-9");
			$feilds = array(
				array(
					"label" => t("user.id"),
					"name" => "id",
					"type" => "number",
					"ltr" => true,
					"min" => 1,
				),
				array(
					"label" => t("user.name"),
					"name" => "name"
				),
				array(
					"label" => t("user.lastname"),
					"name" => "lastname"
				),
				array(
					"label" => t("user.email"),
					"name" => "email"
				),
				array(
					"label" => t("user.cellphone"),
					"name" => "cellphone"
				),
				array(
					"name" => "type",
					"type" => "hidden",
				),
				array(
					"name" => "type-select",
					"type" => "select",
					"label" => t("user.type"),
					"multiple" => true,
					"value" => $this->getSelectedTypes(),
					"options" => $this->getTypesForSelect(),
				),
				array(
					"type" => "checkbox",
					"label" => t("userpanel.users.search.has_custom_permissions"),
					"name" => "has_custom_permissions",
					"options" => [array(
						"value" => 1,
						"label" => t("yes")
					)]
				),
				array(
					"type" => "checkbox",
					"label" => t("user.online"),
					"name" => "online",
					"options" => [array(
						"value" => 1,
						"label" => t("user.online.yes")
					)]
				),
				array(
					"type" => "select",
					"label" => t("user.status"),
					"name" => "status",
					"options" => $this->getStatusForSelect()
				),
				array(
					"type" => "select",
					"name" => "country",
					"label" => t("user.country"),
					"options" => $this->getCountriesForSelect(),
					"ltr" => true
				),
				array(
					"label" => t("user.city"),
					"name" => "city"
				),
				array(
					"type" => "select",
					"label" => t("search.comparison"),
					"name" => "comparison",
					"options" => $this->getComparisonsForSelect()
				)
			);
			foreach ($feilds as $input) {
				echo $this->createField($input);
			}
			?>
		</form>
	</div>
	<div class="modal-footer">
		<button type="submit" form="users-search-form" class="btn btn-success"><?php echo t("userpanel.search"); ?></button>
		<button type="button" class="btn btn-default" data-dismiss="modal" aria-hidden="true"><?php echo t("userpanel.cancel"); ?></button>
	</div>
</div>
<?php

$this->the_footer();