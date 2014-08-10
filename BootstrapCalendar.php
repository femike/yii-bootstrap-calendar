<?php

/**
 * Class BootstrapCalendar
 */
class BootstrapCalendar extends CWidget
{
	public $data = false;
	public $compliance = [];
	public $options = [];
	public $buttons = [
		'prev' => [
			'title' => '<< Prev'
		],
		'today' => [
			'title' => 'Today'
		],
		'next' => [
			'title' => 'Next >>'
		]
	];
	public $buttonsTime = false;
	public $assetUrl;

	public function run()
	{
		if (!$this->assetUrl)
			$this->assetUrl = Yii::app()->getAssetManager()->publish(
				Yii::getPathOfAlias('ext.widgets.lib.bootstrap-calendar.assets')
			);

		$cli = Yii::app()->getClientScript();
		// @todo isset bootstrap.js
		$cli->registerCssFile($this->assetUrl . '/css/calendar.min.css');
		$cli->registerScriptFile($this->assetUrl . '/js/underscore/underscore-min.js');
		$cli->registerScriptFile($this->assetUrl . '/js/calendar.min.js');
		if (isset($this->options['language']))
			$cli->registerScriptFile($this->assetUrl . '/js/language/' . $this->options['language'] . '.js');

		$this->options['tmpl_path'] = $this->assetUrl . '/tmpls/';

		if(isset($this->options['url_source'])){
			$this->options['events_source'] = $this->options['url_source'];
		} else {
			$this->options['events_source'] = 'js:function(){ return' . CJSON::encode(array_values($this->prepareData())) . '}';
		}

		$this->options['onAfterViewLoad'] = 'js:function(view) {
			$(\'#bootstrap-calendar h3\').text(this.getTitle());
			$(\'#bootstrap-calendar .btn-group button\').removeClass(\'active\');
			$(\'#bootstrap-calendar button[data-calendar-view="\' + view + \'"]\').addClass(\'active\');
		}';

		$cli->registerScript('bootstrap-calendar',
			"var calendar = $('#calendar').calendar(" . CJavaScript::encode($this->options) . ");
			", CClientScript::POS_READY
		);

		$cli->registerScript('bootstrap-calendar-buttons',
			"$('.btn-group button[data-calendar-nav]').each(function() {
				var \$this = $(this); \$this.click(function() {
					calendar.navigate(\$this.data('calendar-nav'));
				});
			});

			$('.btn-group button[data-calendar-view]').each(function() {
				var \$this = $(this);
				\$this.click(function() {
					calendar.view(\$this.data('calendar-view'));
				});
			});", CClientScript::POS_READY);

		echo "<!-- open calendar --><div id='bootstrap-calendar'>";
		echo "<h3></h3>";

		echo '
			<div class="pull-right form-inline">
				<div class="btn-group">
			';
		echo (isset($this->buttons['prev']) && $this->buttons['prev']) ?
			'<button class="btn btn-primary" data-calendar-nav="prev">' . $this->buttons['prev']['title'] . '</button>' : '';
		echo (isset($this->buttons['today']) && $this->buttons['today']) ?
			'<button class="btn btn-default" data-calendar-nav="today">' . $this->buttons['today']['title'] . '</button>' : '';
		echo (isset($this->buttons['next']) && $this->buttons['next']) ?
			'<button class="btn btn-primary" data-calendar-nav="next">' . $this->buttons['next']['title'] . '</button>' : '';

		echo ($this->buttonsTime) ? '</div>
				<div class="btn-group">
					<button class="btn btn-warning" data-calendar-view="year">' . Yii::t('BootstrapCalendar.boot-cal', 'Year') . '</button>
					<button class="btn btn-warning active" data-calendar-view="month">' . Yii::t('BootstrapCalendar.boot-cal', 'Month') . '</button>
					<button class="btn btn-warning" data-calendar-view="week">' . Yii::t('BootstrapCalendar.boot-cal', 'Week') . '</button>
					<button class="btn btn-warning" data-calendar-view="day">' . Yii::t('BootstrapCalendar.boot-cal', 'Day') . '</button>
				</div>
			</div>
		' : '';

		echo "<div class='clearfix'></div><br />";
		echo '<div id="calendar" style="position: relative; z-index: 1004"></div>';
		echo "</div><!-- close calendar -->";
	}

	/**
	 *
	 */
	public function prepareData()
	{
		$return = [];
		$compliance = $this->compliance;

		if (count($this->data)) {
			foreach ($this->data as $data) {

				if (isset($compliance['id']) &&
					isset($compliance['id']['name']) &&
					isset($data->$compliance['id']['name'])
				) {
					$id = $data->$compliance['id']['name'];
				} elseif (isset($data->id)) {
					$id = $data->id;
				}

				if (isset($id)) {
					$return[$id]['id'] =
						(isset($compliance['id']) && isset($compliance['id']['value']))
							? $this->evaluateExpression($compliance['id']['value'], ['data' => $data]) : $data->id;

					$return[$id]['title'] = (isset($compliance['title']) && isset($compliance['title']['value']))
						? $this->evaluateExpression($compliance['title']['value'], ['data' => $data]) : $data->title;

					$return[$id]['url'] = (isset($compliance['url']) && isset($compliance['url']['value']))
						? $this->evaluateExpression($compliance['url']['value'], ['data' => $data]) : $data->url;

					$return[$id]['class'] = (isset($compliance['class']) && isset($compliance['class']['value']))
						? $this->evaluateExpression($compliance['class']['value'], ['data' => $data]) :
						isset($data->class) ? $data->class : 'event-important';

					$return[$id]['start'] = strtotime((isset($compliance['start']) && isset($compliance['start']['value']))
							? $this->evaluateExpression($compliance['start']['value'], ['data' => $data]) : $data->start) . '000';

					$end = (isset($compliance['end']) && isset($compliance['end']['value']))
						? $this->evaluateExpression($compliance['end']['value'], ['data' => $data]) : $data->end;

					$return[$id]['end'] = (strtotime($end)) ? strtotime($end) . '000' : $return[$id]['start'];

				}
			}
		}

		return $return;
	}

	/**
	 * @param $_expression_
	 * @param array $_data_
	 * @return mixed
	 */
	public function evaluateExpression($_expression_, $_data_ = [])
	{
		if (is_string($_expression_)) {
			extract($_data_);

			return eval('return ' . $_expression_ . ';');
		} else {
			$_data_[] = $this;

			return call_user_func_array($_expression_, $_data_);
		}
	}
}