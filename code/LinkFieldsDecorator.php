<?php

/**
 * n.b. This decorator doesn't implement updateCMSFields(), you should add the fields using
 * LinkFields::addLinkFields();
 * @author simonwade
 */
class LinkFieldsDecorator extends DataObjectDecorator {

	public function extraStatics() {
		return array(
			'db' => array(
				'LinkType' => 'Enum("NoLink, Internal, External, File")',
				'LinkLabel' => 'Varchar(255)',
				'LinkTargetURL' => 'Varchar(255)',
				'OpenInLightbox' => 'Boolean',
			),
			'defaults' => array(
				'LinkType' => 'NoLink',
			),
			'has_one' => array(
				'LinkTarget' => 'SiteTree',
				'LinkFile' => 'File',
			),
		);
	}

	public function LinkURL() {
		return LinkFields::getLinkURL($this->owner);
	}

	public function LinkClass() {
		return (isset($this->owner->LinkClass) ? ' '.$this->owner->LinkClass : '')
				.' '.strtolower(substr($this->owner->LinkType, 0, 1)).substr($this->owner->LinkType, 1)
				.($this->owner->OpenInLightbox ? ' lightbox' : '');
	}

	public function Anchor( $label = null ) {
		if( $url = $this->LinkURL() ) {
			if( $label === null ) {
				$label = htmlspecialchars($this->owner->LinkLabel);
			}
			$title = htmlspecialchars($this->owner->Title);
			return "<a href='$url' class='{$this->LinkClass()} page-event' "
					. "title='$title' "
					. ($this->owner->LinkType == 'External' ? 'target="_blank" ' : '')
					. "data-category='" . preg_replace('/(.)([A-Z])/', '$1 $2', $this->owner->class) . "' "
					. "data-label='$title'"
					. ">$label</a>";
		}
	}

	public function HasLink() {
		switch( $this->owner->LinkType ) {
			case 'NoLink':
				return false;
			case 'Internal':
				return $this->owner->LinkTarget()->exists();
			case 'External':
				return $this->owner->LinkTargetURL ? true : false;
			case 'File':
				return $this->owner->LinkFile()->exists();
		}
	}

}

?>