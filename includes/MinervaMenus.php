<?php

class MinervaMenus {

	/**
	 * Add the style modules, because the MobileMenu hook was called too late
	 * to add resource loader modules since 1.35.
	 *
	 * @param OutputPage $out
	 * @param Skin $skin
	 */
	public static function onBeforePageDisplay( OutputPage $out, Skin $skin ) {
		if ( $skin->getSkinName() !== 'minerva' ) {
			return;
		}
		// FIXME: Register fontawesome properly.
		$out->addModuleStyles( [ 'fontawesome', 'ext.minervamenus.styles' ] );
	}

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
		if ( $context->getOutput()->isArticleRelated() ) {
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
