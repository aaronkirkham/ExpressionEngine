<?php extend_template('default-nav', 'outer_box'); ?>

<div class="box mb">
	<h1><?=lang('sql_manager_abbr')?></h1>
	<div class="txt-wrap">
		<ul class="checklist">
			<li><?=lang('mysql')?> <?=$sql_version?> / <b><?=lang('total_records')?>:</b> <?=$records?> / <b><?=lang('size')?>: </b><?=$size?></li>
			<li class="last"><b><?=lang('uptime')?>:</b> <?=$database_uptime?></li>
		</ul>
	</div>
</div>
<div class="box">
	<div class="tbl-ctrls">
		<?=form_open($table['base_url'])?>
			<?php $this->view('_shared/alerts')?>
			<fieldset class="tbl-search right">
				<input placeholder="<?=lang('type_phrase')?>" name="search" type="text" value="<?=$table['search']?>">
				<input class="btn submit" type="submit" name="search_form" value="<?=lang('search_tables')?>">
			</fieldset>
			<h1><?=lang('database_tables')?></h1>
			<?php $this->view('_shared/table', $table); ?>
			<fieldset class="tbl-bulk-act">
				<select name="table_action">
					<option value="none">-- with selected --</option>
					<option value="REPAIR">Repair</option>
					<option value="OPTIMIZE">Optimize</option>
				</select>
				<input class="btn submit" type="submit" value="submit">
			</fieldset>
		</form>
	</div>
</div>