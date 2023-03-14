<?php

use MediaWiki\Minerva\Menu\Entries\IMenuEntry;

/**
 * Model for a simple menu entries with label and icon from font-awsome
 */
class FontAwesomeMenuEntry implements IMenuEntry {

	/**
	 * @var string
	 */
	private $name;
	/**
	 * @var array
	 */
	private $component;

	/**
	 * Create a simple menu element with one component
	 *
	 * @param string $name An unique menu element identifier
	 * @param string $text Text to show on menu element
	 * @param string $url URL menu element points to
	 * @param string|null $iconName The Icon name, if not defined, the $name will be used
	 */
	public function __construct( $name, $text, $url, $iconName = null ) {
		$this->name = $name;
		$iconClass = $iconName ?? $name;
		$this->component = [
			'text' => $text,
			'href' => $url,
			'class' => "fa fa-$iconClass",
		];
	}

	/**
	 * @inheritDoc
	 */
	public function getName() {
		return $this->name;
	}

	/**
	 * @inheritDoc
	 */
	public function getCSSClasses(): array {
		return [];
	}

	/**
	 * @inheritDoc
	 */
	public function getComponents(): array {
		return [ $this->component ];
	}
}
