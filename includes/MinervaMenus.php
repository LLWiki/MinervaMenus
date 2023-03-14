<?php

class MinervaMenus {

	/**
	 * @param string $groupName
	 * @param array $group
	 */
	public static function onMobileMenu( $groupName, &$group ) {
		if ( $groupName !== 'sitetools' ) {
			return;
		}
		// TODO: Cache this?
		$text = wfMessage( 'minerva-menu' )->plain();
		$lines = explode( "\n", $text );

		foreach ( $lines as $line ) {
			if ( strpos( $line, '*' ) !== 0 ) {
				continue;
			}
			$line = trim( $line, '* ' );
			if ( strpos( $line, '|' ) === false ) {
				continue;
			}
			$line = array_map( 'trim', explode( '|', $line, 3 ) );
			if ( count( $line ) !== 3 ) {
				continue;
			}
			$msgLink = wfMessage( $line[0] );
			if ( $msgLink->exists() ) {
				$link = $msgLink->text();
				if ( $link == '-' ) {
					continue;
				}
			} else {
				$link = $line[0];
			}
			$href = Title::newFromText( $link )->getLinkURL();
			$msgText = wfMessage( $line[1] );
			if ( $msgText->exists() ) {
				$text = $msgText->text();
			} else {
				$text = $line[1];
			}
			try {
				$group->insertEntry(
					new FontAwesomeMenuEntry( $line[1], $text, $href, $line[2] )
				);
			} catch ( DomainException $e ) {
				//Duplicated
			}
		}

		$context = RequestContext::getMain();
		$out = $context->getOutput();
		// TODO: Register this properly.
		// FIXME: This hook was called too late in 1.35.
		//  Upgrade to higher versions and use the new hook should fix this.
		$out->addModuleStyles( [ 'fontawesome' ] );
		if ( $out->isArticleRelated() ) {
			$title = $context->getTitle();
			$thispage = $title->getPrefixedDBkey();
			$group->insertEntry(
				new FontAwesomeMenuEntry(
					'pageinfo',
					wfMessage( 'pageinfo-toolboxlink' )->text(),
					$title->getLocalURL( "action=info" ),
					'info-circle'
				)
			);
			$group->insertEntry(
				new FontAwesomeMenuEntry(
					'whatlinkshere',
					wfMessage( 'whatlinkshere' )->text(),
					SpecialPage::getTitleFor( 'Whatlinkshere', $thispage )->getLocalURL(),
					'quote-right'
				)
			);

			if ( $title->exists() || $title->inNamespace( NS_CATEGORY ) ) {
				$group->insertEntry(
					new FontAwesomeMenuEntry(
						'recentchangeslinked',
						wfMessage( 'recentchangeslinked-toolbox' )->text(),
						SpecialPage::getTitleFor( 'Recentchangeslinked', $thispage )->getLocalURL(),
						'share-alt'
					)
				);
			}
		}
	}
}
