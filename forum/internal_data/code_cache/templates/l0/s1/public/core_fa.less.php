<?php
// FROM HASH: 063ac3bec6de6efbb3994f376c8e4cdd
return array('macros' => array(), 'code' => function($__templater, array $__vars)
{
	$__finalCompiled = '';
	$__finalCompiled .= '/*!
 * Font Awesome Pro by @fontawesome - https://fontawesome.com
 * License - https://fontawesome.com/license (Commercial License)
 */
@font-face {
  font-family: \'Font Awesome 5 Duotone\';
  font-style: normal;
  font-weight: 900;
  src: url(\'@{fa-font-path}/fa-duotone-900.woff2\') format(\'woff2\'),
    url(\'@{fa-font-path}/fa-duotone-900.woff\') format(\'woff\');
}

.fad {
  font-family: \'Font Awesome 5 Duotone\';
  position: relative;
  font-weight: 900;
}

.fad:before {
  position: absolute;
  color: ~\'var(--@{fa-css-prefix}-primary-color, inherit)\';
  opacity: @fa-primary-opacity;
  opacity: ~\'var(--@{fa-css-prefix}-primary-opacity, @{fa-primary-opacity})\';
}

.fad:after {
  color: ~\'var(--@{fa-css-prefix}-secondary-color, inherit)\';
  opacity: @fa-secondary-opacity;
  opacity: ~\'var(--@{fa-css-prefix}-secondary-opacity, @{fa-secondary-opacity})\';
}

.fad.@{fa-css-prefix}-swap-opacity:before {
  opacity: @fa-secondary-opacity;
  opacity: ~\'var(--@{fa-css-prefix}-secondary-opacity, @{fa-secondary-opacity})\';
}

.fad.@{fa-css-prefix}-swap-opacity:after {
  opacity: @fa-primary-opacity;
  opacity: ~\'var(--@{fa-css-prefix}-primary-opacity, @{fa-primary-opacity})\';
}

.fad.@{fa-css-prefix}-inverse {
  color: @fa-inverse;
}

.fad.@{fa-css-prefix}-stack-1x, .fad.@{fa-css-prefix}-stack-2x {
  position: absolute;
}

.fad.@{fa-css-prefix}-stack-1x:before,
.fad.@{fa-css-prefix}-stack-2x:before,
.fad.@{fa-css-prefix}-fw:before {
  left: 50%;
  transform: translateX(-50%);
}

.fad.@{fa-css-prefix}-abacus:after { content: replace(@fa-var-abacus, "\\\\", "\\\\10"); }
.fad.@{fa-css-prefix}-acorn:after { content: replace(@fa-var-acorn, "\\\\", "\\\\10"); }
.fad.@{fa-css-prefix}-ad:after { content: replace(@fa-var-ad, "\\\\", "\\\\10"); }
.fad.@{fa-css-prefix}-address-book:after { content: replace(@fa-var-address-book, "\\\\", "\\\\10"); }
.fad.@{fa-css-prefix}-address-card:after { content: replace(@fa-var-address-card, "\\\\", "\\\\10"); }
.fad.@{fa-css-prefix}-adjust:after { content: replace(@fa-var-adjust, "\\\\", "\\\\10"); }
.fad.@{fa-css-prefix}-air-freshener:after { content: replace(@fa-var-air-freshener, "\\\\", "\\\\10"); }
.fad.@{fa-css-prefix}-alarm-clock:after { content: replace(@fa-var-alarm-clock, "\\\\", "\\\\10"); }
.fad.@{fa-css-prefix}-alarm-exclamation:after { content: replace(@fa-var-alarm-exclamation, "\\\\", "\\\\10"); }
.fad.@{fa-css-prefix}-alarm-plus:after { content: replace(@fa-var-alarm-plus, "\\\\", "\\\\10"); }
.fad.@{fa-css-prefix}-alarm-snooze:after { content: replace(@fa-var-alarm-snooze, "\\\\", "\\\\10"); }
.fad.@{fa-css-prefix}-alicorn:after { content: replace(@fa-var-alicorn, "\\\\", "\\\\10"); }
.fad.@{fa-css-prefix}-align-center:after { content: replace(@fa-var-align-center, "\\\\", "\\\\10"); }
.fad.@{fa-css-prefix}-align-justify:after { content: replace(@fa-var-align-justify, "\\\\", "\\\\10"); }
.fad.@{fa-css-prefix}-align-left:after { content: replace(@fa-var-align-left, "\\\\", "\\\\10"); }
.fad.@{fa-css-prefix}-align-right:after { content: replace(@fa-var-align-right, "\\\\", "\\\\10"); }
.fad.@{fa-css-prefix}-align-slash:after { content: replace(@fa-var-align-slash, "\\\\", "\\\\10"); }
.fad.@{fa-css-prefix}-allergies:after { content: replace(@fa-var-allergies, "\\\\", "\\\\10"); }
.fad.@{fa-css-prefix}-ambulance:after { content: replace(@fa-var-ambulance, "\\\\", "\\\\10"); }
.fad.@{fa-css-prefix}-american-sign-language-interpreting:after { content: replace(@fa-var-american-sign-language-interpreting, "\\\\", "\\\\10"); }
.fad.@{fa-css-prefix}-analytics:after { content: replace(@fa-var-analytics, "\\\\", "\\\\10"); }
.fad.@{fa-css-prefix}-anchor:after { content: replace(@fa-var-anchor, "\\\\", "\\\\10"); }
.fad.@{fa-css-prefix}-angel:after { content: replace(@fa-var-angel, "\\\\", "\\\\10"); }
.fad.@{fa-css-prefix}-angle-double-down:after { content: replace(@fa-var-angle-double-down, "\\\\", "\\\\10"); }
.fad.@{fa-css-prefix}-angle-double-left:after { content: replace(@fa-var-angle-double-left, "\\\\", "\\\\10"); }
.fad.@{fa-css-prefix}-angle-double-right:after { content: replace(@fa-var-angle-double-right, "\\\\", "\\\\10"); }
.fad.@{fa-css-prefix}-angle-double-up:after { content: replace(@fa-var-angle-double-up, "\\\\", "\\\\10"); }
.fad.@{fa-css-prefix}-angle-down:after { content: replace(@fa-var-angle-down, "\\\\", "\\\\10"); }
.fad.@{fa-css-prefix}-angle-left:after { content: replace(@fa-var-angle-left, "\\\\", "\\\\10"); }
.fad.@{fa-css-prefix}-angle-right:after { content: replace(@fa-var-angle-right, "\\\\", "\\\\10"); }
.fad.@{fa-css-prefix}-angle-up:after { content: replace(@fa-var-angle-up, "\\\\", "\\\\10"); }
.fad.@{fa-css-prefix}-angry:after { content: replace(@fa-var-angry, "\\\\", "\\\\10"); }
.fad.@{fa-css-prefix}-ankh:after { content: replace(@fa-var-ankh, "\\\\", "\\\\10"); }
.fad.@{fa-css-prefix}-apple-alt:after { content: replace(@fa-var-apple-alt, "\\\\", "\\\\10"); }
.fad.@{fa-css-prefix}-apple-crate:after { content: replace(@fa-var-apple-crate, "\\\\", "\\\\10"); }
.fad.@{fa-css-prefix}-archive:after { content: replace(@fa-var-archive, "\\\\", "\\\\10"); }
.fad.@{fa-css-prefix}-archway:after { content: replace(@fa-var-archway, "\\\\", "\\\\10"); }
.fad.@{fa-css-prefix}-arrow-alt-circle-down:after { content: replace(@fa-var-arrow-alt-circle-down, "\\\\", "\\\\10"); }
.fad.@{fa-css-prefix}-arrow-alt-circle-left:after { content: replace(@fa-var-arrow-alt-circle-left, "\\\\", "\\\\10"); }
.fad.@{fa-css-prefix}-arrow-alt-circle-right:after { content: replace(@fa-var-arrow-alt-circle-right, "\\\\", "\\\\10"); }
.fad.@{fa-css-prefix}-arrow-alt-circle-up:after { content: replace(@fa-var-arrow-alt-circle-up, "\\\\", "\\\\10"); }
.fad.@{fa-css-prefix}-arrow-alt-down:after { content: replace(@fa-var-arrow-alt-down, "\\\\", "\\\\10"); }
.fad.@{fa-css-prefix}-arrow-alt-from-bottom:after { content: replace(@fa-var-arrow-alt-from-bottom, "\\\\", "\\\\10"); }
.fad.@{fa-css-prefix}-arrow-alt-from-left:after { content: replace(@fa-var-arrow-alt-from-left, "\\\\", "\\\\10"); }
.fad.@{fa-css-prefix}-arrow-alt-from-right:after { content: replace(@fa-var-arrow-alt-from-right, "\\\\", "\\\\10"); }
.fad.@{fa-css-prefix}-arrow-alt-from-top:after { content: replace(@fa-var-arrow-alt-from-top, "\\\\", "\\\\10"); }
.fad.@{fa-css-prefix}-arrow-alt-left:after { content: replace(@fa-var-arrow-alt-left, "\\\\", "\\\\10"); }
.fad.@{fa-css-prefix}-arrow-alt-right:after { content: replace(@fa-var-arrow-alt-right, "\\\\", "\\\\10"); }
.fad.@{fa-css-prefix}-arrow-alt-square-down:after { content: replace(@fa-var-arrow-alt-square-down, "\\\\", "\\\\10"); }
.fad.@{fa-css-prefix}-arrow-alt-square-left:after { content: replace(@fa-var-arrow-alt-square-left, "\\\\", "\\\\10"); }
.fad.@{fa-css-prefix}-arrow-alt-square-right:after { content: replace(@fa-var-arrow-alt-square-right, "\\\\", "\\\\10"); }
.fad.@{fa-css-prefix}-arrow-alt-square-up:after { content: replace(@fa-var-arrow-alt-square-up, "\\\\", "\\\\10"); }
.fad.@{fa-css-prefix}-arrow-alt-to-bottom:after { content: replace(@fa-var-arrow-alt-to-bottom, "\\\\", "\\\\10"); }
.fad.@{fa-css-prefix}-arrow-alt-to-left:after { content: replace(@fa-var-arrow-alt-to-left, "\\\\", "\\\\10"); }
.fad.@{fa-css-prefix}-arrow-alt-to-right:after { content: replace(@fa-var-arrow-alt-to-right, "\\\\", "\\\\10"); }
.fad.@{fa-css-prefix}-arrow-alt-to-top:after { content: replace(@fa-var-arrow-alt-to-top, "\\\\", "\\\\10"); }
.fad.@{fa-css-prefix}-arrow-alt-up:after { content: replace(@fa-var-arrow-alt-up, "\\\\", "\\\\10"); }
.fad.@{fa-css-prefix}-arrow-circle-down:after { content: replace(@fa-var-arrow-circle-down, "\\\\", "\\\\10"); }
.fad.@{fa-css-prefix}-arrow-circle-left:after { content: replace(@fa-var-arrow-circle-left, "\\\\", "\\\\10"); }
.fad.@{fa-css-prefix}-arrow-circle-right:after { content: replace(@fa-var-arrow-circle-right, "\\\\", "\\\\10"); }
.fad.@{fa-css-prefix}-arrow-circle-up:after { content: replace(@fa-var-arrow-circle-up, "\\\\", "\\\\10"); }
.fad.@{fa-css-prefix}-arrow-down:after { content: replace(@fa-var-arrow-down, "\\\\", "\\\\10"); }
.fad.@{fa-css-prefix}-arrow-from-bottom:after { content: replace(@fa-var-arrow-from-bottom, "\\\\", "\\\\10"); }
.fad.@{fa-css-prefix}-arrow-from-left:after { content: replace(@fa-var-arrow-from-left, "\\\\", "\\\\10"); }
.fad.@{fa-css-prefix}-arrow-from-right:after { content: replace(@fa-var-arrow-from-right, "\\\\", "\\\\10"); }
.fad.@{fa-css-prefix}-arrow-from-top:after { content: replace(@fa-var-arrow-from-top, "\\\\", "\\\\10"); }
.fad.@{fa-css-prefix}-arrow-left:after { content: replace(@fa-var-arrow-left, "\\\\", "\\\\10"); }
.fad.@{fa-css-prefix}-arrow-right:after { content: replace(@fa-var-arrow-right, "\\\\", "\\\\10"); }
.fad.@{fa-css-prefix}-arrow-square-down:after { content: replace(@fa-var-arrow-square-down, "\\\\", "\\\\10"); }
.fad.@{fa-css-prefix}-arrow-square-left:after { content: replace(@fa-var-arrow-square-left, "\\\\", "\\\\10"); }
.fad.@{fa-css-prefix}-arrow-square-right:after { content: replace(@fa-var-arrow-square-right, "\\\\", "\\\\10"); }
.fad.@{fa-css-prefix}-arrow-square-up:after { content: replace(@fa-var-arrow-square-up, "\\\\", "\\\\10"); }
.fad.@{fa-css-prefix}-arrow-to-bottom:after { content: replace(@fa-var-arrow-to-bottom, "\\\\", "\\\\10"); }
.fad.@{fa-css-prefix}-arrow-to-left:after { content: replace(@fa-var-arrow-to-left, "\\\\", "\\\\10"); }
.fad.@{fa-css-prefix}-arrow-to-right:after { content: replace(@fa-var-arrow-to-right, "\\\\", "\\\\10"); }
.fad.@{fa-css-prefix}-arrow-to-top:after { content: replace(@fa-var-arrow-to-top, "\\\\", "\\\\10"); }
.fad.@{fa-css-prefix}-arrow-up:after { content: replace(@fa-var-arrow-up, "\\\\", "\\\\10"); }
.fad.@{fa-css-prefix}-arrows:after { content: replace(@fa-var-arrows, "\\\\", "\\\\10"); }
.fad.@{fa-css-prefix}-arrows-alt:after { content: replace(@fa-var-arrows-alt, "\\\\", "\\\\10"); }
.fad.@{fa-css-prefix}-arrows-alt-h:after { content: replace(@fa-var-arrows-alt-h, "\\\\", "\\\\10"); }
.fad.@{fa-css-prefix}-arrows-alt-v:after { content: replace(@fa-var-arrows-alt-v, "\\\\", "\\\\10"); }
.fad.@{fa-css-prefix}-arrows-h:after { content: replace(@fa-var-arrows-h, "\\\\", "\\\\10"); }
.fad.@{fa-css-prefix}-arrows-v:after { content: replace(@fa-var-arrows-v, "\\\\", "\\\\10"); }
.fad.@{fa-css-prefix}-assistive-listening-systems:after { content: replace(@fa-var-assistive-listening-systems, "\\\\", "\\\\10"); }
.fad.@{fa-css-prefix}-asterisk:after { content: replace(@fa-var-asterisk, "\\\\", "\\\\10"); }
.fad.@{fa-css-prefix}-at:after { content: replace(@fa-var-at, "\\\\", "\\\\10"); }
.fad.@{fa-css-prefix}-atlas:after { content: replace(@fa-var-atlas, "\\\\", "\\\\10"); }
.fad.@{fa-css-prefix}-atom:after { content: replace(@fa-var-atom, "\\\\", "\\\\10"); }
.fad.@{fa-css-prefix}-atom-alt:after { content: replace(@fa-var-atom-alt, "\\\\", "\\\\10"); }
.fad.@{fa-css-prefix}-audio-description:after { content: replace(@fa-var-audio-description, "\\\\", "\\\\10"); }
.fad.@{fa-css-prefix}-award:after { content: replace(@fa-var-award, "\\\\", "\\\\10"); }
.fad.@{fa-css-prefix}-axe:after { content: replace(@fa-var-axe, "\\\\", "\\\\10"); }
.fad.@{fa-css-prefix}-axe-battle:after { content: replace(@fa-var-axe-battle, "\\\\", "\\\\10"); }
.fad.@{fa-css-prefix}-baby:after { content: replace(@fa-var-baby, "\\\\", "\\\\10"); }
.fad.@{fa-css-prefix}-baby-carriage:after { content: replace(@fa-var-baby-carriage, "\\\\", "\\\\10"); }
.fad.@{fa-css-prefix}-backpack:after { content: replace(@fa-var-backpack, "\\\\", "\\\\10"); }
.fad.@{fa-css-prefix}-backspace:after { content: replace(@fa-var-backspace, "\\\\", "\\\\10"); }
.fad.@{fa-css-prefix}-backward:after { content: replace(@fa-var-backward, "\\\\", "\\\\10"); }
.fad.@{fa-css-prefix}-bacon:after { content: replace(@fa-var-bacon, "\\\\", "\\\\10"); }
.fad.@{fa-css-prefix}-badge:after { content: replace(@fa-var-badge, "\\\\", "\\\\10"); }
.fad.@{fa-css-prefix}-badge-check:after { content: replace(@fa-var-badge-check, "\\\\", "\\\\10"); }
.fad.@{fa-css-prefix}-badge-dollar:after { content: replace(@fa-var-badge-dollar, "\\\\", "\\\\10"); }
.fad.@{fa-css-prefix}-badge-percent:after { content: replace(@fa-var-badge-percent, "\\\\", "\\\\10"); }
.fad.@{fa-css-prefix}-badger-honey:after { content: replace(@fa-var-badger-honey, "\\\\", "\\\\10"); }
.fad.@{fa-css-prefix}-bags-shopping:after { content: replace(@fa-var-bags-shopping, "\\\\", "\\\\10"); }
.fad.@{fa-css-prefix}-balance-scale:after { content: replace(@fa-var-balance-scale, "\\\\", "\\\\10"); }
.fad.@{fa-css-prefix}-balance-scale-left:after { content: replace(@fa-var-balance-scale-left, "\\\\", "\\\\10"); }
.fad.@{fa-css-prefix}-balance-scale-right:after { content: replace(@fa-var-balance-scale-right, "\\\\", "\\\\10"); }
.fad.@{fa-css-prefix}-ball-pile:after { content: replace(@fa-var-ball-pile, "\\\\", "\\\\10"); }
.fad.@{fa-css-prefix}-ballot:after { content: replace(@fa-var-ballot, "\\\\", "\\\\10"); }
.fad.@{fa-css-prefix}-ballot-check:after { content: replace(@fa-var-ballot-check, "\\\\", "\\\\10"); }
.fad.@{fa-css-prefix}-ban:after { content: replace(@fa-var-ban, "\\\\", "\\\\10"); }
.fad.@{fa-css-prefix}-band-aid:after { content: replace(@fa-var-band-aid, "\\\\", "\\\\10"); }
.fad.@{fa-css-prefix}-barcode:after { content: replace(@fa-var-barcode, "\\\\", "\\\\10"); }
.fad.@{fa-css-prefix}-barcode-alt:after { content: replace(@fa-var-barcode-alt, "\\\\", "\\\\10"); }
.fad.@{fa-css-prefix}-barcode-read:after { content: replace(@fa-var-barcode-read, "\\\\", "\\\\10"); }
.fad.@{fa-css-prefix}-barcode-scan:after { content: replace(@fa-var-barcode-scan, "\\\\", "\\\\10"); }
.fad.@{fa-css-prefix}-bars:after { content: replace(@fa-var-bars, "\\\\", "\\\\10"); }
.fad.@{fa-css-prefix}-baseball:after { content: replace(@fa-var-baseball, "\\\\", "\\\\10"); }
.fad.@{fa-css-prefix}-baseball-ball:after { content: replace(@fa-var-baseball-ball, "\\\\", "\\\\10"); }
.fad.@{fa-css-prefix}-basketball-ball:after { content: replace(@fa-var-basketball-ball, "\\\\", "\\\\10"); }
.fad.@{fa-css-prefix}-basketball-hoop:after { content: replace(@fa-var-basketball-hoop, "\\\\", "\\\\10"); }
.fad.@{fa-css-prefix}-bat:after { content: replace(@fa-var-bat, "\\\\", "\\\\10"); }
.fad.@{fa-css-prefix}-bath:after { content: replace(@fa-var-bath, "\\\\", "\\\\10"); }
.fad.@{fa-css-prefix}-battery-bolt:after { content: replace(@fa-var-battery-bolt, "\\\\", "\\\\10"); }
.fad.@{fa-css-prefix}-battery-empty:after { content: replace(@fa-var-battery-empty, "\\\\", "\\\\10"); }
.fad.@{fa-css-prefix}-battery-full:after { content: replace(@fa-var-battery-full, "\\\\", "\\\\10"); }
.fad.@{fa-css-prefix}-battery-half:after { content: replace(@fa-var-battery-half, "\\\\", "\\\\10"); }
.fad.@{fa-css-prefix}-battery-quarter:after { content: replace(@fa-var-battery-quarter, "\\\\", "\\\\10"); }
.fad.@{fa-css-prefix}-battery-slash:after { content: replace(@fa-var-battery-slash, "\\\\", "\\\\10"); }
.fad.@{fa-css-prefix}-battery-three-quarters:after { content: replace(@fa-var-battery-three-quarters, "\\\\", "\\\\10"); }
.fad.@{fa-css-prefix}-bed:after { content: replace(@fa-var-bed, "\\\\", "\\\\10"); }
.fad.@{fa-css-prefix}-beer:after { content: replace(@fa-var-beer, "\\\\", "\\\\10"); }
.fad.@{fa-css-prefix}-bell:after { content: replace(@fa-var-bell, "\\\\", "\\\\10"); }
.fad.@{fa-css-prefix}-bell-exclamation:after { content: replace(@fa-var-bell-exclamation, "\\\\", "\\\\10"); }
.fad.@{fa-css-prefix}-bell-plus:after { content: replace(@fa-var-bell-plus, "\\\\", "\\\\10"); }
.fad.@{fa-css-prefix}-bell-school:after { content: replace(@fa-var-bell-school, "\\\\", "\\\\10"); }
.fad.@{fa-css-prefix}-bell-school-slash:after { content: replace(@fa-var-bell-school-slash, "\\\\", "\\\\10"); }
.fad.@{fa-css-prefix}-bell-slash:after { content: replace(@fa-var-bell-slash, "\\\\", "\\\\10"); }
.fad.@{fa-css-prefix}-bells:after { content: replace(@fa-var-bells, "\\\\", "\\\\10"); }
.fad.@{fa-css-prefix}-bezier-curve:after { content: replace(@fa-var-bezier-curve, "\\\\", "\\\\10"); }
.fad.@{fa-css-prefix}-bible:after { content: replace(@fa-var-bible, "\\\\", "\\\\10"); }
.fad.@{fa-css-prefix}-bicycle:after { content: replace(@fa-var-bicycle, "\\\\", "\\\\10"); }
.fad.@{fa-css-prefix}-biking:after { content: replace(@fa-var-biking, "\\\\", "\\\\10"); }
.fad.@{fa-css-prefix}-biking-mountain:after { content: replace(@fa-var-biking-mountain, "\\\\", "\\\\10"); }
.fad.@{fa-css-prefix}-binoculars:after { content: replace(@fa-var-binoculars, "\\\\", "\\\\10"); }
.fad.@{fa-css-prefix}-biohazard:after { content: replace(@fa-var-biohazard, "\\\\", "\\\\10"); }
.fad.@{fa-css-prefix}-birthday-cake:after { content: replace(@fa-var-birthday-cake, "\\\\", "\\\\10"); }
.fad.@{fa-css-prefix}-blanket:after { content: replace(@fa-var-blanket, "\\\\", "\\\\10"); }
.fad.@{fa-css-prefix}-blender:after { content: replace(@fa-var-blender, "\\\\", "\\\\10"); }
.fad.@{fa-css-prefix}-blender-phone:after { content: replace(@fa-var-blender-phone, "\\\\", "\\\\10"); }
.fad.@{fa-css-prefix}-blind:after { content: replace(@fa-var-blind, "\\\\", "\\\\10"); }
.fad.@{fa-css-prefix}-blog:after { content: replace(@fa-var-blog, "\\\\", "\\\\10"); }
.fad.@{fa-css-prefix}-bold:after { content: replace(@fa-var-bold, "\\\\", "\\\\10"); }
.fad.@{fa-css-prefix}-bolt:after { content: replace(@fa-var-bolt, "\\\\", "\\\\10"); }
.fad.@{fa-css-prefix}-bomb:after { content: replace(@fa-var-bomb, "\\\\", "\\\\10"); }
.fad.@{fa-css-prefix}-bone:after { content: replace(@fa-var-bone, "\\\\", "\\\\10"); }
.fad.@{fa-css-prefix}-bone-break:after { content: replace(@fa-var-bone-break, "\\\\", "\\\\10"); }
.fad.@{fa-css-prefix}-bong:after { content: replace(@fa-var-bong, "\\\\", "\\\\10"); }
.fad.@{fa-css-prefix}-book:after { content: replace(@fa-var-book, "\\\\", "\\\\10"); }
.fad.@{fa-css-prefix}-book-alt:after { content: replace(@fa-var-book-alt, "\\\\", "\\\\10"); }
.fad.@{fa-css-prefix}-book-dead:after { content: replace(@fa-var-book-dead, "\\\\", "\\\\10"); }
.fad.@{fa-css-prefix}-book-heart:after { content: replace(@fa-var-book-heart, "\\\\", "\\\\10"); }
.fad.@{fa-css-prefix}-book-medical:after { content: replace(@fa-var-book-medical, "\\\\", "\\\\10"); }
.fad.@{fa-css-prefix}-book-open:after { content: replace(@fa-var-book-open, "\\\\", "\\\\10"); }
.fad.@{fa-css-prefix}-book-reader:after { content: replace(@fa-var-book-reader, "\\\\", "\\\\10"); }
.fad.@{fa-css-prefix}-book-spells:after { content: replace(@fa-var-book-spells, "\\\\", "\\\\10"); }
.fad.@{fa-css-prefix}-book-user:after { content: replace(@fa-var-book-user, "\\\\", "\\\\10"); }
.fad.@{fa-css-prefix}-bookmark:after { content: replace(@fa-var-bookmark, "\\\\", "\\\\10"); }
.fad.@{fa-css-prefix}-books:after { content: replace(@fa-var-books, "\\\\", "\\\\10"); }
.fad.@{fa-css-prefix}-books-medical:after { content: replace(@fa-var-books-medical, "\\\\", "\\\\10"); }
.fad.@{fa-css-prefix}-boot:after { content: replace(@fa-var-boot, "\\\\", "\\\\10"); }
.fad.@{fa-css-prefix}-booth-curtain:after { content: replace(@fa-var-booth-curtain, "\\\\", "\\\\10"); }
.fad.@{fa-css-prefix}-border-all:after { content: replace(@fa-var-border-all, "\\\\", "\\\\10"); }
.fad.@{fa-css-prefix}-border-bottom:after { content: replace(@fa-var-border-bottom, "\\\\", "\\\\10"); }
.fad.@{fa-css-prefix}-border-inner:after { content: replace(@fa-var-border-inner, "\\\\", "\\\\10"); }
.fad.@{fa-css-prefix}-border-left:after { content: replace(@fa-var-border-left, "\\\\", "\\\\10"); }
.fad.@{fa-css-prefix}-border-none:after { content: replace(@fa-var-border-none, "\\\\", "\\\\10"); }
.fad.@{fa-css-prefix}-border-outer:after { content: replace(@fa-var-border-outer, "\\\\", "\\\\10"); }
.fad.@{fa-css-prefix}-border-right:after { content: replace(@fa-var-border-right, "\\\\", "\\\\10"); }
.fad.@{fa-css-prefix}-border-style:after { content: replace(@fa-var-border-style, "\\\\", "\\\\10"); }
.fad.@{fa-css-prefix}-border-style-alt:after { content: replace(@fa-var-border-style-alt, "\\\\", "\\\\10"); }
.fad.@{fa-css-prefix}-border-top:after { content: replace(@fa-var-border-top, "\\\\", "\\\\10"); }
.fad.@{fa-css-prefix}-bow-arrow:after { content: replace(@fa-var-bow-arrow, "\\\\", "\\\\10"); }
.fad.@{fa-css-prefix}-bowling-ball:after { content: replace(@fa-var-bowling-ball, "\\\\", "\\\\10"); }
.fad.@{fa-css-prefix}-bowling-pins:after { content: replace(@fa-var-bowling-pins, "\\\\", "\\\\10"); }
.fad.@{fa-css-prefix}-box:after { content: replace(@fa-var-box, "\\\\", "\\\\10"); }
.fad.@{fa-css-prefix}-box-alt:after { content: replace(@fa-var-box-alt, "\\\\", "\\\\10"); }
.fad.@{fa-css-prefix}-box-ballot:after { content: replace(@fa-var-box-ballot, "\\\\", "\\\\10"); }
.fad.@{fa-css-prefix}-box-check:after { content: replace(@fa-var-box-check, "\\\\", "\\\\10"); }
.fad.@{fa-css-prefix}-box-fragile:after { content: replace(@fa-var-box-fragile, "\\\\", "\\\\10"); }
.fad.@{fa-css-prefix}-box-full:after { content: replace(@fa-var-box-full, "\\\\", "\\\\10"); }
.fad.@{fa-css-prefix}-box-heart:after { content: replace(@fa-var-box-heart, "\\\\", "\\\\10"); }
.fad.@{fa-css-prefix}-box-open:after { content: replace(@fa-var-box-open, "\\\\", "\\\\10"); }
.fad.@{fa-css-prefix}-box-up:after { content: replace(@fa-var-box-up, "\\\\", "\\\\10"); }
.fad.@{fa-css-prefix}-box-usd:after { content: replace(@fa-var-box-usd, "\\\\", "\\\\10"); }
.fad.@{fa-css-prefix}-boxes:after { content: replace(@fa-var-boxes, "\\\\", "\\\\10"); }
.fad.@{fa-css-prefix}-boxes-alt:after { content: replace(@fa-var-boxes-alt, "\\\\", "\\\\10"); }
.fad.@{fa-css-prefix}-boxing-glove:after { content: replace(@fa-var-boxing-glove, "\\\\", "\\\\10"); }
.fad.@{fa-css-prefix}-brackets:after { content: replace(@fa-var-brackets, "\\\\", "\\\\10"); }
.fad.@{fa-css-prefix}-brackets-curly:after { content: replace(@fa-var-brackets-curly, "\\\\", "\\\\10"); }
.fad.@{fa-css-prefix}-braille:after { content: replace(@fa-var-braille, "\\\\", "\\\\10"); }
.fad.@{fa-css-prefix}-brain:after { content: replace(@fa-var-brain, "\\\\", "\\\\10"); }
.fad.@{fa-css-prefix}-bread-loaf:after { content: replace(@fa-var-bread-loaf, "\\\\", "\\\\10"); }
.fad.@{fa-css-prefix}-bread-slice:after { content: replace(@fa-var-bread-slice, "\\\\", "\\\\10"); }
.fad.@{fa-css-prefix}-briefcase:after { content: replace(@fa-var-briefcase, "\\\\", "\\\\10"); }
.fad.@{fa-css-prefix}-briefcase-medical:after { content: replace(@fa-var-briefcase-medical, "\\\\", "\\\\10"); }
.fad.@{fa-css-prefix}-bring-forward:after { content: replace(@fa-var-bring-forward, "\\\\", "\\\\10"); }
.fad.@{fa-css-prefix}-bring-front:after { content: replace(@fa-var-bring-front, "\\\\", "\\\\10"); }
.fad.@{fa-css-prefix}-broadcast-tower:after { content: replace(@fa-var-broadcast-tower, "\\\\", "\\\\10"); }
.fad.@{fa-css-prefix}-broom:after { content: replace(@fa-var-broom, "\\\\", "\\\\10"); }
.fad.@{fa-css-prefix}-browser:after { content: replace(@fa-var-browser, "\\\\", "\\\\10"); }
.fad.@{fa-css-prefix}-brush:after { content: replace(@fa-var-brush, "\\\\", "\\\\10"); }
.fad.@{fa-css-prefix}-bug:after { content: replace(@fa-var-bug, "\\\\", "\\\\10"); }
.fad.@{fa-css-prefix}-building:after { content: replace(@fa-var-building, "\\\\", "\\\\10"); }
.fad.@{fa-css-prefix}-bullhorn:after { content: replace(@fa-var-bullhorn, "\\\\", "\\\\10"); }
.fad.@{fa-css-prefix}-bullseye:after { content: replace(@fa-var-bullseye, "\\\\", "\\\\10"); }
.fad.@{fa-css-prefix}-bullseye-arrow:after { content: replace(@fa-var-bullseye-arrow, "\\\\", "\\\\10"); }
.fad.@{fa-css-prefix}-bullseye-pointer:after { content: replace(@fa-var-bullseye-pointer, "\\\\", "\\\\10"); }
.fad.@{fa-css-prefix}-burger-soda:after { content: replace(@fa-var-burger-soda, "\\\\", "\\\\10"); }
.fad.@{fa-css-prefix}-burn:after { content: replace(@fa-var-burn, "\\\\", "\\\\10"); }
.fad.@{fa-css-prefix}-burrito:after { content: replace(@fa-var-burrito, "\\\\", "\\\\10"); }
.fad.@{fa-css-prefix}-bus:after { content: replace(@fa-var-bus, "\\\\", "\\\\10"); }
.fad.@{fa-css-prefix}-bus-alt:after { content: replace(@fa-var-bus-alt, "\\\\", "\\\\10"); }
.fad.@{fa-css-prefix}-bus-school:after { content: replace(@fa-var-bus-school, "\\\\", "\\\\10"); }
.fad.@{fa-css-prefix}-business-time:after { content: replace(@fa-var-business-time, "\\\\", "\\\\10"); }
.fad.@{fa-css-prefix}-cabinet-filing:after { content: replace(@fa-var-cabinet-filing, "\\\\", "\\\\10"); }
.fad.@{fa-css-prefix}-calculator:after { content: replace(@fa-var-calculator, "\\\\", "\\\\10"); }
.fad.@{fa-css-prefix}-calculator-alt:after { content: replace(@fa-var-calculator-alt, "\\\\", "\\\\10"); }
.fad.@{fa-css-prefix}-calendar:after { content: replace(@fa-var-calendar, "\\\\", "\\\\10"); }
.fad.@{fa-css-prefix}-calendar-alt:after { content: replace(@fa-var-calendar-alt, "\\\\", "\\\\10"); }
.fad.@{fa-css-prefix}-calendar-check:after { content: replace(@fa-var-calendar-check, "\\\\", "\\\\10"); }
.fad.@{fa-css-prefix}-calendar-day:after { content: replace(@fa-var-calendar-day, "\\\\", "\\\\10"); }
.fad.@{fa-css-prefix}-calendar-edit:after { content: replace(@fa-var-calendar-edit, "\\\\", "\\\\10"); }
.fad.@{fa-css-prefix}-calendar-exclamation:after { content: replace(@fa-var-calendar-exclamation, "\\\\", "\\\\10"); }
.fad.@{fa-css-prefix}-calendar-minus:after { content: replace(@fa-var-calendar-minus, "\\\\", "\\\\10"); }
.fad.@{fa-css-prefix}-calendar-plus:after { content: replace(@fa-var-calendar-plus, "\\\\", "\\\\10"); }
.fad.@{fa-css-prefix}-calendar-star:after { content: replace(@fa-var-calendar-star, "\\\\", "\\\\10"); }
.fad.@{fa-css-prefix}-calendar-times:after { content: replace(@fa-var-calendar-times, "\\\\", "\\\\10"); }
.fad.@{fa-css-prefix}-calendar-week:after { content: replace(@fa-var-calendar-week, "\\\\", "\\\\10"); }
.fad.@{fa-css-prefix}-camera:after { content: replace(@fa-var-camera, "\\\\", "\\\\10"); }
.fad.@{fa-css-prefix}-camera-alt:after { content: replace(@fa-var-camera-alt, "\\\\", "\\\\10"); }
.fad.@{fa-css-prefix}-camera-retro:after { content: replace(@fa-var-camera-retro, "\\\\", "\\\\10"); }
.fad.@{fa-css-prefix}-campfire:after { content: replace(@fa-var-campfire, "\\\\", "\\\\10"); }
.fad.@{fa-css-prefix}-campground:after { content: replace(@fa-var-campground, "\\\\", "\\\\10"); }
.fad.@{fa-css-prefix}-candle-holder:after { content: replace(@fa-var-candle-holder, "\\\\", "\\\\10"); }
.fad.@{fa-css-prefix}-candy-cane:after { content: replace(@fa-var-candy-cane, "\\\\", "\\\\10"); }
.fad.@{fa-css-prefix}-candy-corn:after { content: replace(@fa-var-candy-corn, "\\\\", "\\\\10"); }
.fad.@{fa-css-prefix}-cannabis:after { content: replace(@fa-var-cannabis, "\\\\", "\\\\10"); }
.fad.@{fa-css-prefix}-capsules:after { content: replace(@fa-var-capsules, "\\\\", "\\\\10"); }
.fad.@{fa-css-prefix}-car:after { content: replace(@fa-var-car, "\\\\", "\\\\10"); }
.fad.@{fa-css-prefix}-car-alt:after { content: replace(@fa-var-car-alt, "\\\\", "\\\\10"); }
.fad.@{fa-css-prefix}-car-battery:after { content: replace(@fa-var-car-battery, "\\\\", "\\\\10"); }
.fad.@{fa-css-prefix}-car-building:after { content: replace(@fa-var-car-building, "\\\\", "\\\\10"); }
.fad.@{fa-css-prefix}-car-bump:after { content: replace(@fa-var-car-bump, "\\\\", "\\\\10"); }
.fad.@{fa-css-prefix}-car-bus:after { content: replace(@fa-var-car-bus, "\\\\", "\\\\10"); }
.fad.@{fa-css-prefix}-car-crash:after { content: replace(@fa-var-car-crash, "\\\\", "\\\\10"); }
.fad.@{fa-css-prefix}-car-garage:after { content: replace(@fa-var-car-garage, "\\\\", "\\\\10"); }
.fad.@{fa-css-prefix}-car-mechanic:after { content: replace(@fa-var-car-mechanic, "\\\\", "\\\\10"); }
.fad.@{fa-css-prefix}-car-side:after { content: replace(@fa-var-car-side, "\\\\", "\\\\10"); }
.fad.@{fa-css-prefix}-car-tilt:after { content: replace(@fa-var-car-tilt, "\\\\", "\\\\10"); }
.fad.@{fa-css-prefix}-car-wash:after { content: replace(@fa-var-car-wash, "\\\\", "\\\\10"); }
.fad.@{fa-css-prefix}-caret-circle-down:after { content: replace(@fa-var-caret-circle-down, "\\\\", "\\\\10"); }
.fad.@{fa-css-prefix}-caret-circle-left:after { content: replace(@fa-var-caret-circle-left, "\\\\", "\\\\10"); }
.fad.@{fa-css-prefix}-caret-circle-right:after { content: replace(@fa-var-caret-circle-right, "\\\\", "\\\\10"); }
.fad.@{fa-css-prefix}-caret-circle-up:after { content: replace(@fa-var-caret-circle-up, "\\\\", "\\\\10"); }
.fad.@{fa-css-prefix}-caret-down:after { content: replace(@fa-var-caret-down, "\\\\", "\\\\10"); }
.fad.@{fa-css-prefix}-caret-left:after { content: replace(@fa-var-caret-left, "\\\\", "\\\\10"); }
.fad.@{fa-css-prefix}-caret-right:after { content: replace(@fa-var-caret-right, "\\\\", "\\\\10"); }
.fad.@{fa-css-prefix}-caret-square-down:after { content: replace(@fa-var-caret-square-down, "\\\\", "\\\\10"); }
.fad.@{fa-css-prefix}-caret-square-left:after { content: replace(@fa-var-caret-square-left, "\\\\", "\\\\10"); }
.fad.@{fa-css-prefix}-caret-square-right:after { content: replace(@fa-var-caret-square-right, "\\\\", "\\\\10"); }
.fad.@{fa-css-prefix}-caret-square-up:after { content: replace(@fa-var-caret-square-up, "\\\\", "\\\\10"); }
.fad.@{fa-css-prefix}-caret-up:after { content: replace(@fa-var-caret-up, "\\\\", "\\\\10"); }
.fad.@{fa-css-prefix}-carrot:after { content: replace(@fa-var-carrot, "\\\\", "\\\\10"); }
.fad.@{fa-css-prefix}-cars:after { content: replace(@fa-var-cars, "\\\\", "\\\\10"); }
.fad.@{fa-css-prefix}-cart-arrow-down:after { content: replace(@fa-var-cart-arrow-down, "\\\\", "\\\\10"); }
.fad.@{fa-css-prefix}-cart-plus:after { content: replace(@fa-var-cart-plus, "\\\\", "\\\\10"); }
.fad.@{fa-css-prefix}-cash-register:after { content: replace(@fa-var-cash-register, "\\\\", "\\\\10"); }
.fad.@{fa-css-prefix}-cat:after { content: replace(@fa-var-cat, "\\\\", "\\\\10"); }
.fad.@{fa-css-prefix}-cauldron:after { content: replace(@fa-var-cauldron, "\\\\", "\\\\10"); }
.fad.@{fa-css-prefix}-certificate:after { content: replace(@fa-var-certificate, "\\\\", "\\\\10"); }
.fad.@{fa-css-prefix}-chair:after { content: replace(@fa-var-chair, "\\\\", "\\\\10"); }
.fad.@{fa-css-prefix}-chair-office:after { content: replace(@fa-var-chair-office, "\\\\", "\\\\10"); }
.fad.@{fa-css-prefix}-chalkboard:after { content: replace(@fa-var-chalkboard, "\\\\", "\\\\10"); }
.fad.@{fa-css-prefix}-chalkboard-teacher:after { content: replace(@fa-var-chalkboard-teacher, "\\\\", "\\\\10"); }
.fad.@{fa-css-prefix}-charging-station:after { content: replace(@fa-var-charging-station, "\\\\", "\\\\10"); }
.fad.@{fa-css-prefix}-chart-area:after { content: replace(@fa-var-chart-area, "\\\\", "\\\\10"); }
.fad.@{fa-css-prefix}-chart-bar:after { content: replace(@fa-var-chart-bar, "\\\\", "\\\\10"); }
.fad.@{fa-css-prefix}-chart-line:after { content: replace(@fa-var-chart-line, "\\\\", "\\\\10"); }
.fad.@{fa-css-prefix}-chart-line-down:after { content: replace(@fa-var-chart-line-down, "\\\\", "\\\\10"); }
.fad.@{fa-css-prefix}-chart-network:after { content: replace(@fa-var-chart-network, "\\\\", "\\\\10"); }
.fad.@{fa-css-prefix}-chart-pie:after { content: replace(@fa-var-chart-pie, "\\\\", "\\\\10"); }
.fad.@{fa-css-prefix}-chart-pie-alt:after { content: replace(@fa-var-chart-pie-alt, "\\\\", "\\\\10"); }
.fad.@{fa-css-prefix}-chart-scatter:after { content: replace(@fa-var-chart-scatter, "\\\\", "\\\\10"); }
.fad.@{fa-css-prefix}-check:after { content: replace(@fa-var-check, "\\\\", "\\\\10"); }
.fad.@{fa-css-prefix}-check-circle:after { content: replace(@fa-var-check-circle, "\\\\", "\\\\10"); }
.fad.@{fa-css-prefix}-check-double:after { content: replace(@fa-var-check-double, "\\\\", "\\\\10"); }
.fad.@{fa-css-prefix}-check-square:after { content: replace(@fa-var-check-square, "\\\\", "\\\\10"); }
.fad.@{fa-css-prefix}-cheese:after { content: replace(@fa-var-cheese, "\\\\", "\\\\10"); }
.fad.@{fa-css-prefix}-cheese-swiss:after { content: replace(@fa-var-cheese-swiss, "\\\\", "\\\\10"); }
.fad.@{fa-css-prefix}-cheeseburger:after { content: replace(@fa-var-cheeseburger, "\\\\", "\\\\10"); }
.fad.@{fa-css-prefix}-chess:after { content: replace(@fa-var-chess, "\\\\", "\\\\10"); }
.fad.@{fa-css-prefix}-chess-bishop:after { content: replace(@fa-var-chess-bishop, "\\\\", "\\\\10"); }
.fad.@{fa-css-prefix}-chess-bishop-alt:after { content: replace(@fa-var-chess-bishop-alt, "\\\\", "\\\\10"); }
.fad.@{fa-css-prefix}-chess-board:after { content: replace(@fa-var-chess-board, "\\\\", "\\\\10"); }
.fad.@{fa-css-prefix}-chess-clock:after { content: replace(@fa-var-chess-clock, "\\\\", "\\\\10"); }
.fad.@{fa-css-prefix}-chess-clock-alt:after { content: replace(@fa-var-chess-clock-alt, "\\\\", "\\\\10"); }
.fad.@{fa-css-prefix}-chess-king:after { content: replace(@fa-var-chess-king, "\\\\", "\\\\10"); }
.fad.@{fa-css-prefix}-chess-king-alt:after { content: replace(@fa-var-chess-king-alt, "\\\\", "\\\\10"); }
.fad.@{fa-css-prefix}-chess-knight:after { content: replace(@fa-var-chess-knight, "\\\\", "\\\\10"); }
.fad.@{fa-css-prefix}-chess-knight-alt:after { content: replace(@fa-var-chess-knight-alt, "\\\\", "\\\\10"); }
.fad.@{fa-css-prefix}-chess-pawn:after { content: replace(@fa-var-chess-pawn, "\\\\", "\\\\10"); }
.fad.@{fa-css-prefix}-chess-pawn-alt:after { content: replace(@fa-var-chess-pawn-alt, "\\\\", "\\\\10"); }
.fad.@{fa-css-prefix}-chess-queen:after { content: replace(@fa-var-chess-queen, "\\\\", "\\\\10"); }
.fad.@{fa-css-prefix}-chess-queen-alt:after { content: replace(@fa-var-chess-queen-alt, "\\\\", "\\\\10"); }
.fad.@{fa-css-prefix}-chess-rook:after { content: replace(@fa-var-chess-rook, "\\\\", "\\\\10"); }
.fad.@{fa-css-prefix}-chess-rook-alt:after { content: replace(@fa-var-chess-rook-alt, "\\\\", "\\\\10"); }
.fad.@{fa-css-prefix}-chevron-circle-down:after { content: replace(@fa-var-chevron-circle-down, "\\\\", "\\\\10"); }
.fad.@{fa-css-prefix}-chevron-circle-left:after { content: replace(@fa-var-chevron-circle-left, "\\\\", "\\\\10"); }
.fad.@{fa-css-prefix}-chevron-circle-right:after { content: replace(@fa-var-chevron-circle-right, "\\\\", "\\\\10"); }
.fad.@{fa-css-prefix}-chevron-circle-up:after { content: replace(@fa-var-chevron-circle-up, "\\\\", "\\\\10"); }
.fad.@{fa-css-prefix}-chevron-double-down:after { content: replace(@fa-var-chevron-double-down, "\\\\", "\\\\10"); }
.fad.@{fa-css-prefix}-chevron-double-left:after { content: replace(@fa-var-chevron-double-left, "\\\\", "\\\\10"); }
.fad.@{fa-css-prefix}-chevron-double-right:after { content: replace(@fa-var-chevron-double-right, "\\\\", "\\\\10"); }
.fad.@{fa-css-prefix}-chevron-double-up:after { content: replace(@fa-var-chevron-double-up, "\\\\", "\\\\10"); }
.fad.@{fa-css-prefix}-chevron-down:after { content: replace(@fa-var-chevron-down, "\\\\", "\\\\10"); }
.fad.@{fa-css-prefix}-chevron-left:after { content: replace(@fa-var-chevron-left, "\\\\", "\\\\10"); }
.fad.@{fa-css-prefix}-chevron-right:after { content: replace(@fa-var-chevron-right, "\\\\", "\\\\10"); }
.fad.@{fa-css-prefix}-chevron-square-down:after { content: replace(@fa-var-chevron-square-down, "\\\\", "\\\\10"); }
.fad.@{fa-css-prefix}-chevron-square-left:after { content: replace(@fa-var-chevron-square-left, "\\\\", "\\\\10"); }
.fad.@{fa-css-prefix}-chevron-square-right:after { content: replace(@fa-var-chevron-square-right, "\\\\", "\\\\10"); }
.fad.@{fa-css-prefix}-chevron-square-up:after { content: replace(@fa-var-chevron-square-up, "\\\\", "\\\\10"); }
.fad.@{fa-css-prefix}-chevron-up:after { content: replace(@fa-var-chevron-up, "\\\\", "\\\\10"); }
.fad.@{fa-css-prefix}-child:after { content: replace(@fa-var-child, "\\\\", "\\\\10"); }
.fad.@{fa-css-prefix}-chimney:after { content: replace(@fa-var-chimney, "\\\\", "\\\\10"); }
.fad.@{fa-css-prefix}-church:after { content: replace(@fa-var-church, "\\\\", "\\\\10"); }
.fad.@{fa-css-prefix}-circle:after { content: replace(@fa-var-circle, "\\\\", "\\\\10"); }
.fad.@{fa-css-prefix}-circle-notch:after { content: replace(@fa-var-circle-notch, "\\\\", "\\\\10"); }
.fad.@{fa-css-prefix}-city:after { content: replace(@fa-var-city, "\\\\", "\\\\10"); }
.fad.@{fa-css-prefix}-claw-marks:after { content: replace(@fa-var-claw-marks, "\\\\", "\\\\10"); }
.fad.@{fa-css-prefix}-clinic-medical:after { content: replace(@fa-var-clinic-medical, "\\\\", "\\\\10"); }
.fad.@{fa-css-prefix}-clipboard:after { content: replace(@fa-var-clipboard, "\\\\", "\\\\10"); }
.fad.@{fa-css-prefix}-clipboard-check:after { content: replace(@fa-var-clipboard-check, "\\\\", "\\\\10"); }
.fad.@{fa-css-prefix}-clipboard-list:after { content: replace(@fa-var-clipboard-list, "\\\\", "\\\\10"); }
.fad.@{fa-css-prefix}-clipboard-list-check:after { content: replace(@fa-var-clipboard-list-check, "\\\\", "\\\\10"); }
.fad.@{fa-css-prefix}-clipboard-prescription:after { content: replace(@fa-var-clipboard-prescription, "\\\\", "\\\\10"); }
.fad.@{fa-css-prefix}-clipboard-user:after { content: replace(@fa-var-clipboard-user, "\\\\", "\\\\10"); }
.fad.@{fa-css-prefix}-clock:after { content: replace(@fa-var-clock, "\\\\", "\\\\10"); }
.fad.@{fa-css-prefix}-clone:after { content: replace(@fa-var-clone, "\\\\", "\\\\10"); }
.fad.@{fa-css-prefix}-closed-captioning:after { content: replace(@fa-var-closed-captioning, "\\\\", "\\\\10"); }
.fad.@{fa-css-prefix}-cloud:after { content: replace(@fa-var-cloud, "\\\\", "\\\\10"); }
.fad.@{fa-css-prefix}-cloud-download:after { content: replace(@fa-var-cloud-download, "\\\\", "\\\\10"); }
.fad.@{fa-css-prefix}-cloud-download-alt:after { content: replace(@fa-var-cloud-download-alt, "\\\\", "\\\\10"); }
.fad.@{fa-css-prefix}-cloud-drizzle:after { content: replace(@fa-var-cloud-drizzle, "\\\\", "\\\\10"); }
.fad.@{fa-css-prefix}-cloud-hail:after { content: replace(@fa-var-cloud-hail, "\\\\", "\\\\10"); }
.fad.@{fa-css-prefix}-cloud-hail-mixed:after { content: replace(@fa-var-cloud-hail-mixed, "\\\\", "\\\\10"); }
.fad.@{fa-css-prefix}-cloud-meatball:after { content: replace(@fa-var-cloud-meatball, "\\\\", "\\\\10"); }
.fad.@{fa-css-prefix}-cloud-moon:after { content: replace(@fa-var-cloud-moon, "\\\\", "\\\\10"); }
.fad.@{fa-css-prefix}-cloud-moon-rain:after { content: replace(@fa-var-cloud-moon-rain, "\\\\", "\\\\10"); }
.fad.@{fa-css-prefix}-cloud-rain:after { content: replace(@fa-var-cloud-rain, "\\\\", "\\\\10"); }
.fad.@{fa-css-prefix}-cloud-rainbow:after { content: replace(@fa-var-cloud-rainbow, "\\\\", "\\\\10"); }
.fad.@{fa-css-prefix}-cloud-showers:after { content: replace(@fa-var-cloud-showers, "\\\\", "\\\\10"); }
.fad.@{fa-css-prefix}-cloud-showers-heavy:after { content: replace(@fa-var-cloud-showers-heavy, "\\\\", "\\\\10"); }
.fad.@{fa-css-prefix}-cloud-sleet:after { content: replace(@fa-var-cloud-sleet, "\\\\", "\\\\10"); }
.fad.@{fa-css-prefix}-cloud-snow:after { content: replace(@fa-var-cloud-snow, "\\\\", "\\\\10"); }
.fad.@{fa-css-prefix}-cloud-sun:after { content: replace(@fa-var-cloud-sun, "\\\\", "\\\\10"); }
.fad.@{fa-css-prefix}-cloud-sun-rain:after { content: replace(@fa-var-cloud-sun-rain, "\\\\", "\\\\10"); }
.fad.@{fa-css-prefix}-cloud-upload:after { content: replace(@fa-var-cloud-upload, "\\\\", "\\\\10"); }
.fad.@{fa-css-prefix}-cloud-upload-alt:after { content: replace(@fa-var-cloud-upload-alt, "\\\\", "\\\\10"); }
.fad.@{fa-css-prefix}-clouds:after { content: replace(@fa-var-clouds, "\\\\", "\\\\10"); }
.fad.@{fa-css-prefix}-clouds-moon:after { content: replace(@fa-var-clouds-moon, "\\\\", "\\\\10"); }
.fad.@{fa-css-prefix}-clouds-sun:after { content: replace(@fa-var-clouds-sun, "\\\\", "\\\\10"); }
.fad.@{fa-css-prefix}-club:after { content: replace(@fa-var-club, "\\\\", "\\\\10"); }
.fad.@{fa-css-prefix}-cocktail:after { content: replace(@fa-var-cocktail, "\\\\", "\\\\10"); }
.fad.@{fa-css-prefix}-code:after { content: replace(@fa-var-code, "\\\\", "\\\\10"); }
.fad.@{fa-css-prefix}-code-branch:after { content: replace(@fa-var-code-branch, "\\\\", "\\\\10"); }
.fad.@{fa-css-prefix}-code-commit:after { content: replace(@fa-var-code-commit, "\\\\", "\\\\10"); }
.fad.@{fa-css-prefix}-code-merge:after { content: replace(@fa-var-code-merge, "\\\\", "\\\\10"); }
.fad.@{fa-css-prefix}-coffee:after { content: replace(@fa-var-coffee, "\\\\", "\\\\10"); }
.fad.@{fa-css-prefix}-coffee-togo:after { content: replace(@fa-var-coffee-togo, "\\\\", "\\\\10"); }
.fad.@{fa-css-prefix}-coffin:after { content: replace(@fa-var-coffin, "\\\\", "\\\\10"); }
.fad.@{fa-css-prefix}-cog:after { content: replace(@fa-var-cog, "\\\\", "\\\\10"); }
.fad.@{fa-css-prefix}-cogs:after { content: replace(@fa-var-cogs, "\\\\", "\\\\10"); }
.fad.@{fa-css-prefix}-coin:after { content: replace(@fa-var-coin, "\\\\", "\\\\10"); }
.fad.@{fa-css-prefix}-coins:after { content: replace(@fa-var-coins, "\\\\", "\\\\10"); }
.fad.@{fa-css-prefix}-columns:after { content: replace(@fa-var-columns, "\\\\", "\\\\10"); }
.fad.@{fa-css-prefix}-comment:after { content: replace(@fa-var-comment, "\\\\", "\\\\10"); }
.fad.@{fa-css-prefix}-comment-alt:after { content: replace(@fa-var-comment-alt, "\\\\", "\\\\10"); }
.fad.@{fa-css-prefix}-comment-alt-check:after { content: replace(@fa-var-comment-alt-check, "\\\\", "\\\\10"); }
.fad.@{fa-css-prefix}-comment-alt-dollar:after { content: replace(@fa-var-comment-alt-dollar, "\\\\", "\\\\10"); }
.fad.@{fa-css-prefix}-comment-alt-dots:after { content: replace(@fa-var-comment-alt-dots, "\\\\", "\\\\10"); }
.fad.@{fa-css-prefix}-comment-alt-edit:after { content: replace(@fa-var-comment-alt-edit, "\\\\", "\\\\10"); }
.fad.@{fa-css-prefix}-comment-alt-exclamation:after { content: replace(@fa-var-comment-alt-exclamation, "\\\\", "\\\\10"); }
.fad.@{fa-css-prefix}-comment-alt-lines:after { content: replace(@fa-var-comment-alt-lines, "\\\\", "\\\\10"); }
.fad.@{fa-css-prefix}-comment-alt-medical:after { content: replace(@fa-var-comment-alt-medical, "\\\\", "\\\\10"); }
.fad.@{fa-css-prefix}-comment-alt-minus:after { content: replace(@fa-var-comment-alt-minus, "\\\\", "\\\\10"); }
.fad.@{fa-css-prefix}-comment-alt-plus:after { content: replace(@fa-var-comment-alt-plus, "\\\\", "\\\\10"); }
.fad.@{fa-css-prefix}-comment-alt-slash:after { content: replace(@fa-var-comment-alt-slash, "\\\\", "\\\\10"); }
.fad.@{fa-css-prefix}-comment-alt-smile:after { content: replace(@fa-var-comment-alt-smile, "\\\\", "\\\\10"); }
.fad.@{fa-css-prefix}-comment-alt-times:after { content: replace(@fa-var-comment-alt-times, "\\\\", "\\\\10"); }
.fad.@{fa-css-prefix}-comment-check:after { content: replace(@fa-var-comment-check, "\\\\", "\\\\10"); }
.fad.@{fa-css-prefix}-comment-dollar:after { content: replace(@fa-var-comment-dollar, "\\\\", "\\\\10"); }
.fad.@{fa-css-prefix}-comment-dots:after { content: replace(@fa-var-comment-dots, "\\\\", "\\\\10"); }
.fad.@{fa-css-prefix}-comment-edit:after { content: replace(@fa-var-comment-edit, "\\\\", "\\\\10"); }
.fad.@{fa-css-prefix}-comment-exclamation:after { content: replace(@fa-var-comment-exclamation, "\\\\", "\\\\10"); }
.fad.@{fa-css-prefix}-comment-lines:after { content: replace(@fa-var-comment-lines, "\\\\", "\\\\10"); }
.fad.@{fa-css-prefix}-comment-medical:after { content: replace(@fa-var-comment-medical, "\\\\", "\\\\10"); }
.fad.@{fa-css-prefix}-comment-minus:after { content: replace(@fa-var-comment-minus, "\\\\", "\\\\10"); }
.fad.@{fa-css-prefix}-comment-plus:after { content: replace(@fa-var-comment-plus, "\\\\", "\\\\10"); }
.fad.@{fa-css-prefix}-comment-slash:after { content: replace(@fa-var-comment-slash, "\\\\", "\\\\10"); }
.fad.@{fa-css-prefix}-comment-smile:after { content: replace(@fa-var-comment-smile, "\\\\", "\\\\10"); }
.fad.@{fa-css-prefix}-comment-times:after { content: replace(@fa-var-comment-times, "\\\\", "\\\\10"); }
.fad.@{fa-css-prefix}-comments:after { content: replace(@fa-var-comments, "\\\\", "\\\\10"); }
.fad.@{fa-css-prefix}-comments-alt:after { content: replace(@fa-var-comments-alt, "\\\\", "\\\\10"); }
.fad.@{fa-css-prefix}-comments-alt-dollar:after { content: replace(@fa-var-comments-alt-dollar, "\\\\", "\\\\10"); }
.fad.@{fa-css-prefix}-comments-dollar:after { content: replace(@fa-var-comments-dollar, "\\\\", "\\\\10"); }
.fad.@{fa-css-prefix}-compact-disc:after { content: replace(@fa-var-compact-disc, "\\\\", "\\\\10"); }
.fad.@{fa-css-prefix}-compass:after { content: replace(@fa-var-compass, "\\\\", "\\\\10"); }
.fad.@{fa-css-prefix}-compass-slash:after { content: replace(@fa-var-compass-slash, "\\\\", "\\\\10"); }
.fad.@{fa-css-prefix}-compress:after { content: replace(@fa-var-compress, "\\\\", "\\\\10"); }
.fad.@{fa-css-prefix}-compress-alt:after { content: replace(@fa-var-compress-alt, "\\\\", "\\\\10"); }
.fad.@{fa-css-prefix}-compress-arrows-alt:after { content: replace(@fa-var-compress-arrows-alt, "\\\\", "\\\\10"); }
.fad.@{fa-css-prefix}-compress-wide:after { content: replace(@fa-var-compress-wide, "\\\\", "\\\\10"); }
.fad.@{fa-css-prefix}-concierge-bell:after { content: replace(@fa-var-concierge-bell, "\\\\", "\\\\10"); }
.fad.@{fa-css-prefix}-construction:after { content: replace(@fa-var-construction, "\\\\", "\\\\10"); }
.fad.@{fa-css-prefix}-container-storage:after { content: replace(@fa-var-container-storage, "\\\\", "\\\\10"); }
.fad.@{fa-css-prefix}-conveyor-belt:after { content: replace(@fa-var-conveyor-belt, "\\\\", "\\\\10"); }
.fad.@{fa-css-prefix}-conveyor-belt-alt:after { content: replace(@fa-var-conveyor-belt-alt, "\\\\", "\\\\10"); }
.fad.@{fa-css-prefix}-cookie:after { content: replace(@fa-var-cookie, "\\\\", "\\\\10"); }
.fad.@{fa-css-prefix}-cookie-bite:after { content: replace(@fa-var-cookie-bite, "\\\\", "\\\\10"); }
.fad.@{fa-css-prefix}-copy:after { content: replace(@fa-var-copy, "\\\\", "\\\\10"); }
.fad.@{fa-css-prefix}-copyright:after { content: replace(@fa-var-copyright, "\\\\", "\\\\10"); }
.fad.@{fa-css-prefix}-corn:after { content: replace(@fa-var-corn, "\\\\", "\\\\10"); }
.fad.@{fa-css-prefix}-couch:after { content: replace(@fa-var-couch, "\\\\", "\\\\10"); }
.fad.@{fa-css-prefix}-cow:after { content: replace(@fa-var-cow, "\\\\", "\\\\10"); }
.fad.@{fa-css-prefix}-credit-card:after { content: replace(@fa-var-credit-card, "\\\\", "\\\\10"); }
.fad.@{fa-css-prefix}-credit-card-blank:after { content: replace(@fa-var-credit-card-blank, "\\\\", "\\\\10"); }
.fad.@{fa-css-prefix}-credit-card-front:after { content: replace(@fa-var-credit-card-front, "\\\\", "\\\\10"); }
.fad.@{fa-css-prefix}-cricket:after { content: replace(@fa-var-cricket, "\\\\", "\\\\10"); }
.fad.@{fa-css-prefix}-croissant:after { content: replace(@fa-var-croissant, "\\\\", "\\\\10"); }
.fad.@{fa-css-prefix}-crop:after { content: replace(@fa-var-crop, "\\\\", "\\\\10"); }
.fad.@{fa-css-prefix}-crop-alt:after { content: replace(@fa-var-crop-alt, "\\\\", "\\\\10"); }
.fad.@{fa-css-prefix}-cross:after { content: replace(@fa-var-cross, "\\\\", "\\\\10"); }
.fad.@{fa-css-prefix}-crosshairs:after { content: replace(@fa-var-crosshairs, "\\\\", "\\\\10"); }
.fad.@{fa-css-prefix}-crow:after { content: replace(@fa-var-crow, "\\\\", "\\\\10"); }
.fad.@{fa-css-prefix}-crown:after { content: replace(@fa-var-crown, "\\\\", "\\\\10"); }
.fad.@{fa-css-prefix}-crutch:after { content: replace(@fa-var-crutch, "\\\\", "\\\\10"); }
.fad.@{fa-css-prefix}-crutches:after { content: replace(@fa-var-crutches, "\\\\", "\\\\10"); }
.fad.@{fa-css-prefix}-cube:after { content: replace(@fa-var-cube, "\\\\", "\\\\10"); }
.fad.@{fa-css-prefix}-cubes:after { content: replace(@fa-var-cubes, "\\\\", "\\\\10"); }
.fad.@{fa-css-prefix}-curling:after { content: replace(@fa-var-curling, "\\\\", "\\\\10"); }
.fad.@{fa-css-prefix}-cut:after { content: replace(@fa-var-cut, "\\\\", "\\\\10"); }
.fad.@{fa-css-prefix}-dagger:after { content: replace(@fa-var-dagger, "\\\\", "\\\\10"); }
.fad.@{fa-css-prefix}-database:after { content: replace(@fa-var-database, "\\\\", "\\\\10"); }
.fad.@{fa-css-prefix}-deaf:after { content: replace(@fa-var-deaf, "\\\\", "\\\\10"); }
.fad.@{fa-css-prefix}-debug:after { content: replace(@fa-var-debug, "\\\\", "\\\\10"); }
.fad.@{fa-css-prefix}-deer:after { content: replace(@fa-var-deer, "\\\\", "\\\\10"); }
.fad.@{fa-css-prefix}-deer-rudolph:after { content: replace(@fa-var-deer-rudolph, "\\\\", "\\\\10"); }
.fad.@{fa-css-prefix}-democrat:after { content: replace(@fa-var-democrat, "\\\\", "\\\\10"); }
.fad.@{fa-css-prefix}-desktop:after { content: replace(@fa-var-desktop, "\\\\", "\\\\10"); }
.fad.@{fa-css-prefix}-desktop-alt:after { content: replace(@fa-var-desktop-alt, "\\\\", "\\\\10"); }
.fad.@{fa-css-prefix}-dewpoint:after { content: replace(@fa-var-dewpoint, "\\\\", "\\\\10"); }
.fad.@{fa-css-prefix}-dharmachakra:after { content: replace(@fa-var-dharmachakra, "\\\\", "\\\\10"); }
.fad.@{fa-css-prefix}-diagnoses:after { content: replace(@fa-var-diagnoses, "\\\\", "\\\\10"); }
.fad.@{fa-css-prefix}-diamond:after { content: replace(@fa-var-diamond, "\\\\", "\\\\10"); }
.fad.@{fa-css-prefix}-dice:after { content: replace(@fa-var-dice, "\\\\", "\\\\10"); }
.fad.@{fa-css-prefix}-dice-d10:after { content: replace(@fa-var-dice-d10, "\\\\", "\\\\10"); }
.fad.@{fa-css-prefix}-dice-d12:after { content: replace(@fa-var-dice-d12, "\\\\", "\\\\10"); }
.fad.@{fa-css-prefix}-dice-d20:after { content: replace(@fa-var-dice-d20, "\\\\", "\\\\10"); }
.fad.@{fa-css-prefix}-dice-d4:after { content: replace(@fa-var-dice-d4, "\\\\", "\\\\10"); }
.fad.@{fa-css-prefix}-dice-d6:after { content: replace(@fa-var-dice-d6, "\\\\", "\\\\10"); }
.fad.@{fa-css-prefix}-dice-d8:after { content: replace(@fa-var-dice-d8, "\\\\", "\\\\10"); }
.fad.@{fa-css-prefix}-dice-five:after { content: replace(@fa-var-dice-five, "\\\\", "\\\\10"); }
.fad.@{fa-css-prefix}-dice-four:after { content: replace(@fa-var-dice-four, "\\\\", "\\\\10"); }
.fad.@{fa-css-prefix}-dice-one:after { content: replace(@fa-var-dice-one, "\\\\", "\\\\10"); }
.fad.@{fa-css-prefix}-dice-six:after { content: replace(@fa-var-dice-six, "\\\\", "\\\\10"); }
.fad.@{fa-css-prefix}-dice-three:after { content: replace(@fa-var-dice-three, "\\\\", "\\\\10"); }
.fad.@{fa-css-prefix}-dice-two:after { content: replace(@fa-var-dice-two, "\\\\", "\\\\10"); }
.fad.@{fa-css-prefix}-digging:after { content: replace(@fa-var-digging, "\\\\", "\\\\10"); }
.fad.@{fa-css-prefix}-digital-tachograph:after { content: replace(@fa-var-digital-tachograph, "\\\\", "\\\\10"); }
.fad.@{fa-css-prefix}-diploma:after { content: replace(@fa-var-diploma, "\\\\", "\\\\10"); }
.fad.@{fa-css-prefix}-directions:after { content: replace(@fa-var-directions, "\\\\", "\\\\10"); }
.fad.@{fa-css-prefix}-disease:after { content: replace(@fa-var-disease, "\\\\", "\\\\10"); }
.fad.@{fa-css-prefix}-divide:after { content: replace(@fa-var-divide, "\\\\", "\\\\10"); }
.fad.@{fa-css-prefix}-dizzy:after { content: replace(@fa-var-dizzy, "\\\\", "\\\\10"); }
.fad.@{fa-css-prefix}-dna:after { content: replace(@fa-var-dna, "\\\\", "\\\\10"); }
.fad.@{fa-css-prefix}-do-not-enter:after { content: replace(@fa-var-do-not-enter, "\\\\", "\\\\10"); }
.fad.@{fa-css-prefix}-dog:after { content: replace(@fa-var-dog, "\\\\", "\\\\10"); }
.fad.@{fa-css-prefix}-dog-leashed:after { content: replace(@fa-var-dog-leashed, "\\\\", "\\\\10"); }
.fad.@{fa-css-prefix}-dollar-sign:after { content: replace(@fa-var-dollar-sign, "\\\\", "\\\\10"); }
.fad.@{fa-css-prefix}-dolly:after { content: replace(@fa-var-dolly, "\\\\", "\\\\10"); }
.fad.@{fa-css-prefix}-dolly-empty:after { content: replace(@fa-var-dolly-empty, "\\\\", "\\\\10"); }
.fad.@{fa-css-prefix}-dolly-flatbed:after { content: replace(@fa-var-dolly-flatbed, "\\\\", "\\\\10"); }
.fad.@{fa-css-prefix}-dolly-flatbed-alt:after { content: replace(@fa-var-dolly-flatbed-alt, "\\\\", "\\\\10"); }
.fad.@{fa-css-prefix}-dolly-flatbed-empty:after { content: replace(@fa-var-dolly-flatbed-empty, "\\\\", "\\\\10"); }
.fad.@{fa-css-prefix}-donate:after { content: replace(@fa-var-donate, "\\\\", "\\\\10"); }
.fad.@{fa-css-prefix}-door-closed:after { content: replace(@fa-var-door-closed, "\\\\", "\\\\10"); }
.fad.@{fa-css-prefix}-door-open:after { content: replace(@fa-var-door-open, "\\\\", "\\\\10"); }
.fad.@{fa-css-prefix}-dot-circle:after { content: replace(@fa-var-dot-circle, "\\\\", "\\\\10"); }
.fad.@{fa-css-prefix}-dove:after { content: replace(@fa-var-dove, "\\\\", "\\\\10"); }
.fad.@{fa-css-prefix}-download:after { content: replace(@fa-var-download, "\\\\", "\\\\10"); }
.fad.@{fa-css-prefix}-drafting-compass:after { content: replace(@fa-var-drafting-compass, "\\\\", "\\\\10"); }
.fad.@{fa-css-prefix}-dragon:after { content: replace(@fa-var-dragon, "\\\\", "\\\\10"); }
.fad.@{fa-css-prefix}-draw-circle:after { content: replace(@fa-var-draw-circle, "\\\\", "\\\\10"); }
.fad.@{fa-css-prefix}-draw-polygon:after { content: replace(@fa-var-draw-polygon, "\\\\", "\\\\10"); }
.fad.@{fa-css-prefix}-draw-square:after { content: replace(@fa-var-draw-square, "\\\\", "\\\\10"); }
.fad.@{fa-css-prefix}-dreidel:after { content: replace(@fa-var-dreidel, "\\\\", "\\\\10"); }
.fad.@{fa-css-prefix}-drone:after { content: replace(@fa-var-drone, "\\\\", "\\\\10"); }
.fad.@{fa-css-prefix}-drone-alt:after { content: replace(@fa-var-drone-alt, "\\\\", "\\\\10"); }
.fad.@{fa-css-prefix}-drum:after { content: replace(@fa-var-drum, "\\\\", "\\\\10"); }
.fad.@{fa-css-prefix}-drum-steelpan:after { content: replace(@fa-var-drum-steelpan, "\\\\", "\\\\10"); }
.fad.@{fa-css-prefix}-drumstick:after { content: replace(@fa-var-drumstick, "\\\\", "\\\\10"); }
.fad.@{fa-css-prefix}-drumstick-bite:after { content: replace(@fa-var-drumstick-bite, "\\\\", "\\\\10"); }
.fad.@{fa-css-prefix}-dryer:after { content: replace(@fa-var-dryer, "\\\\", "\\\\10"); }
.fad.@{fa-css-prefix}-dryer-alt:after { content: replace(@fa-var-dryer-alt, "\\\\", "\\\\10"); }
.fad.@{fa-css-prefix}-duck:after { content: replace(@fa-var-duck, "\\\\", "\\\\10"); }
.fad.@{fa-css-prefix}-dumbbell:after { content: replace(@fa-var-dumbbell, "\\\\", "\\\\10"); }
.fad.@{fa-css-prefix}-dumpster:after { content: replace(@fa-var-dumpster, "\\\\", "\\\\10"); }
.fad.@{fa-css-prefix}-dumpster-fire:after { content: replace(@fa-var-dumpster-fire, "\\\\", "\\\\10"); }
.fad.@{fa-css-prefix}-dungeon:after { content: replace(@fa-var-dungeon, "\\\\", "\\\\10"); }
.fad.@{fa-css-prefix}-ear:after { content: replace(@fa-var-ear, "\\\\", "\\\\10"); }
.fad.@{fa-css-prefix}-ear-muffs:after { content: replace(@fa-var-ear-muffs, "\\\\", "\\\\10"); }
.fad.@{fa-css-prefix}-eclipse:after { content: replace(@fa-var-eclipse, "\\\\", "\\\\10"); }
.fad.@{fa-css-prefix}-eclipse-alt:after { content: replace(@fa-var-eclipse-alt, "\\\\", "\\\\10"); }
.fad.@{fa-css-prefix}-edit:after { content: replace(@fa-var-edit, "\\\\", "\\\\10"); }
.fad.@{fa-css-prefix}-egg:after { content: replace(@fa-var-egg, "\\\\", "\\\\10"); }
.fad.@{fa-css-prefix}-egg-fried:after { content: replace(@fa-var-egg-fried, "\\\\", "\\\\10"); }
.fad.@{fa-css-prefix}-eject:after { content: replace(@fa-var-eject, "\\\\", "\\\\10"); }
.fad.@{fa-css-prefix}-elephant:after { content: replace(@fa-var-elephant, "\\\\", "\\\\10"); }
.fad.@{fa-css-prefix}-ellipsis-h:after { content: replace(@fa-var-ellipsis-h, "\\\\", "\\\\10"); }
.fad.@{fa-css-prefix}-ellipsis-h-alt:after { content: replace(@fa-var-ellipsis-h-alt, "\\\\", "\\\\10"); }
.fad.@{fa-css-prefix}-ellipsis-v:after { content: replace(@fa-var-ellipsis-v, "\\\\", "\\\\10"); }
.fad.@{fa-css-prefix}-ellipsis-v-alt:after { content: replace(@fa-var-ellipsis-v-alt, "\\\\", "\\\\10"); }
.fad.@{fa-css-prefix}-empty-set:after { content: replace(@fa-var-empty-set, "\\\\", "\\\\10"); }
.fad.@{fa-css-prefix}-engine-warning:after { content: replace(@fa-var-engine-warning, "\\\\", "\\\\10"); }
.fad.@{fa-css-prefix}-envelope:after { content: replace(@fa-var-envelope, "\\\\", "\\\\10"); }
.fad.@{fa-css-prefix}-envelope-open:after { content: replace(@fa-var-envelope-open, "\\\\", "\\\\10"); }
.fad.@{fa-css-prefix}-envelope-open-dollar:after { content: replace(@fa-var-envelope-open-dollar, "\\\\", "\\\\10"); }
.fad.@{fa-css-prefix}-envelope-open-text:after { content: replace(@fa-var-envelope-open-text, "\\\\", "\\\\10"); }
.fad.@{fa-css-prefix}-envelope-square:after { content: replace(@fa-var-envelope-square, "\\\\", "\\\\10"); }
.fad.@{fa-css-prefix}-equals:after { content: replace(@fa-var-equals, "\\\\", "\\\\10"); }
.fad.@{fa-css-prefix}-eraser:after { content: replace(@fa-var-eraser, "\\\\", "\\\\10"); }
.fad.@{fa-css-prefix}-ethernet:after { content: replace(@fa-var-ethernet, "\\\\", "\\\\10"); }
.fad.@{fa-css-prefix}-euro-sign:after { content: replace(@fa-var-euro-sign, "\\\\", "\\\\10"); }
.fad.@{fa-css-prefix}-exchange:after { content: replace(@fa-var-exchange, "\\\\", "\\\\10"); }
.fad.@{fa-css-prefix}-exchange-alt:after { content: replace(@fa-var-exchange-alt, "\\\\", "\\\\10"); }
.fad.@{fa-css-prefix}-exclamation:after { content: replace(@fa-var-exclamation, "\\\\", "\\\\10"); }
.fad.@{fa-css-prefix}-exclamation-circle:after { content: replace(@fa-var-exclamation-circle, "\\\\", "\\\\10"); }
.fad.@{fa-css-prefix}-exclamation-square:after { content: replace(@fa-var-exclamation-square, "\\\\", "\\\\10"); }
.fad.@{fa-css-prefix}-exclamation-triangle:after { content: replace(@fa-var-exclamation-triangle, "\\\\", "\\\\10"); }
.fad.@{fa-css-prefix}-expand:after { content: replace(@fa-var-expand, "\\\\", "\\\\10"); }
.fad.@{fa-css-prefix}-expand-alt:after { content: replace(@fa-var-expand-alt, "\\\\", "\\\\10"); }
.fad.@{fa-css-prefix}-expand-arrows:after { content: replace(@fa-var-expand-arrows, "\\\\", "\\\\10"); }
.fad.@{fa-css-prefix}-expand-arrows-alt:after { content: replace(@fa-var-expand-arrows-alt, "\\\\", "\\\\10"); }
.fad.@{fa-css-prefix}-expand-wide:after { content: replace(@fa-var-expand-wide, "\\\\", "\\\\10"); }
.fad.@{fa-css-prefix}-external-link:after { content: replace(@fa-var-external-link, "\\\\", "\\\\10"); }
.fad.@{fa-css-prefix}-external-link-alt:after { content: replace(@fa-var-external-link-alt, "\\\\", "\\\\10"); }
.fad.@{fa-css-prefix}-external-link-square:after { content: replace(@fa-var-external-link-square, "\\\\", "\\\\10"); }
.fad.@{fa-css-prefix}-external-link-square-alt:after { content: replace(@fa-var-external-link-square-alt, "\\\\", "\\\\10"); }
.fad.@{fa-css-prefix}-eye:after { content: replace(@fa-var-eye, "\\\\", "\\\\10"); }
.fad.@{fa-css-prefix}-eye-dropper:after { content: replace(@fa-var-eye-dropper, "\\\\", "\\\\10"); }
.fad.@{fa-css-prefix}-eye-evil:after { content: replace(@fa-var-eye-evil, "\\\\", "\\\\10"); }
.fad.@{fa-css-prefix}-eye-slash:after { content: replace(@fa-var-eye-slash, "\\\\", "\\\\10"); }
.fad.@{fa-css-prefix}-fan:after { content: replace(@fa-var-fan, "\\\\", "\\\\10"); }
.fad.@{fa-css-prefix}-farm:after { content: replace(@fa-var-farm, "\\\\", "\\\\10"); }
.fad.@{fa-css-prefix}-fast-backward:after { content: replace(@fa-var-fast-backward, "\\\\", "\\\\10"); }
.fad.@{fa-css-prefix}-fast-forward:after { content: replace(@fa-var-fast-forward, "\\\\", "\\\\10"); }
.fad.@{fa-css-prefix}-fax:after { content: replace(@fa-var-fax, "\\\\", "\\\\10"); }
.fad.@{fa-css-prefix}-feather:after { content: replace(@fa-var-feather, "\\\\", "\\\\10"); }
.fad.@{fa-css-prefix}-feather-alt:after { content: replace(@fa-var-feather-alt, "\\\\", "\\\\10"); }
.fad.@{fa-css-prefix}-female:after { content: replace(@fa-var-female, "\\\\", "\\\\10"); }
.fad.@{fa-css-prefix}-field-hockey:after { content: replace(@fa-var-field-hockey, "\\\\", "\\\\10"); }
.fad.@{fa-css-prefix}-fighter-jet:after { content: replace(@fa-var-fighter-jet, "\\\\", "\\\\10"); }
.fad.@{fa-css-prefix}-file:after { content: replace(@fa-var-file, "\\\\", "\\\\10"); }
.fad.@{fa-css-prefix}-file-alt:after { content: replace(@fa-var-file-alt, "\\\\", "\\\\10"); }
.fad.@{fa-css-prefix}-file-archive:after { content: replace(@fa-var-file-archive, "\\\\", "\\\\10"); }
.fad.@{fa-css-prefix}-file-audio:after { content: replace(@fa-var-file-audio, "\\\\", "\\\\10"); }
.fad.@{fa-css-prefix}-file-certificate:after { content: replace(@fa-var-file-certificate, "\\\\", "\\\\10"); }
.fad.@{fa-css-prefix}-file-chart-line:after { content: replace(@fa-var-file-chart-line, "\\\\", "\\\\10"); }
.fad.@{fa-css-prefix}-file-chart-pie:after { content: replace(@fa-var-file-chart-pie, "\\\\", "\\\\10"); }
.fad.@{fa-css-prefix}-file-check:after { content: replace(@fa-var-file-check, "\\\\", "\\\\10"); }
.fad.@{fa-css-prefix}-file-code:after { content: replace(@fa-var-file-code, "\\\\", "\\\\10"); }
.fad.@{fa-css-prefix}-file-contract:after { content: replace(@fa-var-file-contract, "\\\\", "\\\\10"); }
.fad.@{fa-css-prefix}-file-csv:after { content: replace(@fa-var-file-csv, "\\\\", "\\\\10"); }
.fad.@{fa-css-prefix}-file-download:after { content: replace(@fa-var-file-download, "\\\\", "\\\\10"); }
.fad.@{fa-css-prefix}-file-edit:after { content: replace(@fa-var-file-edit, "\\\\", "\\\\10"); }
.fad.@{fa-css-prefix}-file-excel:after { content: replace(@fa-var-file-excel, "\\\\", "\\\\10"); }
.fad.@{fa-css-prefix}-file-exclamation:after { content: replace(@fa-var-file-exclamation, "\\\\", "\\\\10"); }
.fad.@{fa-css-prefix}-file-export:after { content: replace(@fa-var-file-export, "\\\\", "\\\\10"); }
.fad.@{fa-css-prefix}-file-image:after { content: replace(@fa-var-file-image, "\\\\", "\\\\10"); }
.fad.@{fa-css-prefix}-file-import:after { content: replace(@fa-var-file-import, "\\\\", "\\\\10"); }
.fad.@{fa-css-prefix}-file-invoice:after { content: replace(@fa-var-file-invoice, "\\\\", "\\\\10"); }
.fad.@{fa-css-prefix}-file-invoice-dollar:after { content: replace(@fa-var-file-invoice-dollar, "\\\\", "\\\\10"); }
.fad.@{fa-css-prefix}-file-medical:after { content: replace(@fa-var-file-medical, "\\\\", "\\\\10"); }
.fad.@{fa-css-prefix}-file-medical-alt:after { content: replace(@fa-var-file-medical-alt, "\\\\", "\\\\10"); }
.fad.@{fa-css-prefix}-file-minus:after { content: replace(@fa-var-file-minus, "\\\\", "\\\\10"); }
.fad.@{fa-css-prefix}-file-pdf:after { content: replace(@fa-var-file-pdf, "\\\\", "\\\\10"); }
.fad.@{fa-css-prefix}-file-plus:after { content: replace(@fa-var-file-plus, "\\\\", "\\\\10"); }
.fad.@{fa-css-prefix}-file-powerpoint:after { content: replace(@fa-var-file-powerpoint, "\\\\", "\\\\10"); }
.fad.@{fa-css-prefix}-file-prescription:after { content: replace(@fa-var-file-prescription, "\\\\", "\\\\10"); }
.fad.@{fa-css-prefix}-file-search:after { content: replace(@fa-var-file-search, "\\\\", "\\\\10"); }
.fad.@{fa-css-prefix}-file-signature:after { content: replace(@fa-var-file-signature, "\\\\", "\\\\10"); }
.fad.@{fa-css-prefix}-file-spreadsheet:after { content: replace(@fa-var-file-spreadsheet, "\\\\", "\\\\10"); }
.fad.@{fa-css-prefix}-file-times:after { content: replace(@fa-var-file-times, "\\\\", "\\\\10"); }
.fad.@{fa-css-prefix}-file-upload:after { content: replace(@fa-var-file-upload, "\\\\", "\\\\10"); }
.fad.@{fa-css-prefix}-file-user:after { content: replace(@fa-var-file-user, "\\\\", "\\\\10"); }
.fad.@{fa-css-prefix}-file-video:after { content: replace(@fa-var-file-video, "\\\\", "\\\\10"); }
.fad.@{fa-css-prefix}-file-word:after { content: replace(@fa-var-file-word, "\\\\", "\\\\10"); }
.fad.@{fa-css-prefix}-files-medical:after { content: replace(@fa-var-files-medical, "\\\\", "\\\\10"); }
.fad.@{fa-css-prefix}-fill:after { content: replace(@fa-var-fill, "\\\\", "\\\\10"); }
.fad.@{fa-css-prefix}-fill-drip:after { content: replace(@fa-var-fill-drip, "\\\\", "\\\\10"); }
.fad.@{fa-css-prefix}-film:after { content: replace(@fa-var-film, "\\\\", "\\\\10"); }
.fad.@{fa-css-prefix}-film-alt:after { content: replace(@fa-var-film-alt, "\\\\", "\\\\10"); }
.fad.@{fa-css-prefix}-filter:after { content: replace(@fa-var-filter, "\\\\", "\\\\10"); }
.fad.@{fa-css-prefix}-fingerprint:after { content: replace(@fa-var-fingerprint, "\\\\", "\\\\10"); }
.fad.@{fa-css-prefix}-fire:after { content: replace(@fa-var-fire, "\\\\", "\\\\10"); }
.fad.@{fa-css-prefix}-fire-alt:after { content: replace(@fa-var-fire-alt, "\\\\", "\\\\10"); }
.fad.@{fa-css-prefix}-fire-extinguisher:after { content: replace(@fa-var-fire-extinguisher, "\\\\", "\\\\10"); }
.fad.@{fa-css-prefix}-fire-smoke:after { content: replace(@fa-var-fire-smoke, "\\\\", "\\\\10"); }
.fad.@{fa-css-prefix}-fireplace:after { content: replace(@fa-var-fireplace, "\\\\", "\\\\10"); }
.fad.@{fa-css-prefix}-first-aid:after { content: replace(@fa-var-first-aid, "\\\\", "\\\\10"); }
.fad.@{fa-css-prefix}-fish:after { content: replace(@fa-var-fish, "\\\\", "\\\\10"); }
.fad.@{fa-css-prefix}-fish-cooked:after { content: replace(@fa-var-fish-cooked, "\\\\", "\\\\10"); }
.fad.@{fa-css-prefix}-fist-raised:after { content: replace(@fa-var-fist-raised, "\\\\", "\\\\10"); }
.fad.@{fa-css-prefix}-flag:after { content: replace(@fa-var-flag, "\\\\", "\\\\10"); }
.fad.@{fa-css-prefix}-flag-alt:after { content: replace(@fa-var-flag-alt, "\\\\", "\\\\10"); }
.fad.@{fa-css-prefix}-flag-checkered:after { content: replace(@fa-var-flag-checkered, "\\\\", "\\\\10"); }
.fad.@{fa-css-prefix}-flag-usa:after { content: replace(@fa-var-flag-usa, "\\\\", "\\\\10"); }
.fad.@{fa-css-prefix}-flame:after { content: replace(@fa-var-flame, "\\\\", "\\\\10"); }
.fad.@{fa-css-prefix}-flask:after { content: replace(@fa-var-flask, "\\\\", "\\\\10"); }
.fad.@{fa-css-prefix}-flask-poison:after { content: replace(@fa-var-flask-poison, "\\\\", "\\\\10"); }
.fad.@{fa-css-prefix}-flask-potion:after { content: replace(@fa-var-flask-potion, "\\\\", "\\\\10"); }
.fad.@{fa-css-prefix}-flower:after { content: replace(@fa-var-flower, "\\\\", "\\\\10"); }
.fad.@{fa-css-prefix}-flower-daffodil:after { content: replace(@fa-var-flower-daffodil, "\\\\", "\\\\10"); }
.fad.@{fa-css-prefix}-flower-tulip:after { content: replace(@fa-var-flower-tulip, "\\\\", "\\\\10"); }
.fad.@{fa-css-prefix}-flushed:after { content: replace(@fa-var-flushed, "\\\\", "\\\\10"); }
.fad.@{fa-css-prefix}-fog:after { content: replace(@fa-var-fog, "\\\\", "\\\\10"); }
.fad.@{fa-css-prefix}-folder:after { content: replace(@fa-var-folder, "\\\\", "\\\\10"); }
.fad.@{fa-css-prefix}-folder-minus:after { content: replace(@fa-var-folder-minus, "\\\\", "\\\\10"); }
.fad.@{fa-css-prefix}-folder-open:after { content: replace(@fa-var-folder-open, "\\\\", "\\\\10"); }
.fad.@{fa-css-prefix}-folder-plus:after { content: replace(@fa-var-folder-plus, "\\\\", "\\\\10"); }
.fad.@{fa-css-prefix}-folder-times:after { content: replace(@fa-var-folder-times, "\\\\", "\\\\10"); }
.fad.@{fa-css-prefix}-folder-tree:after { content: replace(@fa-var-folder-tree, "\\\\", "\\\\10"); }
.fad.@{fa-css-prefix}-folders:after { content: replace(@fa-var-folders, "\\\\", "\\\\10"); }
.fad.@{fa-css-prefix}-font:after { content: replace(@fa-var-font, "\\\\", "\\\\10"); }
.fad.@{fa-css-prefix}-font-awesome-logo-full:after { content: replace(@fa-var-font-awesome-logo-full, "\\\\", "\\\\10"); }
.fad.@{fa-css-prefix}-font-case:after { content: replace(@fa-var-font-case, "\\\\", "\\\\10"); }
.fad.@{fa-css-prefix}-football-ball:after { content: replace(@fa-var-football-ball, "\\\\", "\\\\10"); }
.fad.@{fa-css-prefix}-football-helmet:after { content: replace(@fa-var-football-helmet, "\\\\", "\\\\10"); }
.fad.@{fa-css-prefix}-forklift:after { content: replace(@fa-var-forklift, "\\\\", "\\\\10"); }
.fad.@{fa-css-prefix}-forward:after { content: replace(@fa-var-forward, "\\\\", "\\\\10"); }
.fad.@{fa-css-prefix}-fragile:after { content: replace(@fa-var-fragile, "\\\\", "\\\\10"); }
.fad.@{fa-css-prefix}-french-fries:after { content: replace(@fa-var-french-fries, "\\\\", "\\\\10"); }
.fad.@{fa-css-prefix}-frog:after { content: replace(@fa-var-frog, "\\\\", "\\\\10"); }
.fad.@{fa-css-prefix}-frosty-head:after { content: replace(@fa-var-frosty-head, "\\\\", "\\\\10"); }
.fad.@{fa-css-prefix}-frown:after { content: replace(@fa-var-frown, "\\\\", "\\\\10"); }
.fad.@{fa-css-prefix}-frown-open:after { content: replace(@fa-var-frown-open, "\\\\", "\\\\10"); }
.fad.@{fa-css-prefix}-function:after { content: replace(@fa-var-function, "\\\\", "\\\\10"); }
.fad.@{fa-css-prefix}-funnel-dollar:after { content: replace(@fa-var-funnel-dollar, "\\\\", "\\\\10"); }
.fad.@{fa-css-prefix}-futbol:after { content: replace(@fa-var-futbol, "\\\\", "\\\\10"); }
.fad.@{fa-css-prefix}-game-board:after { content: replace(@fa-var-game-board, "\\\\", "\\\\10"); }
.fad.@{fa-css-prefix}-game-board-alt:after { content: replace(@fa-var-game-board-alt, "\\\\", "\\\\10"); }
.fad.@{fa-css-prefix}-gamepad:after { content: replace(@fa-var-gamepad, "\\\\", "\\\\10"); }
.fad.@{fa-css-prefix}-gas-pump:after { content: replace(@fa-var-gas-pump, "\\\\", "\\\\10"); }
.fad.@{fa-css-prefix}-gas-pump-slash:after { content: replace(@fa-var-gas-pump-slash, "\\\\", "\\\\10"); }
.fad.@{fa-css-prefix}-gavel:after { content: replace(@fa-var-gavel, "\\\\", "\\\\10"); }
.fad.@{fa-css-prefix}-gem:after { content: replace(@fa-var-gem, "\\\\", "\\\\10"); }
.fad.@{fa-css-prefix}-genderless:after { content: replace(@fa-var-genderless, "\\\\", "\\\\10"); }
.fad.@{fa-css-prefix}-ghost:after { content: replace(@fa-var-ghost, "\\\\", "\\\\10"); }
.fad.@{fa-css-prefix}-gift:after { content: replace(@fa-var-gift, "\\\\", "\\\\10"); }
.fad.@{fa-css-prefix}-gift-card:after { content: replace(@fa-var-gift-card, "\\\\", "\\\\10"); }
.fad.@{fa-css-prefix}-gifts:after { content: replace(@fa-var-gifts, "\\\\", "\\\\10"); }
.fad.@{fa-css-prefix}-gingerbread-man:after { content: replace(@fa-var-gingerbread-man, "\\\\", "\\\\10"); }
.fad.@{fa-css-prefix}-glass:after { content: replace(@fa-var-glass, "\\\\", "\\\\10"); }
.fad.@{fa-css-prefix}-glass-champagne:after { content: replace(@fa-var-glass-champagne, "\\\\", "\\\\10"); }
.fad.@{fa-css-prefix}-glass-cheers:after { content: replace(@fa-var-glass-cheers, "\\\\", "\\\\10"); }
.fad.@{fa-css-prefix}-glass-citrus:after { content: replace(@fa-var-glass-citrus, "\\\\", "\\\\10"); }
.fad.@{fa-css-prefix}-glass-martini:after { content: replace(@fa-var-glass-martini, "\\\\", "\\\\10"); }
.fad.@{fa-css-prefix}-glass-martini-alt:after { content: replace(@fa-var-glass-martini-alt, "\\\\", "\\\\10"); }
.fad.@{fa-css-prefix}-glass-whiskey:after { content: replace(@fa-var-glass-whiskey, "\\\\", "\\\\10"); }
.fad.@{fa-css-prefix}-glass-whiskey-rocks:after { content: replace(@fa-var-glass-whiskey-rocks, "\\\\", "\\\\10"); }
.fad.@{fa-css-prefix}-glasses:after { content: replace(@fa-var-glasses, "\\\\", "\\\\10"); }
.fad.@{fa-css-prefix}-glasses-alt:after { content: replace(@fa-var-glasses-alt, "\\\\", "\\\\10"); }
.fad.@{fa-css-prefix}-globe:after { content: replace(@fa-var-globe, "\\\\", "\\\\10"); }
.fad.@{fa-css-prefix}-globe-africa:after { content: replace(@fa-var-globe-africa, "\\\\", "\\\\10"); }
.fad.@{fa-css-prefix}-globe-americas:after { content: replace(@fa-var-globe-americas, "\\\\", "\\\\10"); }
.fad.@{fa-css-prefix}-globe-asia:after { content: replace(@fa-var-globe-asia, "\\\\", "\\\\10"); }
.fad.@{fa-css-prefix}-globe-europe:after { content: replace(@fa-var-globe-europe, "\\\\", "\\\\10"); }
.fad.@{fa-css-prefix}-globe-snow:after { content: replace(@fa-var-globe-snow, "\\\\", "\\\\10"); }
.fad.@{fa-css-prefix}-globe-stand:after { content: replace(@fa-var-globe-stand, "\\\\", "\\\\10"); }
.fad.@{fa-css-prefix}-golf-ball:after { content: replace(@fa-var-golf-ball, "\\\\", "\\\\10"); }
.fad.@{fa-css-prefix}-golf-club:after { content: replace(@fa-var-golf-club, "\\\\", "\\\\10"); }
.fad.@{fa-css-prefix}-gopuram:after { content: replace(@fa-var-gopuram, "\\\\", "\\\\10"); }
.fad.@{fa-css-prefix}-graduation-cap:after { content: replace(@fa-var-graduation-cap, "\\\\", "\\\\10"); }
.fad.@{fa-css-prefix}-greater-than:after { content: replace(@fa-var-greater-than, "\\\\", "\\\\10"); }
.fad.@{fa-css-prefix}-greater-than-equal:after { content: replace(@fa-var-greater-than-equal, "\\\\", "\\\\10"); }
.fad.@{fa-css-prefix}-grimace:after { content: replace(@fa-var-grimace, "\\\\", "\\\\10"); }
.fad.@{fa-css-prefix}-grin:after { content: replace(@fa-var-grin, "\\\\", "\\\\10"); }
.fad.@{fa-css-prefix}-grin-alt:after { content: replace(@fa-var-grin-alt, "\\\\", "\\\\10"); }
.fad.@{fa-css-prefix}-grin-beam:after { content: replace(@fa-var-grin-beam, "\\\\", "\\\\10"); }
.fad.@{fa-css-prefix}-grin-beam-sweat:after { content: replace(@fa-var-grin-beam-sweat, "\\\\", "\\\\10"); }
.fad.@{fa-css-prefix}-grin-hearts:after { content: replace(@fa-var-grin-hearts, "\\\\", "\\\\10"); }
.fad.@{fa-css-prefix}-grin-squint:after { content: replace(@fa-var-grin-squint, "\\\\", "\\\\10"); }
.fad.@{fa-css-prefix}-grin-squint-tears:after { content: replace(@fa-var-grin-squint-tears, "\\\\", "\\\\10"); }
.fad.@{fa-css-prefix}-grin-stars:after { content: replace(@fa-var-grin-stars, "\\\\", "\\\\10"); }
.fad.@{fa-css-prefix}-grin-tears:after { content: replace(@fa-var-grin-tears, "\\\\", "\\\\10"); }
.fad.@{fa-css-prefix}-grin-tongue:after { content: replace(@fa-var-grin-tongue, "\\\\", "\\\\10"); }
.fad.@{fa-css-prefix}-grin-tongue-squint:after { content: replace(@fa-var-grin-tongue-squint, "\\\\", "\\\\10"); }
.fad.@{fa-css-prefix}-grin-tongue-wink:after { content: replace(@fa-var-grin-tongue-wink, "\\\\", "\\\\10"); }
.fad.@{fa-css-prefix}-grin-wink:after { content: replace(@fa-var-grin-wink, "\\\\", "\\\\10"); }
.fad.@{fa-css-prefix}-grip-horizontal:after { content: replace(@fa-var-grip-horizontal, "\\\\", "\\\\10"); }
.fad.@{fa-css-prefix}-grip-lines:after { content: replace(@fa-var-grip-lines, "\\\\", "\\\\10"); }
.fad.@{fa-css-prefix}-grip-lines-vertical:after { content: replace(@fa-var-grip-lines-vertical, "\\\\", "\\\\10"); }
.fad.@{fa-css-prefix}-grip-vertical:after { content: replace(@fa-var-grip-vertical, "\\\\", "\\\\10"); }
.fad.@{fa-css-prefix}-guitar:after { content: replace(@fa-var-guitar, "\\\\", "\\\\10"); }
.fad.@{fa-css-prefix}-h-square:after { content: replace(@fa-var-h-square, "\\\\", "\\\\10"); }
.fad.@{fa-css-prefix}-h1:after { content: replace(@fa-var-h1, "\\\\", "\\\\10"); }
.fad.@{fa-css-prefix}-h2:after { content: replace(@fa-var-h2, "\\\\", "\\\\10"); }
.fad.@{fa-css-prefix}-h3:after { content: replace(@fa-var-h3, "\\\\", "\\\\10"); }
.fad.@{fa-css-prefix}-h4:after { content: replace(@fa-var-h4, "\\\\", "\\\\10"); }
.fad.@{fa-css-prefix}-hamburger:after { content: replace(@fa-var-hamburger, "\\\\", "\\\\10"); }
.fad.@{fa-css-prefix}-hammer:after { content: replace(@fa-var-hammer, "\\\\", "\\\\10"); }
.fad.@{fa-css-prefix}-hammer-war:after { content: replace(@fa-var-hammer-war, "\\\\", "\\\\10"); }
.fad.@{fa-css-prefix}-hamsa:after { content: replace(@fa-var-hamsa, "\\\\", "\\\\10"); }
.fad.@{fa-css-prefix}-hand-heart:after { content: replace(@fa-var-hand-heart, "\\\\", "\\\\10"); }
.fad.@{fa-css-prefix}-hand-holding:after { content: replace(@fa-var-hand-holding, "\\\\", "\\\\10"); }
.fad.@{fa-css-prefix}-hand-holding-box:after { content: replace(@fa-var-hand-holding-box, "\\\\", "\\\\10"); }
.fad.@{fa-css-prefix}-hand-holding-heart:after { content: replace(@fa-var-hand-holding-heart, "\\\\", "\\\\10"); }
.fad.@{fa-css-prefix}-hand-holding-magic:after { content: replace(@fa-var-hand-holding-magic, "\\\\", "\\\\10"); }
.fad.@{fa-css-prefix}-hand-holding-seedling:after { content: replace(@fa-var-hand-holding-seedling, "\\\\", "\\\\10"); }
.fad.@{fa-css-prefix}-hand-holding-usd:after { content: replace(@fa-var-hand-holding-usd, "\\\\", "\\\\10"); }
.fad.@{fa-css-prefix}-hand-holding-water:after { content: replace(@fa-var-hand-holding-water, "\\\\", "\\\\10"); }
.fad.@{fa-css-prefix}-hand-lizard:after { content: replace(@fa-var-hand-lizard, "\\\\", "\\\\10"); }
.fad.@{fa-css-prefix}-hand-middle-finger:after { content: replace(@fa-var-hand-middle-finger, "\\\\", "\\\\10"); }
.fad.@{fa-css-prefix}-hand-paper:after { content: replace(@fa-var-hand-paper, "\\\\", "\\\\10"); }
.fad.@{fa-css-prefix}-hand-peace:after { content: replace(@fa-var-hand-peace, "\\\\", "\\\\10"); }
.fad.@{fa-css-prefix}-hand-point-down:after { content: replace(@fa-var-hand-point-down, "\\\\", "\\\\10"); }
.fad.@{fa-css-prefix}-hand-point-left:after { content: replace(@fa-var-hand-point-left, "\\\\", "\\\\10"); }
.fad.@{fa-css-prefix}-hand-point-right:after { content: replace(@fa-var-hand-point-right, "\\\\", "\\\\10"); }
.fad.@{fa-css-prefix}-hand-point-up:after { content: replace(@fa-var-hand-point-up, "\\\\", "\\\\10"); }
.fad.@{fa-css-prefix}-hand-pointer:after { content: replace(@fa-var-hand-pointer, "\\\\", "\\\\10"); }
.fad.@{fa-css-prefix}-hand-receiving:after { content: replace(@fa-var-hand-receiving, "\\\\", "\\\\10"); }
.fad.@{fa-css-prefix}-hand-rock:after { content: replace(@fa-var-hand-rock, "\\\\", "\\\\10"); }
.fad.@{fa-css-prefix}-hand-scissors:after { content: replace(@fa-var-hand-scissors, "\\\\", "\\\\10"); }
.fad.@{fa-css-prefix}-hand-spock:after { content: replace(@fa-var-hand-spock, "\\\\", "\\\\10"); }
.fad.@{fa-css-prefix}-hands:after { content: replace(@fa-var-hands, "\\\\", "\\\\10"); }
.fad.@{fa-css-prefix}-hands-heart:after { content: replace(@fa-var-hands-heart, "\\\\", "\\\\10"); }
.fad.@{fa-css-prefix}-hands-helping:after { content: replace(@fa-var-hands-helping, "\\\\", "\\\\10"); }
.fad.@{fa-css-prefix}-hands-usd:after { content: replace(@fa-var-hands-usd, "\\\\", "\\\\10"); }
.fad.@{fa-css-prefix}-handshake:after { content: replace(@fa-var-handshake, "\\\\", "\\\\10"); }
.fad.@{fa-css-prefix}-handshake-alt:after { content: replace(@fa-var-handshake-alt, "\\\\", "\\\\10"); }
.fad.@{fa-css-prefix}-hanukiah:after { content: replace(@fa-var-hanukiah, "\\\\", "\\\\10"); }
.fad.@{fa-css-prefix}-hard-hat:after { content: replace(@fa-var-hard-hat, "\\\\", "\\\\10"); }
.fad.@{fa-css-prefix}-hashtag:after { content: replace(@fa-var-hashtag, "\\\\", "\\\\10"); }
.fad.@{fa-css-prefix}-hat-chef:after { content: replace(@fa-var-hat-chef, "\\\\", "\\\\10"); }
.fad.@{fa-css-prefix}-hat-santa:after { content: replace(@fa-var-hat-santa, "\\\\", "\\\\10"); }
.fad.@{fa-css-prefix}-hat-winter:after { content: replace(@fa-var-hat-winter, "\\\\", "\\\\10"); }
.fad.@{fa-css-prefix}-hat-witch:after { content: replace(@fa-var-hat-witch, "\\\\", "\\\\10"); }
.fad.@{fa-css-prefix}-hat-wizard:after { content: replace(@fa-var-hat-wizard, "\\\\", "\\\\10"); }
.fad.@{fa-css-prefix}-haykal:after { content: replace(@fa-var-haykal, "\\\\", "\\\\10"); }
.fad.@{fa-css-prefix}-hdd:after { content: replace(@fa-var-hdd, "\\\\", "\\\\10"); }
.fad.@{fa-css-prefix}-head-side:after { content: replace(@fa-var-head-side, "\\\\", "\\\\10"); }
.fad.@{fa-css-prefix}-head-side-brain:after { content: replace(@fa-var-head-side-brain, "\\\\", "\\\\10"); }
.fad.@{fa-css-prefix}-head-side-medical:after { content: replace(@fa-var-head-side-medical, "\\\\", "\\\\10"); }
.fad.@{fa-css-prefix}-head-vr:after { content: replace(@fa-var-head-vr, "\\\\", "\\\\10"); }
.fad.@{fa-css-prefix}-heading:after { content: replace(@fa-var-heading, "\\\\", "\\\\10"); }
.fad.@{fa-css-prefix}-headphones:after { content: replace(@fa-var-headphones, "\\\\", "\\\\10"); }
.fad.@{fa-css-prefix}-headphones-alt:after { content: replace(@fa-var-headphones-alt, "\\\\", "\\\\10"); }
.fad.@{fa-css-prefix}-headset:after { content: replace(@fa-var-headset, "\\\\", "\\\\10"); }
.fad.@{fa-css-prefix}-heart:after { content: replace(@fa-var-heart, "\\\\", "\\\\10"); }
.fad.@{fa-css-prefix}-heart-broken:after { content: replace(@fa-var-heart-broken, "\\\\", "\\\\10"); }
.fad.@{fa-css-prefix}-heart-circle:after { content: replace(@fa-var-heart-circle, "\\\\", "\\\\10"); }
.fad.@{fa-css-prefix}-heart-rate:after { content: replace(@fa-var-heart-rate, "\\\\", "\\\\10"); }
.fad.@{fa-css-prefix}-heart-square:after { content: replace(@fa-var-heart-square, "\\\\", "\\\\10"); }
.fad.@{fa-css-prefix}-heartbeat:after { content: replace(@fa-var-heartbeat, "\\\\", "\\\\10"); }
.fad.@{fa-css-prefix}-helicopter:after { content: replace(@fa-var-helicopter, "\\\\", "\\\\10"); }
.fad.@{fa-css-prefix}-helmet-battle:after { content: replace(@fa-var-helmet-battle, "\\\\", "\\\\10"); }
.fad.@{fa-css-prefix}-hexagon:after { content: replace(@fa-var-hexagon, "\\\\", "\\\\10"); }
.fad.@{fa-css-prefix}-highlighter:after { content: replace(@fa-var-highlighter, "\\\\", "\\\\10"); }
.fad.@{fa-css-prefix}-hiking:after { content: replace(@fa-var-hiking, "\\\\", "\\\\10"); }
.fad.@{fa-css-prefix}-hippo:after { content: replace(@fa-var-hippo, "\\\\", "\\\\10"); }
.fad.@{fa-css-prefix}-history:after { content: replace(@fa-var-history, "\\\\", "\\\\10"); }
.fad.@{fa-css-prefix}-hockey-mask:after { content: replace(@fa-var-hockey-mask, "\\\\", "\\\\10"); }
.fad.@{fa-css-prefix}-hockey-puck:after { content: replace(@fa-var-hockey-puck, "\\\\", "\\\\10"); }
.fad.@{fa-css-prefix}-hockey-sticks:after { content: replace(@fa-var-hockey-sticks, "\\\\", "\\\\10"); }
.fad.@{fa-css-prefix}-holly-berry:after { content: replace(@fa-var-holly-berry, "\\\\", "\\\\10"); }
.fad.@{fa-css-prefix}-home:after { content: replace(@fa-var-home, "\\\\", "\\\\10"); }
.fad.@{fa-css-prefix}-home-alt:after { content: replace(@fa-var-home-alt, "\\\\", "\\\\10"); }
.fad.@{fa-css-prefix}-home-heart:after { content: replace(@fa-var-home-heart, "\\\\", "\\\\10"); }
.fad.@{fa-css-prefix}-home-lg:after { content: replace(@fa-var-home-lg, "\\\\", "\\\\10"); }
.fad.@{fa-css-prefix}-home-lg-alt:after { content: replace(@fa-var-home-lg-alt, "\\\\", "\\\\10"); }
.fad.@{fa-css-prefix}-hood-cloak:after { content: replace(@fa-var-hood-cloak, "\\\\", "\\\\10"); }
.fad.@{fa-css-prefix}-horizontal-rule:after { content: replace(@fa-var-horizontal-rule, "\\\\", "\\\\10"); }
.fad.@{fa-css-prefix}-horse:after { content: replace(@fa-var-horse, "\\\\", "\\\\10"); }
.fad.@{fa-css-prefix}-horse-head:after { content: replace(@fa-var-horse-head, "\\\\", "\\\\10"); }
.fad.@{fa-css-prefix}-hospital:after { content: replace(@fa-var-hospital, "\\\\", "\\\\10"); }
.fad.@{fa-css-prefix}-hospital-alt:after { content: replace(@fa-var-hospital-alt, "\\\\", "\\\\10"); }
.fad.@{fa-css-prefix}-hospital-symbol:after { content: replace(@fa-var-hospital-symbol, "\\\\", "\\\\10"); }
.fad.@{fa-css-prefix}-hospital-user:after { content: replace(@fa-var-hospital-user, "\\\\", "\\\\10"); }
.fad.@{fa-css-prefix}-hospitals:after { content: replace(@fa-var-hospitals, "\\\\", "\\\\10"); }
.fad.@{fa-css-prefix}-hot-tub:after { content: replace(@fa-var-hot-tub, "\\\\", "\\\\10"); }
.fad.@{fa-css-prefix}-hotdog:after { content: replace(@fa-var-hotdog, "\\\\", "\\\\10"); }
.fad.@{fa-css-prefix}-hotel:after { content: replace(@fa-var-hotel, "\\\\", "\\\\10"); }
.fad.@{fa-css-prefix}-hourglass:after { content: replace(@fa-var-hourglass, "\\\\", "\\\\10"); }
.fad.@{fa-css-prefix}-hourglass-end:after { content: replace(@fa-var-hourglass-end, "\\\\", "\\\\10"); }
.fad.@{fa-css-prefix}-hourglass-half:after { content: replace(@fa-var-hourglass-half, "\\\\", "\\\\10"); }
.fad.@{fa-css-prefix}-hourglass-start:after { content: replace(@fa-var-hourglass-start, "\\\\", "\\\\10"); }
.fad.@{fa-css-prefix}-house-damage:after { content: replace(@fa-var-house-damage, "\\\\", "\\\\10"); }
.fad.@{fa-css-prefix}-house-flood:after { content: replace(@fa-var-house-flood, "\\\\", "\\\\10"); }
.fad.@{fa-css-prefix}-hryvnia:after { content: replace(@fa-var-hryvnia, "\\\\", "\\\\10"); }
.fad.@{fa-css-prefix}-humidity:after { content: replace(@fa-var-humidity, "\\\\", "\\\\10"); }
.fad.@{fa-css-prefix}-hurricane:after { content: replace(@fa-var-hurricane, "\\\\", "\\\\10"); }
.fad.@{fa-css-prefix}-i-cursor:after { content: replace(@fa-var-i-cursor, "\\\\", "\\\\10"); }
.fad.@{fa-css-prefix}-ice-cream:after { content: replace(@fa-var-ice-cream, "\\\\", "\\\\10"); }
.fad.@{fa-css-prefix}-ice-skate:after { content: replace(@fa-var-ice-skate, "\\\\", "\\\\10"); }
.fad.@{fa-css-prefix}-icicles:after { content: replace(@fa-var-icicles, "\\\\", "\\\\10"); }
.fad.@{fa-css-prefix}-icons:after { content: replace(@fa-var-icons, "\\\\", "\\\\10"); }
.fad.@{fa-css-prefix}-icons-alt:after { content: replace(@fa-var-icons-alt, "\\\\", "\\\\10"); }
.fad.@{fa-css-prefix}-id-badge:after { content: replace(@fa-var-id-badge, "\\\\", "\\\\10"); }
.fad.@{fa-css-prefix}-id-card:after { content: replace(@fa-var-id-card, "\\\\", "\\\\10"); }
.fad.@{fa-css-prefix}-id-card-alt:after { content: replace(@fa-var-id-card-alt, "\\\\", "\\\\10"); }
.fad.@{fa-css-prefix}-igloo:after { content: replace(@fa-var-igloo, "\\\\", "\\\\10"); }
.fad.@{fa-css-prefix}-image:after { content: replace(@fa-var-image, "\\\\", "\\\\10"); }
.fad.@{fa-css-prefix}-images:after { content: replace(@fa-var-images, "\\\\", "\\\\10"); }
.fad.@{fa-css-prefix}-inbox:after { content: replace(@fa-var-inbox, "\\\\", "\\\\10"); }
.fad.@{fa-css-prefix}-inbox-in:after { content: replace(@fa-var-inbox-in, "\\\\", "\\\\10"); }
.fad.@{fa-css-prefix}-inbox-out:after { content: replace(@fa-var-inbox-out, "\\\\", "\\\\10"); }
.fad.@{fa-css-prefix}-indent:after { content: replace(@fa-var-indent, "\\\\", "\\\\10"); }
.fad.@{fa-css-prefix}-industry:after { content: replace(@fa-var-industry, "\\\\", "\\\\10"); }
.fad.@{fa-css-prefix}-industry-alt:after { content: replace(@fa-var-industry-alt, "\\\\", "\\\\10"); }
.fad.@{fa-css-prefix}-infinity:after { content: replace(@fa-var-infinity, "\\\\", "\\\\10"); }
.fad.@{fa-css-prefix}-info:after { content: replace(@fa-var-info, "\\\\", "\\\\10"); }
.fad.@{fa-css-prefix}-info-circle:after { content: replace(@fa-var-info-circle, "\\\\", "\\\\10"); }
.fad.@{fa-css-prefix}-info-square:after { content: replace(@fa-var-info-square, "\\\\", "\\\\10"); }
.fad.@{fa-css-prefix}-inhaler:after { content: replace(@fa-var-inhaler, "\\\\", "\\\\10"); }
.fad.@{fa-css-prefix}-integral:after { content: replace(@fa-var-integral, "\\\\", "\\\\10"); }
.fad.@{fa-css-prefix}-intersection:after { content: replace(@fa-var-intersection, "\\\\", "\\\\10"); }
.fad.@{fa-css-prefix}-inventory:after { content: replace(@fa-var-inventory, "\\\\", "\\\\10"); }
.fad.@{fa-css-prefix}-island-tropical:after { content: replace(@fa-var-island-tropical, "\\\\", "\\\\10"); }
.fad.@{fa-css-prefix}-italic:after { content: replace(@fa-var-italic, "\\\\", "\\\\10"); }
.fad.@{fa-css-prefix}-jack-o-lantern:after { content: replace(@fa-var-jack-o-lantern, "\\\\", "\\\\10"); }
.fad.@{fa-css-prefix}-jedi:after { content: replace(@fa-var-jedi, "\\\\", "\\\\10"); }
.fad.@{fa-css-prefix}-joint:after { content: replace(@fa-var-joint, "\\\\", "\\\\10"); }
.fad.@{fa-css-prefix}-journal-whills:after { content: replace(@fa-var-journal-whills, "\\\\", "\\\\10"); }
.fad.@{fa-css-prefix}-kaaba:after { content: replace(@fa-var-kaaba, "\\\\", "\\\\10"); }
.fad.@{fa-css-prefix}-kerning:after { content: replace(@fa-var-kerning, "\\\\", "\\\\10"); }
.fad.@{fa-css-prefix}-key:after { content: replace(@fa-var-key, "\\\\", "\\\\10"); }
.fad.@{fa-css-prefix}-key-skeleton:after { content: replace(@fa-var-key-skeleton, "\\\\", "\\\\10"); }
.fad.@{fa-css-prefix}-keyboard:after { content: replace(@fa-var-keyboard, "\\\\", "\\\\10"); }
.fad.@{fa-css-prefix}-keynote:after { content: replace(@fa-var-keynote, "\\\\", "\\\\10"); }
.fad.@{fa-css-prefix}-khanda:after { content: replace(@fa-var-khanda, "\\\\", "\\\\10"); }
.fad.@{fa-css-prefix}-kidneys:after { content: replace(@fa-var-kidneys, "\\\\", "\\\\10"); }
.fad.@{fa-css-prefix}-kiss:after { content: replace(@fa-var-kiss, "\\\\", "\\\\10"); }
.fad.@{fa-css-prefix}-kiss-beam:after { content: replace(@fa-var-kiss-beam, "\\\\", "\\\\10"); }
.fad.@{fa-css-prefix}-kiss-wink-heart:after { content: replace(@fa-var-kiss-wink-heart, "\\\\", "\\\\10"); }
.fad.@{fa-css-prefix}-kite:after { content: replace(@fa-var-kite, "\\\\", "\\\\10"); }
.fad.@{fa-css-prefix}-kiwi-bird:after { content: replace(@fa-var-kiwi-bird, "\\\\", "\\\\10"); }
.fad.@{fa-css-prefix}-knife-kitchen:after { content: replace(@fa-var-knife-kitchen, "\\\\", "\\\\10"); }
.fad.@{fa-css-prefix}-lambda:after { content: replace(@fa-var-lambda, "\\\\", "\\\\10"); }
.fad.@{fa-css-prefix}-lamp:after { content: replace(@fa-var-lamp, "\\\\", "\\\\10"); }
.fad.@{fa-css-prefix}-landmark:after { content: replace(@fa-var-landmark, "\\\\", "\\\\10"); }
.fad.@{fa-css-prefix}-landmark-alt:after { content: replace(@fa-var-landmark-alt, "\\\\", "\\\\10"); }
.fad.@{fa-css-prefix}-language:after { content: replace(@fa-var-language, "\\\\", "\\\\10"); }
.fad.@{fa-css-prefix}-laptop:after { content: replace(@fa-var-laptop, "\\\\", "\\\\10"); }
.fad.@{fa-css-prefix}-laptop-code:after { content: replace(@fa-var-laptop-code, "\\\\", "\\\\10"); }
.fad.@{fa-css-prefix}-laptop-medical:after { content: replace(@fa-var-laptop-medical, "\\\\", "\\\\10"); }
.fad.@{fa-css-prefix}-laugh:after { content: replace(@fa-var-laugh, "\\\\", "\\\\10"); }
.fad.@{fa-css-prefix}-laugh-beam:after { content: replace(@fa-var-laugh-beam, "\\\\", "\\\\10"); }
.fad.@{fa-css-prefix}-laugh-squint:after { content: replace(@fa-var-laugh-squint, "\\\\", "\\\\10"); }
.fad.@{fa-css-prefix}-laugh-wink:after { content: replace(@fa-var-laugh-wink, "\\\\", "\\\\10"); }
.fad.@{fa-css-prefix}-layer-group:after { content: replace(@fa-var-layer-group, "\\\\", "\\\\10"); }
.fad.@{fa-css-prefix}-layer-minus:after { content: replace(@fa-var-layer-minus, "\\\\", "\\\\10"); }
.fad.@{fa-css-prefix}-layer-plus:after { content: replace(@fa-var-layer-plus, "\\\\", "\\\\10"); }
.fad.@{fa-css-prefix}-leaf:after { content: replace(@fa-var-leaf, "\\\\", "\\\\10"); }
.fad.@{fa-css-prefix}-leaf-heart:after { content: replace(@fa-var-leaf-heart, "\\\\", "\\\\10"); }
.fad.@{fa-css-prefix}-leaf-maple:after { content: replace(@fa-var-leaf-maple, "\\\\", "\\\\10"); }
.fad.@{fa-css-prefix}-leaf-oak:after { content: replace(@fa-var-leaf-oak, "\\\\", "\\\\10"); }
.fad.@{fa-css-prefix}-lemon:after { content: replace(@fa-var-lemon, "\\\\", "\\\\10"); }
.fad.@{fa-css-prefix}-less-than:after { content: replace(@fa-var-less-than, "\\\\", "\\\\10"); }
.fad.@{fa-css-prefix}-less-than-equal:after { content: replace(@fa-var-less-than-equal, "\\\\", "\\\\10"); }
.fad.@{fa-css-prefix}-level-down:after { content: replace(@fa-var-level-down, "\\\\", "\\\\10"); }
.fad.@{fa-css-prefix}-level-down-alt:after { content: replace(@fa-var-level-down-alt, "\\\\", "\\\\10"); }
.fad.@{fa-css-prefix}-level-up:after { content: replace(@fa-var-level-up, "\\\\", "\\\\10"); }
.fad.@{fa-css-prefix}-level-up-alt:after { content: replace(@fa-var-level-up-alt, "\\\\", "\\\\10"); }
.fad.@{fa-css-prefix}-life-ring:after { content: replace(@fa-var-life-ring, "\\\\", "\\\\10"); }
.fad.@{fa-css-prefix}-lightbulb:after { content: replace(@fa-var-lightbulb, "\\\\", "\\\\10"); }
.fad.@{fa-css-prefix}-lightbulb-dollar:after { content: replace(@fa-var-lightbulb-dollar, "\\\\", "\\\\10"); }
.fad.@{fa-css-prefix}-lightbulb-exclamation:after { content: replace(@fa-var-lightbulb-exclamation, "\\\\", "\\\\10"); }
.fad.@{fa-css-prefix}-lightbulb-on:after { content: replace(@fa-var-lightbulb-on, "\\\\", "\\\\10"); }
.fad.@{fa-css-prefix}-lightbulb-slash:after { content: replace(@fa-var-lightbulb-slash, "\\\\", "\\\\10"); }
.fad.@{fa-css-prefix}-lights-holiday:after { content: replace(@fa-var-lights-holiday, "\\\\", "\\\\10"); }
.fad.@{fa-css-prefix}-line-columns:after { content: replace(@fa-var-line-columns, "\\\\", "\\\\10"); }
.fad.@{fa-css-prefix}-line-height:after { content: replace(@fa-var-line-height, "\\\\", "\\\\10"); }
.fad.@{fa-css-prefix}-link:after { content: replace(@fa-var-link, "\\\\", "\\\\10"); }
.fad.@{fa-css-prefix}-lips:after { content: replace(@fa-var-lips, "\\\\", "\\\\10"); }
.fad.@{fa-css-prefix}-lira-sign:after { content: replace(@fa-var-lira-sign, "\\\\", "\\\\10"); }
.fad.@{fa-css-prefix}-list:after { content: replace(@fa-var-list, "\\\\", "\\\\10"); }
.fad.@{fa-css-prefix}-list-alt:after { content: replace(@fa-var-list-alt, "\\\\", "\\\\10"); }
.fad.@{fa-css-prefix}-list-ol:after { content: replace(@fa-var-list-ol, "\\\\", "\\\\10"); }
.fad.@{fa-css-prefix}-list-ul:after { content: replace(@fa-var-list-ul, "\\\\", "\\\\10"); }
.fad.@{fa-css-prefix}-location:after { content: replace(@fa-var-location, "\\\\", "\\\\10"); }
.fad.@{fa-css-prefix}-location-arrow:after { content: replace(@fa-var-location-arrow, "\\\\", "\\\\10"); }
.fad.@{fa-css-prefix}-location-circle:after { content: replace(@fa-var-location-circle, "\\\\", "\\\\10"); }
.fad.@{fa-css-prefix}-location-slash:after { content: replace(@fa-var-location-slash, "\\\\", "\\\\10"); }
.fad.@{fa-css-prefix}-lock:after { content: replace(@fa-var-lock, "\\\\", "\\\\10"); }
.fad.@{fa-css-prefix}-lock-alt:after { content: replace(@fa-var-lock-alt, "\\\\", "\\\\10"); }
.fad.@{fa-css-prefix}-lock-open:after { content: replace(@fa-var-lock-open, "\\\\", "\\\\10"); }
.fad.@{fa-css-prefix}-lock-open-alt:after { content: replace(@fa-var-lock-open-alt, "\\\\", "\\\\10"); }
.fad.@{fa-css-prefix}-long-arrow-alt-down:after { content: replace(@fa-var-long-arrow-alt-down, "\\\\", "\\\\10"); }
.fad.@{fa-css-prefix}-long-arrow-alt-left:after { content: replace(@fa-var-long-arrow-alt-left, "\\\\", "\\\\10"); }
.fad.@{fa-css-prefix}-long-arrow-alt-right:after { content: replace(@fa-var-long-arrow-alt-right, "\\\\", "\\\\10"); }
.fad.@{fa-css-prefix}-long-arrow-alt-up:after { content: replace(@fa-var-long-arrow-alt-up, "\\\\", "\\\\10"); }
.fad.@{fa-css-prefix}-long-arrow-down:after { content: replace(@fa-var-long-arrow-down, "\\\\", "\\\\10"); }
.fad.@{fa-css-prefix}-long-arrow-left:after { content: replace(@fa-var-long-arrow-left, "\\\\", "\\\\10"); }
.fad.@{fa-css-prefix}-long-arrow-right:after { content: replace(@fa-var-long-arrow-right, "\\\\", "\\\\10"); }
.fad.@{fa-css-prefix}-long-arrow-up:after { content: replace(@fa-var-long-arrow-up, "\\\\", "\\\\10"); }
.fad.@{fa-css-prefix}-loveseat:after { content: replace(@fa-var-loveseat, "\\\\", "\\\\10"); }
.fad.@{fa-css-prefix}-low-vision:after { content: replace(@fa-var-low-vision, "\\\\", "\\\\10"); }
.fad.@{fa-css-prefix}-luchador:after { content: replace(@fa-var-luchador, "\\\\", "\\\\10"); }
.fad.@{fa-css-prefix}-luggage-cart:after { content: replace(@fa-var-luggage-cart, "\\\\", "\\\\10"); }
.fad.@{fa-css-prefix}-lungs:after { content: replace(@fa-var-lungs, "\\\\", "\\\\10"); }
.fad.@{fa-css-prefix}-mace:after { content: replace(@fa-var-mace, "\\\\", "\\\\10"); }
.fad.@{fa-css-prefix}-magic:after { content: replace(@fa-var-magic, "\\\\", "\\\\10"); }
.fad.@{fa-css-prefix}-magnet:after { content: replace(@fa-var-magnet, "\\\\", "\\\\10"); }
.fad.@{fa-css-prefix}-mail-bulk:after { content: replace(@fa-var-mail-bulk, "\\\\", "\\\\10"); }
.fad.@{fa-css-prefix}-mailbox:after { content: replace(@fa-var-mailbox, "\\\\", "\\\\10"); }
.fad.@{fa-css-prefix}-male:after { content: replace(@fa-var-male, "\\\\", "\\\\10"); }
.fad.@{fa-css-prefix}-mandolin:after { content: replace(@fa-var-mandolin, "\\\\", "\\\\10"); }
.fad.@{fa-css-prefix}-map:after { content: replace(@fa-var-map, "\\\\", "\\\\10"); }
.fad.@{fa-css-prefix}-map-marked:after { content: replace(@fa-var-map-marked, "\\\\", "\\\\10"); }
.fad.@{fa-css-prefix}-map-marked-alt:after { content: replace(@fa-var-map-marked-alt, "\\\\", "\\\\10"); }
.fad.@{fa-css-prefix}-map-marker:after { content: replace(@fa-var-map-marker, "\\\\", "\\\\10"); }
.fad.@{fa-css-prefix}-map-marker-alt:after { content: replace(@fa-var-map-marker-alt, "\\\\", "\\\\10"); }
.fad.@{fa-css-prefix}-map-marker-alt-slash:after { content: replace(@fa-var-map-marker-alt-slash, "\\\\", "\\\\10"); }
.fad.@{fa-css-prefix}-map-marker-check:after { content: replace(@fa-var-map-marker-check, "\\\\", "\\\\10"); }
.fad.@{fa-css-prefix}-map-marker-edit:after { content: replace(@fa-var-map-marker-edit, "\\\\", "\\\\10"); }
.fad.@{fa-css-prefix}-map-marker-exclamation:after { content: replace(@fa-var-map-marker-exclamation, "\\\\", "\\\\10"); }
.fad.@{fa-css-prefix}-map-marker-minus:after { content: replace(@fa-var-map-marker-minus, "\\\\", "\\\\10"); }
.fad.@{fa-css-prefix}-map-marker-plus:after { content: replace(@fa-var-map-marker-plus, "\\\\", "\\\\10"); }
.fad.@{fa-css-prefix}-map-marker-question:after { content: replace(@fa-var-map-marker-question, "\\\\", "\\\\10"); }
.fad.@{fa-css-prefix}-map-marker-slash:after { content: replace(@fa-var-map-marker-slash, "\\\\", "\\\\10"); }
.fad.@{fa-css-prefix}-map-marker-smile:after { content: replace(@fa-var-map-marker-smile, "\\\\", "\\\\10"); }
.fad.@{fa-css-prefix}-map-marker-times:after { content: replace(@fa-var-map-marker-times, "\\\\", "\\\\10"); }
.fad.@{fa-css-prefix}-map-pin:after { content: replace(@fa-var-map-pin, "\\\\", "\\\\10"); }
.fad.@{fa-css-prefix}-map-signs:after { content: replace(@fa-var-map-signs, "\\\\", "\\\\10"); }
.fad.@{fa-css-prefix}-marker:after { content: replace(@fa-var-marker, "\\\\", "\\\\10"); }
.fad.@{fa-css-prefix}-mars:after { content: replace(@fa-var-mars, "\\\\", "\\\\10"); }
.fad.@{fa-css-prefix}-mars-double:after { content: replace(@fa-var-mars-double, "\\\\", "\\\\10"); }
.fad.@{fa-css-prefix}-mars-stroke:after { content: replace(@fa-var-mars-stroke, "\\\\", "\\\\10"); }
.fad.@{fa-css-prefix}-mars-stroke-h:after { content: replace(@fa-var-mars-stroke-h, "\\\\", "\\\\10"); }
.fad.@{fa-css-prefix}-mars-stroke-v:after { content: replace(@fa-var-mars-stroke-v, "\\\\", "\\\\10"); }
.fad.@{fa-css-prefix}-mask:after { content: replace(@fa-var-mask, "\\\\", "\\\\10"); }
.fad.@{fa-css-prefix}-meat:after { content: replace(@fa-var-meat, "\\\\", "\\\\10"); }
.fad.@{fa-css-prefix}-medal:after { content: replace(@fa-var-medal, "\\\\", "\\\\10"); }
.fad.@{fa-css-prefix}-medkit:after { content: replace(@fa-var-medkit, "\\\\", "\\\\10"); }
.fad.@{fa-css-prefix}-megaphone:after { content: replace(@fa-var-megaphone, "\\\\", "\\\\10"); }
.fad.@{fa-css-prefix}-meh:after { content: replace(@fa-var-meh, "\\\\", "\\\\10"); }
.fad.@{fa-css-prefix}-meh-blank:after { content: replace(@fa-var-meh-blank, "\\\\", "\\\\10"); }
.fad.@{fa-css-prefix}-meh-rolling-eyes:after { content: replace(@fa-var-meh-rolling-eyes, "\\\\", "\\\\10"); }
.fad.@{fa-css-prefix}-memory:after { content: replace(@fa-var-memory, "\\\\", "\\\\10"); }
.fad.@{fa-css-prefix}-menorah:after { content: replace(@fa-var-menorah, "\\\\", "\\\\10"); }
.fad.@{fa-css-prefix}-mercury:after { content: replace(@fa-var-mercury, "\\\\", "\\\\10"); }
.fad.@{fa-css-prefix}-meteor:after { content: replace(@fa-var-meteor, "\\\\", "\\\\10"); }
.fad.@{fa-css-prefix}-microchip:after { content: replace(@fa-var-microchip, "\\\\", "\\\\10"); }
.fad.@{fa-css-prefix}-microphone:after { content: replace(@fa-var-microphone, "\\\\", "\\\\10"); }
.fad.@{fa-css-prefix}-microphone-alt:after { content: replace(@fa-var-microphone-alt, "\\\\", "\\\\10"); }
.fad.@{fa-css-prefix}-microphone-alt-slash:after { content: replace(@fa-var-microphone-alt-slash, "\\\\", "\\\\10"); }
.fad.@{fa-css-prefix}-microphone-slash:after { content: replace(@fa-var-microphone-slash, "\\\\", "\\\\10"); }
.fad.@{fa-css-prefix}-microscope:after { content: replace(@fa-var-microscope, "\\\\", "\\\\10"); }
.fad.@{fa-css-prefix}-mind-share:after { content: replace(@fa-var-mind-share, "\\\\", "\\\\10"); }
.fad.@{fa-css-prefix}-minus:after { content: replace(@fa-var-minus, "\\\\", "\\\\10"); }
.fad.@{fa-css-prefix}-minus-circle:after { content: replace(@fa-var-minus-circle, "\\\\", "\\\\10"); }
.fad.@{fa-css-prefix}-minus-hexagon:after { content: replace(@fa-var-minus-hexagon, "\\\\", "\\\\10"); }
.fad.@{fa-css-prefix}-minus-octagon:after { content: replace(@fa-var-minus-octagon, "\\\\", "\\\\10"); }
.fad.@{fa-css-prefix}-minus-square:after { content: replace(@fa-var-minus-square, "\\\\", "\\\\10"); }
.fad.@{fa-css-prefix}-mistletoe:after { content: replace(@fa-var-mistletoe, "\\\\", "\\\\10"); }
.fad.@{fa-css-prefix}-mitten:after { content: replace(@fa-var-mitten, "\\\\", "\\\\10"); }
.fad.@{fa-css-prefix}-mobile:after { content: replace(@fa-var-mobile, "\\\\", "\\\\10"); }
.fad.@{fa-css-prefix}-mobile-alt:after { content: replace(@fa-var-mobile-alt, "\\\\", "\\\\10"); }
.fad.@{fa-css-prefix}-mobile-android:after { content: replace(@fa-var-mobile-android, "\\\\", "\\\\10"); }
.fad.@{fa-css-prefix}-mobile-android-alt:after { content: replace(@fa-var-mobile-android-alt, "\\\\", "\\\\10"); }
.fad.@{fa-css-prefix}-money-bill:after { content: replace(@fa-var-money-bill, "\\\\", "\\\\10"); }
.fad.@{fa-css-prefix}-money-bill-alt:after { content: replace(@fa-var-money-bill-alt, "\\\\", "\\\\10"); }
.fad.@{fa-css-prefix}-money-bill-wave:after { content: replace(@fa-var-money-bill-wave, "\\\\", "\\\\10"); }
.fad.@{fa-css-prefix}-money-bill-wave-alt:after { content: replace(@fa-var-money-bill-wave-alt, "\\\\", "\\\\10"); }
.fad.@{fa-css-prefix}-money-check:after { content: replace(@fa-var-money-check, "\\\\", "\\\\10"); }
.fad.@{fa-css-prefix}-money-check-alt:after { content: replace(@fa-var-money-check-alt, "\\\\", "\\\\10"); }
.fad.@{fa-css-prefix}-money-check-edit:after { content: replace(@fa-var-money-check-edit, "\\\\", "\\\\10"); }
.fad.@{fa-css-prefix}-money-check-edit-alt:after { content: replace(@fa-var-money-check-edit-alt, "\\\\", "\\\\10"); }
.fad.@{fa-css-prefix}-monitor-heart-rate:after { content: replace(@fa-var-monitor-heart-rate, "\\\\", "\\\\10"); }
.fad.@{fa-css-prefix}-monkey:after { content: replace(@fa-var-monkey, "\\\\", "\\\\10"); }
.fad.@{fa-css-prefix}-monument:after { content: replace(@fa-var-monument, "\\\\", "\\\\10"); }
.fad.@{fa-css-prefix}-moon:after { content: replace(@fa-var-moon, "\\\\", "\\\\10"); }
.fad.@{fa-css-prefix}-moon-cloud:after { content: replace(@fa-var-moon-cloud, "\\\\", "\\\\10"); }
.fad.@{fa-css-prefix}-moon-stars:after { content: replace(@fa-var-moon-stars, "\\\\", "\\\\10"); }
.fad.@{fa-css-prefix}-mortar-pestle:after { content: replace(@fa-var-mortar-pestle, "\\\\", "\\\\10"); }
.fad.@{fa-css-prefix}-mosque:after { content: replace(@fa-var-mosque, "\\\\", "\\\\10"); }
.fad.@{fa-css-prefix}-motorcycle:after { content: replace(@fa-var-motorcycle, "\\\\", "\\\\10"); }
.fad.@{fa-css-prefix}-mountain:after { content: replace(@fa-var-mountain, "\\\\", "\\\\10"); }
.fad.@{fa-css-prefix}-mountains:after { content: replace(@fa-var-mountains, "\\\\", "\\\\10"); }
.fad.@{fa-css-prefix}-mouse-pointer:after { content: replace(@fa-var-mouse-pointer, "\\\\", "\\\\10"); }
.fad.@{fa-css-prefix}-mug:after { content: replace(@fa-var-mug, "\\\\", "\\\\10"); }
.fad.@{fa-css-prefix}-mug-hot:after { content: replace(@fa-var-mug-hot, "\\\\", "\\\\10"); }
.fad.@{fa-css-prefix}-mug-marshmallows:after { content: replace(@fa-var-mug-marshmallows, "\\\\", "\\\\10"); }
.fad.@{fa-css-prefix}-mug-tea:after { content: replace(@fa-var-mug-tea, "\\\\", "\\\\10"); }
.fad.@{fa-css-prefix}-music:after { content: replace(@fa-var-music, "\\\\", "\\\\10"); }
.fad.@{fa-css-prefix}-narwhal:after { content: replace(@fa-var-narwhal, "\\\\", "\\\\10"); }
.fad.@{fa-css-prefix}-network-wired:after { content: replace(@fa-var-network-wired, "\\\\", "\\\\10"); }
.fad.@{fa-css-prefix}-neuter:after { content: replace(@fa-var-neuter, "\\\\", "\\\\10"); }
.fad.@{fa-css-prefix}-newspaper:after { content: replace(@fa-var-newspaper, "\\\\", "\\\\10"); }
.fad.@{fa-css-prefix}-not-equal:after { content: replace(@fa-var-not-equal, "\\\\", "\\\\10"); }
.fad.@{fa-css-prefix}-notes-medical:after { content: replace(@fa-var-notes-medical, "\\\\", "\\\\10"); }
.fad.@{fa-css-prefix}-object-group:after { content: replace(@fa-var-object-group, "\\\\", "\\\\10"); }
.fad.@{fa-css-prefix}-object-ungroup:after { content: replace(@fa-var-object-ungroup, "\\\\", "\\\\10"); }
.fad.@{fa-css-prefix}-octagon:after { content: replace(@fa-var-octagon, "\\\\", "\\\\10"); }
.fad.@{fa-css-prefix}-oil-can:after { content: replace(@fa-var-oil-can, "\\\\", "\\\\10"); }
.fad.@{fa-css-prefix}-oil-temp:after { content: replace(@fa-var-oil-temp, "\\\\", "\\\\10"); }
.fad.@{fa-css-prefix}-om:after { content: replace(@fa-var-om, "\\\\", "\\\\10"); }
.fad.@{fa-css-prefix}-omega:after { content: replace(@fa-var-omega, "\\\\", "\\\\10"); }
.fad.@{fa-css-prefix}-ornament:after { content: replace(@fa-var-ornament, "\\\\", "\\\\10"); }
.fad.@{fa-css-prefix}-otter:after { content: replace(@fa-var-otter, "\\\\", "\\\\10"); }
.fad.@{fa-css-prefix}-outdent:after { content: replace(@fa-var-outdent, "\\\\", "\\\\10"); }
.fad.@{fa-css-prefix}-overline:after { content: replace(@fa-var-overline, "\\\\", "\\\\10"); }
.fad.@{fa-css-prefix}-page-break:after { content: replace(@fa-var-page-break, "\\\\", "\\\\10"); }
.fad.@{fa-css-prefix}-pager:after { content: replace(@fa-var-pager, "\\\\", "\\\\10"); }
.fad.@{fa-css-prefix}-paint-brush:after { content: replace(@fa-var-paint-brush, "\\\\", "\\\\10"); }
.fad.@{fa-css-prefix}-paint-brush-alt:after { content: replace(@fa-var-paint-brush-alt, "\\\\", "\\\\10"); }
.fad.@{fa-css-prefix}-paint-roller:after { content: replace(@fa-var-paint-roller, "\\\\", "\\\\10"); }
.fad.@{fa-css-prefix}-palette:after { content: replace(@fa-var-palette, "\\\\", "\\\\10"); }
.fad.@{fa-css-prefix}-pallet:after { content: replace(@fa-var-pallet, "\\\\", "\\\\10"); }
.fad.@{fa-css-prefix}-pallet-alt:after { content: replace(@fa-var-pallet-alt, "\\\\", "\\\\10"); }
.fad.@{fa-css-prefix}-paper-plane:after { content: replace(@fa-var-paper-plane, "\\\\", "\\\\10"); }
.fad.@{fa-css-prefix}-paperclip:after { content: replace(@fa-var-paperclip, "\\\\", "\\\\10"); }
.fad.@{fa-css-prefix}-parachute-box:after { content: replace(@fa-var-parachute-box, "\\\\", "\\\\10"); }
.fad.@{fa-css-prefix}-paragraph:after { content: replace(@fa-var-paragraph, "\\\\", "\\\\10"); }
.fad.@{fa-css-prefix}-paragraph-rtl:after { content: replace(@fa-var-paragraph-rtl, "\\\\", "\\\\10"); }
.fad.@{fa-css-prefix}-parking:after { content: replace(@fa-var-parking, "\\\\", "\\\\10"); }
.fad.@{fa-css-prefix}-parking-circle:after { content: replace(@fa-var-parking-circle, "\\\\", "\\\\10"); }
.fad.@{fa-css-prefix}-parking-circle-slash:after { content: replace(@fa-var-parking-circle-slash, "\\\\", "\\\\10"); }
.fad.@{fa-css-prefix}-parking-slash:after { content: replace(@fa-var-parking-slash, "\\\\", "\\\\10"); }
.fad.@{fa-css-prefix}-passport:after { content: replace(@fa-var-passport, "\\\\", "\\\\10"); }
.fad.@{fa-css-prefix}-pastafarianism:after { content: replace(@fa-var-pastafarianism, "\\\\", "\\\\10"); }
.fad.@{fa-css-prefix}-paste:after { content: replace(@fa-var-paste, "\\\\", "\\\\10"); }
.fad.@{fa-css-prefix}-pause:after { content: replace(@fa-var-pause, "\\\\", "\\\\10"); }
.fad.@{fa-css-prefix}-pause-circle:after { content: replace(@fa-var-pause-circle, "\\\\", "\\\\10"); }
.fad.@{fa-css-prefix}-paw:after { content: replace(@fa-var-paw, "\\\\", "\\\\10"); }
.fad.@{fa-css-prefix}-paw-alt:after { content: replace(@fa-var-paw-alt, "\\\\", "\\\\10"); }
.fad.@{fa-css-prefix}-paw-claws:after { content: replace(@fa-var-paw-claws, "\\\\", "\\\\10"); }
.fad.@{fa-css-prefix}-peace:after { content: replace(@fa-var-peace, "\\\\", "\\\\10"); }
.fad.@{fa-css-prefix}-pegasus:after { content: replace(@fa-var-pegasus, "\\\\", "\\\\10"); }
.fad.@{fa-css-prefix}-pen:after { content: replace(@fa-var-pen, "\\\\", "\\\\10"); }
.fad.@{fa-css-prefix}-pen-alt:after { content: replace(@fa-var-pen-alt, "\\\\", "\\\\10"); }
.fad.@{fa-css-prefix}-pen-fancy:after { content: replace(@fa-var-pen-fancy, "\\\\", "\\\\10"); }
.fad.@{fa-css-prefix}-pen-nib:after { content: replace(@fa-var-pen-nib, "\\\\", "\\\\10"); }
.fad.@{fa-css-prefix}-pen-square:after { content: replace(@fa-var-pen-square, "\\\\", "\\\\10"); }
.fad.@{fa-css-prefix}-pencil:after { content: replace(@fa-var-pencil, "\\\\", "\\\\10"); }
.fad.@{fa-css-prefix}-pencil-alt:after { content: replace(@fa-var-pencil-alt, "\\\\", "\\\\10"); }
.fad.@{fa-css-prefix}-pencil-paintbrush:after { content: replace(@fa-var-pencil-paintbrush, "\\\\", "\\\\10"); }
.fad.@{fa-css-prefix}-pencil-ruler:after { content: replace(@fa-var-pencil-ruler, "\\\\", "\\\\10"); }
.fad.@{fa-css-prefix}-pennant:after { content: replace(@fa-var-pennant, "\\\\", "\\\\10"); }
.fad.@{fa-css-prefix}-people-carry:after { content: replace(@fa-var-people-carry, "\\\\", "\\\\10"); }
.fad.@{fa-css-prefix}-pepper-hot:after { content: replace(@fa-var-pepper-hot, "\\\\", "\\\\10"); }
.fad.@{fa-css-prefix}-percent:after { content: replace(@fa-var-percent, "\\\\", "\\\\10"); }
.fad.@{fa-css-prefix}-percentage:after { content: replace(@fa-var-percentage, "\\\\", "\\\\10"); }
.fad.@{fa-css-prefix}-person-booth:after { content: replace(@fa-var-person-booth, "\\\\", "\\\\10"); }
.fad.@{fa-css-prefix}-person-carry:after { content: replace(@fa-var-person-carry, "\\\\", "\\\\10"); }
.fad.@{fa-css-prefix}-person-dolly:after { content: replace(@fa-var-person-dolly, "\\\\", "\\\\10"); }
.fad.@{fa-css-prefix}-person-dolly-empty:after { content: replace(@fa-var-person-dolly-empty, "\\\\", "\\\\10"); }
.fad.@{fa-css-prefix}-person-sign:after { content: replace(@fa-var-person-sign, "\\\\", "\\\\10"); }
.fad.@{fa-css-prefix}-phone:after { content: replace(@fa-var-phone, "\\\\", "\\\\10"); }
.fad.@{fa-css-prefix}-phone-laptop:after { content: replace(@fa-var-phone-laptop, "\\\\", "\\\\10"); }
.fad.@{fa-css-prefix}-phone-office:after { content: replace(@fa-var-phone-office, "\\\\", "\\\\10"); }
.fad.@{fa-css-prefix}-phone-plus:after { content: replace(@fa-var-phone-plus, "\\\\", "\\\\10"); }
.fad.@{fa-css-prefix}-phone-slash:after { content: replace(@fa-var-phone-slash, "\\\\", "\\\\10"); }
.fad.@{fa-css-prefix}-phone-square:after { content: replace(@fa-var-phone-square, "\\\\", "\\\\10"); }
.fad.@{fa-css-prefix}-phone-volume:after { content: replace(@fa-var-phone-volume, "\\\\", "\\\\10"); }
.fad.@{fa-css-prefix}-photo-video:after { content: replace(@fa-var-photo-video, "\\\\", "\\\\10"); }
.fad.@{fa-css-prefix}-pi:after { content: replace(@fa-var-pi, "\\\\", "\\\\10"); }
.fad.@{fa-css-prefix}-pie:after { content: replace(@fa-var-pie, "\\\\", "\\\\10"); }
.fad.@{fa-css-prefix}-pig:after { content: replace(@fa-var-pig, "\\\\", "\\\\10"); }
.fad.@{fa-css-prefix}-piggy-bank:after { content: replace(@fa-var-piggy-bank, "\\\\", "\\\\10"); }
.fad.@{fa-css-prefix}-pills:after { content: replace(@fa-var-pills, "\\\\", "\\\\10"); }
.fad.@{fa-css-prefix}-pizza:after { content: replace(@fa-var-pizza, "\\\\", "\\\\10"); }
.fad.@{fa-css-prefix}-pizza-slice:after { content: replace(@fa-var-pizza-slice, "\\\\", "\\\\10"); }
.fad.@{fa-css-prefix}-place-of-worship:after { content: replace(@fa-var-place-of-worship, "\\\\", "\\\\10"); }
.fad.@{fa-css-prefix}-plane:after { content: replace(@fa-var-plane, "\\\\", "\\\\10"); }
.fad.@{fa-css-prefix}-plane-alt:after { content: replace(@fa-var-plane-alt, "\\\\", "\\\\10"); }
.fad.@{fa-css-prefix}-plane-arrival:after { content: replace(@fa-var-plane-arrival, "\\\\", "\\\\10"); }
.fad.@{fa-css-prefix}-plane-departure:after { content: replace(@fa-var-plane-departure, "\\\\", "\\\\10"); }
.fad.@{fa-css-prefix}-play:after { content: replace(@fa-var-play, "\\\\", "\\\\10"); }
.fad.@{fa-css-prefix}-play-circle:after { content: replace(@fa-var-play-circle, "\\\\", "\\\\10"); }
.fad.@{fa-css-prefix}-plug:after { content: replace(@fa-var-plug, "\\\\", "\\\\10"); }
.fad.@{fa-css-prefix}-plus:after { content: replace(@fa-var-plus, "\\\\", "\\\\10"); }
.fad.@{fa-css-prefix}-plus-circle:after { content: replace(@fa-var-plus-circle, "\\\\", "\\\\10"); }
.fad.@{fa-css-prefix}-plus-hexagon:after { content: replace(@fa-var-plus-hexagon, "\\\\", "\\\\10"); }
.fad.@{fa-css-prefix}-plus-octagon:after { content: replace(@fa-var-plus-octagon, "\\\\", "\\\\10"); }
.fad.@{fa-css-prefix}-plus-square:after { content: replace(@fa-var-plus-square, "\\\\", "\\\\10"); }
.fad.@{fa-css-prefix}-podcast:after { content: replace(@fa-var-podcast, "\\\\", "\\\\10"); }
.fad.@{fa-css-prefix}-podium:after { content: replace(@fa-var-podium, "\\\\", "\\\\10"); }
.fad.@{fa-css-prefix}-podium-star:after { content: replace(@fa-var-podium-star, "\\\\", "\\\\10"); }
.fad.@{fa-css-prefix}-poll:after { content: replace(@fa-var-poll, "\\\\", "\\\\10"); }
.fad.@{fa-css-prefix}-poll-h:after { content: replace(@fa-var-poll-h, "\\\\", "\\\\10"); }
.fad.@{fa-css-prefix}-poll-people:after { content: replace(@fa-var-poll-people, "\\\\", "\\\\10"); }
.fad.@{fa-css-prefix}-poo:after { content: replace(@fa-var-poo, "\\\\", "\\\\10"); }
.fad.@{fa-css-prefix}-poo-storm:after { content: replace(@fa-var-poo-storm, "\\\\", "\\\\10"); }
.fad.@{fa-css-prefix}-poop:after { content: replace(@fa-var-poop, "\\\\", "\\\\10"); }
.fad.@{fa-css-prefix}-popcorn:after { content: replace(@fa-var-popcorn, "\\\\", "\\\\10"); }
.fad.@{fa-css-prefix}-portrait:after { content: replace(@fa-var-portrait, "\\\\", "\\\\10"); }
.fad.@{fa-css-prefix}-pound-sign:after { content: replace(@fa-var-pound-sign, "\\\\", "\\\\10"); }
.fad.@{fa-css-prefix}-power-off:after { content: replace(@fa-var-power-off, "\\\\", "\\\\10"); }
.fad.@{fa-css-prefix}-pray:after { content: replace(@fa-var-pray, "\\\\", "\\\\10"); }
.fad.@{fa-css-prefix}-praying-hands:after { content: replace(@fa-var-praying-hands, "\\\\", "\\\\10"); }
.fad.@{fa-css-prefix}-prescription:after { content: replace(@fa-var-prescription, "\\\\", "\\\\10"); }
.fad.@{fa-css-prefix}-prescription-bottle:after { content: replace(@fa-var-prescription-bottle, "\\\\", "\\\\10"); }
.fad.@{fa-css-prefix}-prescription-bottle-alt:after { content: replace(@fa-var-prescription-bottle-alt, "\\\\", "\\\\10"); }
.fad.@{fa-css-prefix}-presentation:after { content: replace(@fa-var-presentation, "\\\\", "\\\\10"); }
.fad.@{fa-css-prefix}-print:after { content: replace(@fa-var-print, "\\\\", "\\\\10"); }
.fad.@{fa-css-prefix}-print-search:after { content: replace(@fa-var-print-search, "\\\\", "\\\\10"); }
.fad.@{fa-css-prefix}-print-slash:after { content: replace(@fa-var-print-slash, "\\\\", "\\\\10"); }
.fad.@{fa-css-prefix}-procedures:after { content: replace(@fa-var-procedures, "\\\\", "\\\\10"); }
.fad.@{fa-css-prefix}-project-diagram:after { content: replace(@fa-var-project-diagram, "\\\\", "\\\\10"); }
.fad.@{fa-css-prefix}-pumpkin:after { content: replace(@fa-var-pumpkin, "\\\\", "\\\\10"); }
.fad.@{fa-css-prefix}-puzzle-piece:after { content: replace(@fa-var-puzzle-piece, "\\\\", "\\\\10"); }
.fad.@{fa-css-prefix}-qrcode:after { content: replace(@fa-var-qrcode, "\\\\", "\\\\10"); }
.fad.@{fa-css-prefix}-question:after { content: replace(@fa-var-question, "\\\\", "\\\\10"); }
.fad.@{fa-css-prefix}-question-circle:after { content: replace(@fa-var-question-circle, "\\\\", "\\\\10"); }
.fad.@{fa-css-prefix}-question-square:after { content: replace(@fa-var-question-square, "\\\\", "\\\\10"); }
.fad.@{fa-css-prefix}-quidditch:after { content: replace(@fa-var-quidditch, "\\\\", "\\\\10"); }
.fad.@{fa-css-prefix}-quote-left:after { content: replace(@fa-var-quote-left, "\\\\", "\\\\10"); }
.fad.@{fa-css-prefix}-quote-right:after { content: replace(@fa-var-quote-right, "\\\\", "\\\\10"); }
.fad.@{fa-css-prefix}-quran:after { content: replace(@fa-var-quran, "\\\\", "\\\\10"); }
.fad.@{fa-css-prefix}-rabbit:after { content: replace(@fa-var-rabbit, "\\\\", "\\\\10"); }
.fad.@{fa-css-prefix}-rabbit-fast:after { content: replace(@fa-var-rabbit-fast, "\\\\", "\\\\10"); }
.fad.@{fa-css-prefix}-racquet:after { content: replace(@fa-var-racquet, "\\\\", "\\\\10"); }
.fad.@{fa-css-prefix}-radiation:after { content: replace(@fa-var-radiation, "\\\\", "\\\\10"); }
.fad.@{fa-css-prefix}-radiation-alt:after { content: replace(@fa-var-radiation-alt, "\\\\", "\\\\10"); }
.fad.@{fa-css-prefix}-rainbow:after { content: replace(@fa-var-rainbow, "\\\\", "\\\\10"); }
.fad.@{fa-css-prefix}-raindrops:after { content: replace(@fa-var-raindrops, "\\\\", "\\\\10"); }
.fad.@{fa-css-prefix}-ram:after { content: replace(@fa-var-ram, "\\\\", "\\\\10"); }
.fad.@{fa-css-prefix}-ramp-loading:after { content: replace(@fa-var-ramp-loading, "\\\\", "\\\\10"); }
.fad.@{fa-css-prefix}-random:after { content: replace(@fa-var-random, "\\\\", "\\\\10"); }
.fad.@{fa-css-prefix}-receipt:after { content: replace(@fa-var-receipt, "\\\\", "\\\\10"); }
.fad.@{fa-css-prefix}-rectangle-landscape:after { content: replace(@fa-var-rectangle-landscape, "\\\\", "\\\\10"); }
.fad.@{fa-css-prefix}-rectangle-portrait:after { content: replace(@fa-var-rectangle-portrait, "\\\\", "\\\\10"); }
.fad.@{fa-css-prefix}-rectangle-wide:after { content: replace(@fa-var-rectangle-wide, "\\\\", "\\\\10"); }
.fad.@{fa-css-prefix}-recycle:after { content: replace(@fa-var-recycle, "\\\\", "\\\\10"); }
.fad.@{fa-css-prefix}-redo:after { content: replace(@fa-var-redo, "\\\\", "\\\\10"); }
.fad.@{fa-css-prefix}-redo-alt:after { content: replace(@fa-var-redo-alt, "\\\\", "\\\\10"); }
.fad.@{fa-css-prefix}-registered:after { content: replace(@fa-var-registered, "\\\\", "\\\\10"); }
.fad.@{fa-css-prefix}-remove-format:after { content: replace(@fa-var-remove-format, "\\\\", "\\\\10"); }
.fad.@{fa-css-prefix}-repeat:after { content: replace(@fa-var-repeat, "\\\\", "\\\\10"); }
.fad.@{fa-css-prefix}-repeat-1:after { content: replace(@fa-var-repeat-1, "\\\\", "\\\\10"); }
.fad.@{fa-css-prefix}-repeat-1-alt:after { content: replace(@fa-var-repeat-1-alt, "\\\\", "\\\\10"); }
.fad.@{fa-css-prefix}-repeat-alt:after { content: replace(@fa-var-repeat-alt, "\\\\", "\\\\10"); }
.fad.@{fa-css-prefix}-reply:after { content: replace(@fa-var-reply, "\\\\", "\\\\10"); }
.fad.@{fa-css-prefix}-reply-all:after { content: replace(@fa-var-reply-all, "\\\\", "\\\\10"); }
.fad.@{fa-css-prefix}-republican:after { content: replace(@fa-var-republican, "\\\\", "\\\\10"); }
.fad.@{fa-css-prefix}-restroom:after { content: replace(@fa-var-restroom, "\\\\", "\\\\10"); }
.fad.@{fa-css-prefix}-retweet:after { content: replace(@fa-var-retweet, "\\\\", "\\\\10"); }
.fad.@{fa-css-prefix}-retweet-alt:after { content: replace(@fa-var-retweet-alt, "\\\\", "\\\\10"); }
.fad.@{fa-css-prefix}-ribbon:after { content: replace(@fa-var-ribbon, "\\\\", "\\\\10"); }
.fad.@{fa-css-prefix}-ring:after { content: replace(@fa-var-ring, "\\\\", "\\\\10"); }
.fad.@{fa-css-prefix}-rings-wedding:after { content: replace(@fa-var-rings-wedding, "\\\\", "\\\\10"); }
.fad.@{fa-css-prefix}-road:after { content: replace(@fa-var-road, "\\\\", "\\\\10"); }
.fad.@{fa-css-prefix}-robot:after { content: replace(@fa-var-robot, "\\\\", "\\\\10"); }
.fad.@{fa-css-prefix}-rocket:after { content: replace(@fa-var-rocket, "\\\\", "\\\\10"); }
.fad.@{fa-css-prefix}-route:after { content: replace(@fa-var-route, "\\\\", "\\\\10"); }
.fad.@{fa-css-prefix}-route-highway:after { content: replace(@fa-var-route-highway, "\\\\", "\\\\10"); }
.fad.@{fa-css-prefix}-route-interstate:after { content: replace(@fa-var-route-interstate, "\\\\", "\\\\10"); }
.fad.@{fa-css-prefix}-rss:after { content: replace(@fa-var-rss, "\\\\", "\\\\10"); }
.fad.@{fa-css-prefix}-rss-square:after { content: replace(@fa-var-rss-square, "\\\\", "\\\\10"); }
.fad.@{fa-css-prefix}-ruble-sign:after { content: replace(@fa-var-ruble-sign, "\\\\", "\\\\10"); }
.fad.@{fa-css-prefix}-ruler:after { content: replace(@fa-var-ruler, "\\\\", "\\\\10"); }
.fad.@{fa-css-prefix}-ruler-combined:after { content: replace(@fa-var-ruler-combined, "\\\\", "\\\\10"); }
.fad.@{fa-css-prefix}-ruler-horizontal:after { content: replace(@fa-var-ruler-horizontal, "\\\\", "\\\\10"); }
.fad.@{fa-css-prefix}-ruler-triangle:after { content: replace(@fa-var-ruler-triangle, "\\\\", "\\\\10"); }
.fad.@{fa-css-prefix}-ruler-vertical:after { content: replace(@fa-var-ruler-vertical, "\\\\", "\\\\10"); }
.fad.@{fa-css-prefix}-running:after { content: replace(@fa-var-running, "\\\\", "\\\\10"); }
.fad.@{fa-css-prefix}-rupee-sign:after { content: replace(@fa-var-rupee-sign, "\\\\", "\\\\10"); }
.fad.@{fa-css-prefix}-rv:after { content: replace(@fa-var-rv, "\\\\", "\\\\10"); }
.fad.@{fa-css-prefix}-sack:after { content: replace(@fa-var-sack, "\\\\", "\\\\10"); }
.fad.@{fa-css-prefix}-sack-dollar:after { content: replace(@fa-var-sack-dollar, "\\\\", "\\\\10"); }
.fad.@{fa-css-prefix}-sad-cry:after { content: replace(@fa-var-sad-cry, "\\\\", "\\\\10"); }
.fad.@{fa-css-prefix}-sad-tear:after { content: replace(@fa-var-sad-tear, "\\\\", "\\\\10"); }
.fad.@{fa-css-prefix}-salad:after { content: replace(@fa-var-salad, "\\\\", "\\\\10"); }
.fad.@{fa-css-prefix}-sandwich:after { content: replace(@fa-var-sandwich, "\\\\", "\\\\10"); }
.fad.@{fa-css-prefix}-satellite:after { content: replace(@fa-var-satellite, "\\\\", "\\\\10"); }
.fad.@{fa-css-prefix}-satellite-dish:after { content: replace(@fa-var-satellite-dish, "\\\\", "\\\\10"); }
.fad.@{fa-css-prefix}-sausage:after { content: replace(@fa-var-sausage, "\\\\", "\\\\10"); }
.fad.@{fa-css-prefix}-save:after { content: replace(@fa-var-save, "\\\\", "\\\\10"); }
.fad.@{fa-css-prefix}-scalpel:after { content: replace(@fa-var-scalpel, "\\\\", "\\\\10"); }
.fad.@{fa-css-prefix}-scalpel-path:after { content: replace(@fa-var-scalpel-path, "\\\\", "\\\\10"); }
.fad.@{fa-css-prefix}-scanner:after { content: replace(@fa-var-scanner, "\\\\", "\\\\10"); }
.fad.@{fa-css-prefix}-scanner-keyboard:after { content: replace(@fa-var-scanner-keyboard, "\\\\", "\\\\10"); }
.fad.@{fa-css-prefix}-scanner-touchscreen:after { content: replace(@fa-var-scanner-touchscreen, "\\\\", "\\\\10"); }
.fad.@{fa-css-prefix}-scarecrow:after { content: replace(@fa-var-scarecrow, "\\\\", "\\\\10"); }
.fad.@{fa-css-prefix}-scarf:after { content: replace(@fa-var-scarf, "\\\\", "\\\\10"); }
.fad.@{fa-css-prefix}-school:after { content: replace(@fa-var-school, "\\\\", "\\\\10"); }
.fad.@{fa-css-prefix}-screwdriver:after { content: replace(@fa-var-screwdriver, "\\\\", "\\\\10"); }
.fad.@{fa-css-prefix}-scroll:after { content: replace(@fa-var-scroll, "\\\\", "\\\\10"); }
.fad.@{fa-css-prefix}-scroll-old:after { content: replace(@fa-var-scroll-old, "\\\\", "\\\\10"); }
.fad.@{fa-css-prefix}-scrubber:after { content: replace(@fa-var-scrubber, "\\\\", "\\\\10"); }
.fad.@{fa-css-prefix}-scythe:after { content: replace(@fa-var-scythe, "\\\\", "\\\\10"); }
.fad.@{fa-css-prefix}-sd-card:after { content: replace(@fa-var-sd-card, "\\\\", "\\\\10"); }
.fad.@{fa-css-prefix}-search:after { content: replace(@fa-var-search, "\\\\", "\\\\10"); }
.fad.@{fa-css-prefix}-search-dollar:after { content: replace(@fa-var-search-dollar, "\\\\", "\\\\10"); }
.fad.@{fa-css-prefix}-search-location:after { content: replace(@fa-var-search-location, "\\\\", "\\\\10"); }
.fad.@{fa-css-prefix}-search-minus:after { content: replace(@fa-var-search-minus, "\\\\", "\\\\10"); }
.fad.@{fa-css-prefix}-search-plus:after { content: replace(@fa-var-search-plus, "\\\\", "\\\\10"); }
.fad.@{fa-css-prefix}-seedling:after { content: replace(@fa-var-seedling, "\\\\", "\\\\10"); }
.fad.@{fa-css-prefix}-send-back:after { content: replace(@fa-var-send-back, "\\\\", "\\\\10"); }
.fad.@{fa-css-prefix}-send-backward:after { content: replace(@fa-var-send-backward, "\\\\", "\\\\10"); }
.fad.@{fa-css-prefix}-server:after { content: replace(@fa-var-server, "\\\\", "\\\\10"); }
.fad.@{fa-css-prefix}-shapes:after { content: replace(@fa-var-shapes, "\\\\", "\\\\10"); }
.fad.@{fa-css-prefix}-share:after { content: replace(@fa-var-share, "\\\\", "\\\\10"); }
.fad.@{fa-css-prefix}-share-all:after { content: replace(@fa-var-share-all, "\\\\", "\\\\10"); }
.fad.@{fa-css-prefix}-share-alt:after { content: replace(@fa-var-share-alt, "\\\\", "\\\\10"); }
.fad.@{fa-css-prefix}-share-alt-square:after { content: replace(@fa-var-share-alt-square, "\\\\", "\\\\10"); }
.fad.@{fa-css-prefix}-share-square:after { content: replace(@fa-var-share-square, "\\\\", "\\\\10"); }
.fad.@{fa-css-prefix}-sheep:after { content: replace(@fa-var-sheep, "\\\\", "\\\\10"); }
.fad.@{fa-css-prefix}-shekel-sign:after { content: replace(@fa-var-shekel-sign, "\\\\", "\\\\10"); }
.fad.@{fa-css-prefix}-shield:after { content: replace(@fa-var-shield, "\\\\", "\\\\10"); }
.fad.@{fa-css-prefix}-shield-alt:after { content: replace(@fa-var-shield-alt, "\\\\", "\\\\10"); }
.fad.@{fa-css-prefix}-shield-check:after { content: replace(@fa-var-shield-check, "\\\\", "\\\\10"); }
.fad.@{fa-css-prefix}-shield-cross:after { content: replace(@fa-var-shield-cross, "\\\\", "\\\\10"); }
.fad.@{fa-css-prefix}-ship:after { content: replace(@fa-var-ship, "\\\\", "\\\\10"); }
.fad.@{fa-css-prefix}-shipping-fast:after { content: replace(@fa-var-shipping-fast, "\\\\", "\\\\10"); }
.fad.@{fa-css-prefix}-shipping-timed:after { content: replace(@fa-var-shipping-timed, "\\\\", "\\\\10"); }
.fad.@{fa-css-prefix}-shish-kebab:after { content: replace(@fa-var-shish-kebab, "\\\\", "\\\\10"); }
.fad.@{fa-css-prefix}-shoe-prints:after { content: replace(@fa-var-shoe-prints, "\\\\", "\\\\10"); }
.fad.@{fa-css-prefix}-shopping-bag:after { content: replace(@fa-var-shopping-bag, "\\\\", "\\\\10"); }
.fad.@{fa-css-prefix}-shopping-basket:after { content: replace(@fa-var-shopping-basket, "\\\\", "\\\\10"); }
.fad.@{fa-css-prefix}-shopping-cart:after { content: replace(@fa-var-shopping-cart, "\\\\", "\\\\10"); }
.fad.@{fa-css-prefix}-shovel:after { content: replace(@fa-var-shovel, "\\\\", "\\\\10"); }
.fad.@{fa-css-prefix}-shovel-snow:after { content: replace(@fa-var-shovel-snow, "\\\\", "\\\\10"); }
.fad.@{fa-css-prefix}-shower:after { content: replace(@fa-var-shower, "\\\\", "\\\\10"); }
.fad.@{fa-css-prefix}-shredder:after { content: replace(@fa-var-shredder, "\\\\", "\\\\10"); }
.fad.@{fa-css-prefix}-shuttle-van:after { content: replace(@fa-var-shuttle-van, "\\\\", "\\\\10"); }
.fad.@{fa-css-prefix}-shuttlecock:after { content: replace(@fa-var-shuttlecock, "\\\\", "\\\\10"); }
.fad.@{fa-css-prefix}-sickle:after { content: replace(@fa-var-sickle, "\\\\", "\\\\10"); }
.fad.@{fa-css-prefix}-sigma:after { content: replace(@fa-var-sigma, "\\\\", "\\\\10"); }
.fad.@{fa-css-prefix}-sign:after { content: replace(@fa-var-sign, "\\\\", "\\\\10"); }
.fad.@{fa-css-prefix}-sign-in:after { content: replace(@fa-var-sign-in, "\\\\", "\\\\10"); }
.fad.@{fa-css-prefix}-sign-in-alt:after { content: replace(@fa-var-sign-in-alt, "\\\\", "\\\\10"); }
.fad.@{fa-css-prefix}-sign-language:after { content: replace(@fa-var-sign-language, "\\\\", "\\\\10"); }
.fad.@{fa-css-prefix}-sign-out:after { content: replace(@fa-var-sign-out, "\\\\", "\\\\10"); }
.fad.@{fa-css-prefix}-sign-out-alt:after { content: replace(@fa-var-sign-out-alt, "\\\\", "\\\\10"); }
.fad.@{fa-css-prefix}-signal:after { content: replace(@fa-var-signal, "\\\\", "\\\\10"); }
.fad.@{fa-css-prefix}-signal-1:after { content: replace(@fa-var-signal-1, "\\\\", "\\\\10"); }
.fad.@{fa-css-prefix}-signal-2:after { content: replace(@fa-var-signal-2, "\\\\", "\\\\10"); }
.fad.@{fa-css-prefix}-signal-3:after { content: replace(@fa-var-signal-3, "\\\\", "\\\\10"); }
.fad.@{fa-css-prefix}-signal-4:after { content: replace(@fa-var-signal-4, "\\\\", "\\\\10"); }
.fad.@{fa-css-prefix}-signal-alt:after { content: replace(@fa-var-signal-alt, "\\\\", "\\\\10"); }
.fad.@{fa-css-prefix}-signal-alt-1:after { content: replace(@fa-var-signal-alt-1, "\\\\", "\\\\10"); }
.fad.@{fa-css-prefix}-signal-alt-2:after { content: replace(@fa-var-signal-alt-2, "\\\\", "\\\\10"); }
.fad.@{fa-css-prefix}-signal-alt-3:after { content: replace(@fa-var-signal-alt-3, "\\\\", "\\\\10"); }
.fad.@{fa-css-prefix}-signal-alt-slash:after { content: replace(@fa-var-signal-alt-slash, "\\\\", "\\\\10"); }
.fad.@{fa-css-prefix}-signal-slash:after { content: replace(@fa-var-signal-slash, "\\\\", "\\\\10"); }
.fad.@{fa-css-prefix}-signature:after { content: replace(@fa-var-signature, "\\\\", "\\\\10"); }
.fad.@{fa-css-prefix}-sim-card:after { content: replace(@fa-var-sim-card, "\\\\", "\\\\10"); }
.fad.@{fa-css-prefix}-sitemap:after { content: replace(@fa-var-sitemap, "\\\\", "\\\\10"); }
.fad.@{fa-css-prefix}-skating:after { content: replace(@fa-var-skating, "\\\\", "\\\\10"); }
.fad.@{fa-css-prefix}-skeleton:after { content: replace(@fa-var-skeleton, "\\\\", "\\\\10"); }
.fad.@{fa-css-prefix}-ski-jump:after { content: replace(@fa-var-ski-jump, "\\\\", "\\\\10"); }
.fad.@{fa-css-prefix}-ski-lift:after { content: replace(@fa-var-ski-lift, "\\\\", "\\\\10"); }
.fad.@{fa-css-prefix}-skiing:after { content: replace(@fa-var-skiing, "\\\\", "\\\\10"); }
.fad.@{fa-css-prefix}-skiing-nordic:after { content: replace(@fa-var-skiing-nordic, "\\\\", "\\\\10"); }
.fad.@{fa-css-prefix}-skull:after { content: replace(@fa-var-skull, "\\\\", "\\\\10"); }
.fad.@{fa-css-prefix}-skull-crossbones:after { content: replace(@fa-var-skull-crossbones, "\\\\", "\\\\10"); }
.fad.@{fa-css-prefix}-slash:after { content: replace(@fa-var-slash, "\\\\", "\\\\10"); }
.fad.@{fa-css-prefix}-sledding:after { content: replace(@fa-var-sledding, "\\\\", "\\\\10"); }
.fad.@{fa-css-prefix}-sleigh:after { content: replace(@fa-var-sleigh, "\\\\", "\\\\10"); }
.fad.@{fa-css-prefix}-sliders-h:after { content: replace(@fa-var-sliders-h, "\\\\", "\\\\10"); }
.fad.@{fa-css-prefix}-sliders-h-square:after { content: replace(@fa-var-sliders-h-square, "\\\\", "\\\\10"); }
.fad.@{fa-css-prefix}-sliders-v:after { content: replace(@fa-var-sliders-v, "\\\\", "\\\\10"); }
.fad.@{fa-css-prefix}-sliders-v-square:after { content: replace(@fa-var-sliders-v-square, "\\\\", "\\\\10"); }
.fad.@{fa-css-prefix}-smile:after { content: replace(@fa-var-smile, "\\\\", "\\\\10"); }
.fad.@{fa-css-prefix}-smile-beam:after { content: replace(@fa-var-smile-beam, "\\\\", "\\\\10"); }
.fad.@{fa-css-prefix}-smile-plus:after { content: replace(@fa-var-smile-plus, "\\\\", "\\\\10"); }
.fad.@{fa-css-prefix}-smile-wink:after { content: replace(@fa-var-smile-wink, "\\\\", "\\\\10"); }
.fad.@{fa-css-prefix}-smog:after { content: replace(@fa-var-smog, "\\\\", "\\\\10"); }
.fad.@{fa-css-prefix}-smoke:after { content: replace(@fa-var-smoke, "\\\\", "\\\\10"); }
.fad.@{fa-css-prefix}-smoking:after { content: replace(@fa-var-smoking, "\\\\", "\\\\10"); }
.fad.@{fa-css-prefix}-smoking-ban:after { content: replace(@fa-var-smoking-ban, "\\\\", "\\\\10"); }
.fad.@{fa-css-prefix}-sms:after { content: replace(@fa-var-sms, "\\\\", "\\\\10"); }
.fad.@{fa-css-prefix}-snake:after { content: replace(@fa-var-snake, "\\\\", "\\\\10"); }
.fad.@{fa-css-prefix}-snooze:after { content: replace(@fa-var-snooze, "\\\\", "\\\\10"); }
.fad.@{fa-css-prefix}-snow-blowing:after { content: replace(@fa-var-snow-blowing, "\\\\", "\\\\10"); }
.fad.@{fa-css-prefix}-snowboarding:after { content: replace(@fa-var-snowboarding, "\\\\", "\\\\10"); }
.fad.@{fa-css-prefix}-snowflake:after { content: replace(@fa-var-snowflake, "\\\\", "\\\\10"); }
.fad.@{fa-css-prefix}-snowflakes:after { content: replace(@fa-var-snowflakes, "\\\\", "\\\\10"); }
.fad.@{fa-css-prefix}-snowman:after { content: replace(@fa-var-snowman, "\\\\", "\\\\10"); }
.fad.@{fa-css-prefix}-snowmobile:after { content: replace(@fa-var-snowmobile, "\\\\", "\\\\10"); }
.fad.@{fa-css-prefix}-snowplow:after { content: replace(@fa-var-snowplow, "\\\\", "\\\\10"); }
.fad.@{fa-css-prefix}-socks:after { content: replace(@fa-var-socks, "\\\\", "\\\\10"); }
.fad.@{fa-css-prefix}-solar-panel:after { content: replace(@fa-var-solar-panel, "\\\\", "\\\\10"); }
.fad.@{fa-css-prefix}-sort:after { content: replace(@fa-var-sort, "\\\\", "\\\\10"); }
.fad.@{fa-css-prefix}-sort-alpha-down:after { content: replace(@fa-var-sort-alpha-down, "\\\\", "\\\\10"); }
.fad.@{fa-css-prefix}-sort-alpha-down-alt:after { content: replace(@fa-var-sort-alpha-down-alt, "\\\\", "\\\\10"); }
.fad.@{fa-css-prefix}-sort-alpha-up:after { content: replace(@fa-var-sort-alpha-up, "\\\\", "\\\\10"); }
.fad.@{fa-css-prefix}-sort-alpha-up-alt:after { content: replace(@fa-var-sort-alpha-up-alt, "\\\\", "\\\\10"); }
.fad.@{fa-css-prefix}-sort-alt:after { content: replace(@fa-var-sort-alt, "\\\\", "\\\\10"); }
.fad.@{fa-css-prefix}-sort-amount-down:after { content: replace(@fa-var-sort-amount-down, "\\\\", "\\\\10"); }
.fad.@{fa-css-prefix}-sort-amount-down-alt:after { content: replace(@fa-var-sort-amount-down-alt, "\\\\", "\\\\10"); }
.fad.@{fa-css-prefix}-sort-amount-up:after { content: replace(@fa-var-sort-amount-up, "\\\\", "\\\\10"); }
.fad.@{fa-css-prefix}-sort-amount-up-alt:after { content: replace(@fa-var-sort-amount-up-alt, "\\\\", "\\\\10"); }
.fad.@{fa-css-prefix}-sort-down:after { content: replace(@fa-var-sort-down, "\\\\", "\\\\10"); }
.fad.@{fa-css-prefix}-sort-numeric-down:after { content: replace(@fa-var-sort-numeric-down, "\\\\", "\\\\10"); }
.fad.@{fa-css-prefix}-sort-numeric-down-alt:after { content: replace(@fa-var-sort-numeric-down-alt, "\\\\", "\\\\10"); }
.fad.@{fa-css-prefix}-sort-numeric-up:after { content: replace(@fa-var-sort-numeric-up, "\\\\", "\\\\10"); }
.fad.@{fa-css-prefix}-sort-numeric-up-alt:after { content: replace(@fa-var-sort-numeric-up-alt, "\\\\", "\\\\10"); }
.fad.@{fa-css-prefix}-sort-shapes-down:after { content: replace(@fa-var-sort-shapes-down, "\\\\", "\\\\10"); }
.fad.@{fa-css-prefix}-sort-shapes-down-alt:after { content: replace(@fa-var-sort-shapes-down-alt, "\\\\", "\\\\10"); }
.fad.@{fa-css-prefix}-sort-shapes-up:after { content: replace(@fa-var-sort-shapes-up, "\\\\", "\\\\10"); }
.fad.@{fa-css-prefix}-sort-shapes-up-alt:after { content: replace(@fa-var-sort-shapes-up-alt, "\\\\", "\\\\10"); }
.fad.@{fa-css-prefix}-sort-size-down:after { content: replace(@fa-var-sort-size-down, "\\\\", "\\\\10"); }
.fad.@{fa-css-prefix}-sort-size-down-alt:after { content: replace(@fa-var-sort-size-down-alt, "\\\\", "\\\\10"); }
.fad.@{fa-css-prefix}-sort-size-up:after { content: replace(@fa-var-sort-size-up, "\\\\", "\\\\10"); }
.fad.@{fa-css-prefix}-sort-size-up-alt:after { content: replace(@fa-var-sort-size-up-alt, "\\\\", "\\\\10"); }
.fad.@{fa-css-prefix}-sort-up:after { content: replace(@fa-var-sort-up, "\\\\", "\\\\10"); }
.fad.@{fa-css-prefix}-soup:after { content: replace(@fa-var-soup, "\\\\", "\\\\10"); }
.fad.@{fa-css-prefix}-spa:after { content: replace(@fa-var-spa, "\\\\", "\\\\10"); }
.fad.@{fa-css-prefix}-space-shuttle:after { content: replace(@fa-var-space-shuttle, "\\\\", "\\\\10"); }
.fad.@{fa-css-prefix}-spade:after { content: replace(@fa-var-spade, "\\\\", "\\\\10"); }
.fad.@{fa-css-prefix}-sparkles:after { content: replace(@fa-var-sparkles, "\\\\", "\\\\10"); }
.fad.@{fa-css-prefix}-spell-check:after { content: replace(@fa-var-spell-check, "\\\\", "\\\\10"); }
.fad.@{fa-css-prefix}-spider:after { content: replace(@fa-var-spider, "\\\\", "\\\\10"); }
.fad.@{fa-css-prefix}-spider-black-widow:after { content: replace(@fa-var-spider-black-widow, "\\\\", "\\\\10"); }
.fad.@{fa-css-prefix}-spider-web:after { content: replace(@fa-var-spider-web, "\\\\", "\\\\10"); }
.fad.@{fa-css-prefix}-spinner:after { content: replace(@fa-var-spinner, "\\\\", "\\\\10"); }
.fad.@{fa-css-prefix}-spinner-third:after { content: replace(@fa-var-spinner-third, "\\\\", "\\\\10"); }
.fad.@{fa-css-prefix}-splotch:after { content: replace(@fa-var-splotch, "\\\\", "\\\\10"); }
.fad.@{fa-css-prefix}-spray-can:after { content: replace(@fa-var-spray-can, "\\\\", "\\\\10"); }
.fad.@{fa-css-prefix}-square:after { content: replace(@fa-var-square, "\\\\", "\\\\10"); }
.fad.@{fa-css-prefix}-square-full:after { content: replace(@fa-var-square-full, "\\\\", "\\\\10"); }
.fad.@{fa-css-prefix}-square-root:after { content: replace(@fa-var-square-root, "\\\\", "\\\\10"); }
.fad.@{fa-css-prefix}-square-root-alt:after { content: replace(@fa-var-square-root-alt, "\\\\", "\\\\10"); }
.fad.@{fa-css-prefix}-squirrel:after { content: replace(@fa-var-squirrel, "\\\\", "\\\\10"); }
.fad.@{fa-css-prefix}-staff:after { content: replace(@fa-var-staff, "\\\\", "\\\\10"); }
.fad.@{fa-css-prefix}-stamp:after { content: replace(@fa-var-stamp, "\\\\", "\\\\10"); }
.fad.@{fa-css-prefix}-star:after { content: replace(@fa-var-star, "\\\\", "\\\\10"); }
.fad.@{fa-css-prefix}-star-and-crescent:after { content: replace(@fa-var-star-and-crescent, "\\\\", "\\\\10"); }
.fad.@{fa-css-prefix}-star-christmas:after { content: replace(@fa-var-star-christmas, "\\\\", "\\\\10"); }
.fad.@{fa-css-prefix}-star-exclamation:after { content: replace(@fa-var-star-exclamation, "\\\\", "\\\\10"); }
.fad.@{fa-css-prefix}-star-half:after { content: replace(@fa-var-star-half, "\\\\", "\\\\10"); }
.fad.@{fa-css-prefix}-star-half-alt:after { content: replace(@fa-var-star-half-alt, "\\\\", "\\\\10"); }
.fad.@{fa-css-prefix}-star-of-david:after { content: replace(@fa-var-star-of-david, "\\\\", "\\\\10"); }
.fad.@{fa-css-prefix}-star-of-life:after { content: replace(@fa-var-star-of-life, "\\\\", "\\\\10"); }
.fad.@{fa-css-prefix}-stars:after { content: replace(@fa-var-stars, "\\\\", "\\\\10"); }
.fad.@{fa-css-prefix}-steak:after { content: replace(@fa-var-steak, "\\\\", "\\\\10"); }
.fad.@{fa-css-prefix}-steering-wheel:after { content: replace(@fa-var-steering-wheel, "\\\\", "\\\\10"); }
.fad.@{fa-css-prefix}-step-backward:after { content: replace(@fa-var-step-backward, "\\\\", "\\\\10"); }
.fad.@{fa-css-prefix}-step-forward:after { content: replace(@fa-var-step-forward, "\\\\", "\\\\10"); }
.fad.@{fa-css-prefix}-stethoscope:after { content: replace(@fa-var-stethoscope, "\\\\", "\\\\10"); }
.fad.@{fa-css-prefix}-sticky-note:after { content: replace(@fa-var-sticky-note, "\\\\", "\\\\10"); }
.fad.@{fa-css-prefix}-stocking:after { content: replace(@fa-var-stocking, "\\\\", "\\\\10"); }
.fad.@{fa-css-prefix}-stomach:after { content: replace(@fa-var-stomach, "\\\\", "\\\\10"); }
.fad.@{fa-css-prefix}-stop:after { content: replace(@fa-var-stop, "\\\\", "\\\\10"); }
.fad.@{fa-css-prefix}-stop-circle:after { content: replace(@fa-var-stop-circle, "\\\\", "\\\\10"); }
.fad.@{fa-css-prefix}-stopwatch:after { content: replace(@fa-var-stopwatch, "\\\\", "\\\\10"); }
.fad.@{fa-css-prefix}-store:after { content: replace(@fa-var-store, "\\\\", "\\\\10"); }
.fad.@{fa-css-prefix}-store-alt:after { content: replace(@fa-var-store-alt, "\\\\", "\\\\10"); }
.fad.@{fa-css-prefix}-stream:after { content: replace(@fa-var-stream, "\\\\", "\\\\10"); }
.fad.@{fa-css-prefix}-street-view:after { content: replace(@fa-var-street-view, "\\\\", "\\\\10"); }
.fad.@{fa-css-prefix}-stretcher:after { content: replace(@fa-var-stretcher, "\\\\", "\\\\10"); }
.fad.@{fa-css-prefix}-strikethrough:after { content: replace(@fa-var-strikethrough, "\\\\", "\\\\10"); }
.fad.@{fa-css-prefix}-stroopwafel:after { content: replace(@fa-var-stroopwafel, "\\\\", "\\\\10"); }
.fad.@{fa-css-prefix}-subscript:after { content: replace(@fa-var-subscript, "\\\\", "\\\\10"); }
.fad.@{fa-css-prefix}-subway:after { content: replace(@fa-var-subway, "\\\\", "\\\\10"); }
.fad.@{fa-css-prefix}-suitcase:after { content: replace(@fa-var-suitcase, "\\\\", "\\\\10"); }
.fad.@{fa-css-prefix}-suitcase-rolling:after { content: replace(@fa-var-suitcase-rolling, "\\\\", "\\\\10"); }
.fad.@{fa-css-prefix}-sun:after { content: replace(@fa-var-sun, "\\\\", "\\\\10"); }
.fad.@{fa-css-prefix}-sun-cloud:after { content: replace(@fa-var-sun-cloud, "\\\\", "\\\\10"); }
.fad.@{fa-css-prefix}-sun-dust:after { content: replace(@fa-var-sun-dust, "\\\\", "\\\\10"); }
.fad.@{fa-css-prefix}-sun-haze:after { content: replace(@fa-var-sun-haze, "\\\\", "\\\\10"); }
.fad.@{fa-css-prefix}-sunglasses:after { content: replace(@fa-var-sunglasses, "\\\\", "\\\\10"); }
.fad.@{fa-css-prefix}-sunrise:after { content: replace(@fa-var-sunrise, "\\\\", "\\\\10"); }
.fad.@{fa-css-prefix}-sunset:after { content: replace(@fa-var-sunset, "\\\\", "\\\\10"); }
.fad.@{fa-css-prefix}-superscript:after { content: replace(@fa-var-superscript, "\\\\", "\\\\10"); }
.fad.@{fa-css-prefix}-surprise:after { content: replace(@fa-var-surprise, "\\\\", "\\\\10"); }
.fad.@{fa-css-prefix}-swatchbook:after { content: replace(@fa-var-swatchbook, "\\\\", "\\\\10"); }
.fad.@{fa-css-prefix}-swimmer:after { content: replace(@fa-var-swimmer, "\\\\", "\\\\10"); }
.fad.@{fa-css-prefix}-swimming-pool:after { content: replace(@fa-var-swimming-pool, "\\\\", "\\\\10"); }
.fad.@{fa-css-prefix}-sword:after { content: replace(@fa-var-sword, "\\\\", "\\\\10"); }
.fad.@{fa-css-prefix}-swords:after { content: replace(@fa-var-swords, "\\\\", "\\\\10"); }
.fad.@{fa-css-prefix}-synagogue:after { content: replace(@fa-var-synagogue, "\\\\", "\\\\10"); }
.fad.@{fa-css-prefix}-sync:after { content: replace(@fa-var-sync, "\\\\", "\\\\10"); }
.fad.@{fa-css-prefix}-sync-alt:after { content: replace(@fa-var-sync-alt, "\\\\", "\\\\10"); }
.fad.@{fa-css-prefix}-syringe:after { content: replace(@fa-var-syringe, "\\\\", "\\\\10"); }
.fad.@{fa-css-prefix}-table:after { content: replace(@fa-var-table, "\\\\", "\\\\10"); }
.fad.@{fa-css-prefix}-table-tennis:after { content: replace(@fa-var-table-tennis, "\\\\", "\\\\10"); }
.fad.@{fa-css-prefix}-tablet:after { content: replace(@fa-var-tablet, "\\\\", "\\\\10"); }
.fad.@{fa-css-prefix}-tablet-alt:after { content: replace(@fa-var-tablet-alt, "\\\\", "\\\\10"); }
.fad.@{fa-css-prefix}-tablet-android:after { content: replace(@fa-var-tablet-android, "\\\\", "\\\\10"); }
.fad.@{fa-css-prefix}-tablet-android-alt:after { content: replace(@fa-var-tablet-android-alt, "\\\\", "\\\\10"); }
.fad.@{fa-css-prefix}-tablet-rugged:after { content: replace(@fa-var-tablet-rugged, "\\\\", "\\\\10"); }
.fad.@{fa-css-prefix}-tablets:after { content: replace(@fa-var-tablets, "\\\\", "\\\\10"); }
.fad.@{fa-css-prefix}-tachometer:after { content: replace(@fa-var-tachometer, "\\\\", "\\\\10"); }
.fad.@{fa-css-prefix}-tachometer-alt:after { content: replace(@fa-var-tachometer-alt, "\\\\", "\\\\10"); }
.fad.@{fa-css-prefix}-tachometer-alt-average:after { content: replace(@fa-var-tachometer-alt-average, "\\\\", "\\\\10"); }
.fad.@{fa-css-prefix}-tachometer-alt-fast:after { content: replace(@fa-var-tachometer-alt-fast, "\\\\", "\\\\10"); }
.fad.@{fa-css-prefix}-tachometer-alt-fastest:after { content: replace(@fa-var-tachometer-alt-fastest, "\\\\", "\\\\10"); }
.fad.@{fa-css-prefix}-tachometer-alt-slow:after { content: replace(@fa-var-tachometer-alt-slow, "\\\\", "\\\\10"); }
.fad.@{fa-css-prefix}-tachometer-alt-slowest:after { content: replace(@fa-var-tachometer-alt-slowest, "\\\\", "\\\\10"); }
.fad.@{fa-css-prefix}-tachometer-average:after { content: replace(@fa-var-tachometer-average, "\\\\", "\\\\10"); }
.fad.@{fa-css-prefix}-tachometer-fast:after { content: replace(@fa-var-tachometer-fast, "\\\\", "\\\\10"); }
.fad.@{fa-css-prefix}-tachometer-fastest:after { content: replace(@fa-var-tachometer-fastest, "\\\\", "\\\\10"); }
.fad.@{fa-css-prefix}-tachometer-slow:after { content: replace(@fa-var-tachometer-slow, "\\\\", "\\\\10"); }
.fad.@{fa-css-prefix}-tachometer-slowest:after { content: replace(@fa-var-tachometer-slowest, "\\\\", "\\\\10"); }
.fad.@{fa-css-prefix}-taco:after { content: replace(@fa-var-taco, "\\\\", "\\\\10"); }
.fad.@{fa-css-prefix}-tag:after { content: replace(@fa-var-tag, "\\\\", "\\\\10"); }
.fad.@{fa-css-prefix}-tags:after { content: replace(@fa-var-tags, "\\\\", "\\\\10"); }
.fad.@{fa-css-prefix}-tally:after { content: replace(@fa-var-tally, "\\\\", "\\\\10"); }
.fad.@{fa-css-prefix}-tanakh:after { content: replace(@fa-var-tanakh, "\\\\", "\\\\10"); }
.fad.@{fa-css-prefix}-tape:after { content: replace(@fa-var-tape, "\\\\", "\\\\10"); }
.fad.@{fa-css-prefix}-tasks:after { content: replace(@fa-var-tasks, "\\\\", "\\\\10"); }
.fad.@{fa-css-prefix}-tasks-alt:after { content: replace(@fa-var-tasks-alt, "\\\\", "\\\\10"); }
.fad.@{fa-css-prefix}-taxi:after { content: replace(@fa-var-taxi, "\\\\", "\\\\10"); }
.fad.@{fa-css-prefix}-teeth:after { content: replace(@fa-var-teeth, "\\\\", "\\\\10"); }
.fad.@{fa-css-prefix}-teeth-open:after { content: replace(@fa-var-teeth-open, "\\\\", "\\\\10"); }
.fad.@{fa-css-prefix}-temperature-frigid:after { content: replace(@fa-var-temperature-frigid, "\\\\", "\\\\10"); }
.fad.@{fa-css-prefix}-temperature-high:after { content: replace(@fa-var-temperature-high, "\\\\", "\\\\10"); }
.fad.@{fa-css-prefix}-temperature-hot:after { content: replace(@fa-var-temperature-hot, "\\\\", "\\\\10"); }
.fad.@{fa-css-prefix}-temperature-low:after { content: replace(@fa-var-temperature-low, "\\\\", "\\\\10"); }
.fad.@{fa-css-prefix}-tenge:after { content: replace(@fa-var-tenge, "\\\\", "\\\\10"); }
.fad.@{fa-css-prefix}-tennis-ball:after { content: replace(@fa-var-tennis-ball, "\\\\", "\\\\10"); }
.fad.@{fa-css-prefix}-terminal:after { content: replace(@fa-var-terminal, "\\\\", "\\\\10"); }
.fad.@{fa-css-prefix}-text:after { content: replace(@fa-var-text, "\\\\", "\\\\10"); }
.fad.@{fa-css-prefix}-text-height:after { content: replace(@fa-var-text-height, "\\\\", "\\\\10"); }
.fad.@{fa-css-prefix}-text-size:after { content: replace(@fa-var-text-size, "\\\\", "\\\\10"); }
.fad.@{fa-css-prefix}-text-width:after { content: replace(@fa-var-text-width, "\\\\", "\\\\10"); }
.fad.@{fa-css-prefix}-th:after { content: replace(@fa-var-th, "\\\\", "\\\\10"); }
.fad.@{fa-css-prefix}-th-large:after { content: replace(@fa-var-th-large, "\\\\", "\\\\10"); }
.fad.@{fa-css-prefix}-th-list:after { content: replace(@fa-var-th-list, "\\\\", "\\\\10"); }
.fad.@{fa-css-prefix}-theater-masks:after { content: replace(@fa-var-theater-masks, "\\\\", "\\\\10"); }
.fad.@{fa-css-prefix}-thermometer:after { content: replace(@fa-var-thermometer, "\\\\", "\\\\10"); }
.fad.@{fa-css-prefix}-thermometer-empty:after { content: replace(@fa-var-thermometer-empty, "\\\\", "\\\\10"); }
.fad.@{fa-css-prefix}-thermometer-full:after { content: replace(@fa-var-thermometer-full, "\\\\", "\\\\10"); }
.fad.@{fa-css-prefix}-thermometer-half:after { content: replace(@fa-var-thermometer-half, "\\\\", "\\\\10"); }
.fad.@{fa-css-prefix}-thermometer-quarter:after { content: replace(@fa-var-thermometer-quarter, "\\\\", "\\\\10"); }
.fad.@{fa-css-prefix}-thermometer-three-quarters:after { content: replace(@fa-var-thermometer-three-quarters, "\\\\", "\\\\10"); }
.fad.@{fa-css-prefix}-theta:after { content: replace(@fa-var-theta, "\\\\", "\\\\10"); }
.fad.@{fa-css-prefix}-thumbs-down:after { content: replace(@fa-var-thumbs-down, "\\\\", "\\\\10"); }
.fad.@{fa-css-prefix}-thumbs-up:after { content: replace(@fa-var-thumbs-up, "\\\\", "\\\\10"); }
.fad.@{fa-css-prefix}-thumbtack:after { content: replace(@fa-var-thumbtack, "\\\\", "\\\\10"); }
.fad.@{fa-css-prefix}-thunderstorm:after { content: replace(@fa-var-thunderstorm, "\\\\", "\\\\10"); }
.fad.@{fa-css-prefix}-thunderstorm-moon:after { content: replace(@fa-var-thunderstorm-moon, "\\\\", "\\\\10"); }
.fad.@{fa-css-prefix}-thunderstorm-sun:after { content: replace(@fa-var-thunderstorm-sun, "\\\\", "\\\\10"); }
.fad.@{fa-css-prefix}-ticket:after { content: replace(@fa-var-ticket, "\\\\", "\\\\10"); }
.fad.@{fa-css-prefix}-ticket-alt:after { content: replace(@fa-var-ticket-alt, "\\\\", "\\\\10"); }
.fad.@{fa-css-prefix}-tilde:after { content: replace(@fa-var-tilde, "\\\\", "\\\\10"); }
.fad.@{fa-css-prefix}-times:after { content: replace(@fa-var-times, "\\\\", "\\\\10"); }
.fad.@{fa-css-prefix}-times-circle:after { content: replace(@fa-var-times-circle, "\\\\", "\\\\10"); }
.fad.@{fa-css-prefix}-times-hexagon:after { content: replace(@fa-var-times-hexagon, "\\\\", "\\\\10"); }
.fad.@{fa-css-prefix}-times-octagon:after { content: replace(@fa-var-times-octagon, "\\\\", "\\\\10"); }
.fad.@{fa-css-prefix}-times-square:after { content: replace(@fa-var-times-square, "\\\\", "\\\\10"); }
.fad.@{fa-css-prefix}-tint:after { content: replace(@fa-var-tint, "\\\\", "\\\\10"); }
.fad.@{fa-css-prefix}-tint-slash:after { content: replace(@fa-var-tint-slash, "\\\\", "\\\\10"); }
.fad.@{fa-css-prefix}-tire:after { content: replace(@fa-var-tire, "\\\\", "\\\\10"); }
.fad.@{fa-css-prefix}-tire-flat:after { content: replace(@fa-var-tire-flat, "\\\\", "\\\\10"); }
.fad.@{fa-css-prefix}-tire-pressure-warning:after { content: replace(@fa-var-tire-pressure-warning, "\\\\", "\\\\10"); }
.fad.@{fa-css-prefix}-tire-rugged:after { content: replace(@fa-var-tire-rugged, "\\\\", "\\\\10"); }
.fad.@{fa-css-prefix}-tired:after { content: replace(@fa-var-tired, "\\\\", "\\\\10"); }
.fad.@{fa-css-prefix}-toggle-off:after { content: replace(@fa-var-toggle-off, "\\\\", "\\\\10"); }
.fad.@{fa-css-prefix}-toggle-on:after { content: replace(@fa-var-toggle-on, "\\\\", "\\\\10"); }
.fad.@{fa-css-prefix}-toilet:after { content: replace(@fa-var-toilet, "\\\\", "\\\\10"); }
.fad.@{fa-css-prefix}-toilet-paper:after { content: replace(@fa-var-toilet-paper, "\\\\", "\\\\10"); }
.fad.@{fa-css-prefix}-toilet-paper-alt:after { content: replace(@fa-var-toilet-paper-alt, "\\\\", "\\\\10"); }
.fad.@{fa-css-prefix}-tombstone:after { content: replace(@fa-var-tombstone, "\\\\", "\\\\10"); }
.fad.@{fa-css-prefix}-tombstone-alt:after { content: replace(@fa-var-tombstone-alt, "\\\\", "\\\\10"); }
.fad.@{fa-css-prefix}-toolbox:after { content: replace(@fa-var-toolbox, "\\\\", "\\\\10"); }
.fad.@{fa-css-prefix}-tools:after { content: replace(@fa-var-tools, "\\\\", "\\\\10"); }
.fad.@{fa-css-prefix}-tooth:after { content: replace(@fa-var-tooth, "\\\\", "\\\\10"); }
.fad.@{fa-css-prefix}-toothbrush:after { content: replace(@fa-var-toothbrush, "\\\\", "\\\\10"); }
.fad.@{fa-css-prefix}-torah:after { content: replace(@fa-var-torah, "\\\\", "\\\\10"); }
.fad.@{fa-css-prefix}-torii-gate:after { content: replace(@fa-var-torii-gate, "\\\\", "\\\\10"); }
.fad.@{fa-css-prefix}-tornado:after { content: replace(@fa-var-tornado, "\\\\", "\\\\10"); }
.fad.@{fa-css-prefix}-tractor:after { content: replace(@fa-var-tractor, "\\\\", "\\\\10"); }
.fad.@{fa-css-prefix}-trademark:after { content: replace(@fa-var-trademark, "\\\\", "\\\\10"); }
.fad.@{fa-css-prefix}-traffic-cone:after { content: replace(@fa-var-traffic-cone, "\\\\", "\\\\10"); }
.fad.@{fa-css-prefix}-traffic-light:after { content: replace(@fa-var-traffic-light, "\\\\", "\\\\10"); }
.fad.@{fa-css-prefix}-traffic-light-go:after { content: replace(@fa-var-traffic-light-go, "\\\\", "\\\\10"); }
.fad.@{fa-css-prefix}-traffic-light-slow:after { content: replace(@fa-var-traffic-light-slow, "\\\\", "\\\\10"); }
.fad.@{fa-css-prefix}-traffic-light-stop:after { content: replace(@fa-var-traffic-light-stop, "\\\\", "\\\\10"); }
.fad.@{fa-css-prefix}-train:after { content: replace(@fa-var-train, "\\\\", "\\\\10"); }
.fad.@{fa-css-prefix}-tram:after { content: replace(@fa-var-tram, "\\\\", "\\\\10"); }
.fad.@{fa-css-prefix}-transgender:after { content: replace(@fa-var-transgender, "\\\\", "\\\\10"); }
.fad.@{fa-css-prefix}-transgender-alt:after { content: replace(@fa-var-transgender-alt, "\\\\", "\\\\10"); }
.fad.@{fa-css-prefix}-trash:after { content: replace(@fa-var-trash, "\\\\", "\\\\10"); }
.fad.@{fa-css-prefix}-trash-alt:after { content: replace(@fa-var-trash-alt, "\\\\", "\\\\10"); }
.fad.@{fa-css-prefix}-trash-restore:after { content: replace(@fa-var-trash-restore, "\\\\", "\\\\10"); }
.fad.@{fa-css-prefix}-trash-restore-alt:after { content: replace(@fa-var-trash-restore-alt, "\\\\", "\\\\10"); }
.fad.@{fa-css-prefix}-trash-undo:after { content: replace(@fa-var-trash-undo, "\\\\", "\\\\10"); }
.fad.@{fa-css-prefix}-trash-undo-alt:after { content: replace(@fa-var-trash-undo-alt, "\\\\", "\\\\10"); }
.fad.@{fa-css-prefix}-treasure-chest:after { content: replace(@fa-var-treasure-chest, "\\\\", "\\\\10"); }
.fad.@{fa-css-prefix}-tree:after { content: replace(@fa-var-tree, "\\\\", "\\\\10"); }
.fad.@{fa-css-prefix}-tree-alt:after { content: replace(@fa-var-tree-alt, "\\\\", "\\\\10"); }
.fad.@{fa-css-prefix}-tree-christmas:after { content: replace(@fa-var-tree-christmas, "\\\\", "\\\\10"); }
.fad.@{fa-css-prefix}-tree-decorated:after { content: replace(@fa-var-tree-decorated, "\\\\", "\\\\10"); }
.fad.@{fa-css-prefix}-tree-large:after { content: replace(@fa-var-tree-large, "\\\\", "\\\\10"); }
.fad.@{fa-css-prefix}-tree-palm:after { content: replace(@fa-var-tree-palm, "\\\\", "\\\\10"); }
.fad.@{fa-css-prefix}-trees:after { content: replace(@fa-var-trees, "\\\\", "\\\\10"); }
.fad.@{fa-css-prefix}-triangle:after { content: replace(@fa-var-triangle, "\\\\", "\\\\10"); }
.fad.@{fa-css-prefix}-trophy:after { content: replace(@fa-var-trophy, "\\\\", "\\\\10"); }
.fad.@{fa-css-prefix}-trophy-alt:after { content: replace(@fa-var-trophy-alt, "\\\\", "\\\\10"); }
.fad.@{fa-css-prefix}-truck:after { content: replace(@fa-var-truck, "\\\\", "\\\\10"); }
.fad.@{fa-css-prefix}-truck-container:after { content: replace(@fa-var-truck-container, "\\\\", "\\\\10"); }
.fad.@{fa-css-prefix}-truck-couch:after { content: replace(@fa-var-truck-couch, "\\\\", "\\\\10"); }
.fad.@{fa-css-prefix}-truck-loading:after { content: replace(@fa-var-truck-loading, "\\\\", "\\\\10"); }
.fad.@{fa-css-prefix}-truck-monster:after { content: replace(@fa-var-truck-monster, "\\\\", "\\\\10"); }
.fad.@{fa-css-prefix}-truck-moving:after { content: replace(@fa-var-truck-moving, "\\\\", "\\\\10"); }
.fad.@{fa-css-prefix}-truck-pickup:after { content: replace(@fa-var-truck-pickup, "\\\\", "\\\\10"); }
.fad.@{fa-css-prefix}-truck-plow:after { content: replace(@fa-var-truck-plow, "\\\\", "\\\\10"); }
.fad.@{fa-css-prefix}-truck-ramp:after { content: replace(@fa-var-truck-ramp, "\\\\", "\\\\10"); }
.fad.@{fa-css-prefix}-tshirt:after { content: replace(@fa-var-tshirt, "\\\\", "\\\\10"); }
.fad.@{fa-css-prefix}-tty:after { content: replace(@fa-var-tty, "\\\\", "\\\\10"); }
.fad.@{fa-css-prefix}-turkey:after { content: replace(@fa-var-turkey, "\\\\", "\\\\10"); }
.fad.@{fa-css-prefix}-turtle:after { content: replace(@fa-var-turtle, "\\\\", "\\\\10"); }
.fad.@{fa-css-prefix}-tv:after { content: replace(@fa-var-tv, "\\\\", "\\\\10"); }
.fad.@{fa-css-prefix}-tv-retro:after { content: replace(@fa-var-tv-retro, "\\\\", "\\\\10"); }
.fad.@{fa-css-prefix}-umbrella:after { content: replace(@fa-var-umbrella, "\\\\", "\\\\10"); }
.fad.@{fa-css-prefix}-umbrella-beach:after { content: replace(@fa-var-umbrella-beach, "\\\\", "\\\\10"); }
.fad.@{fa-css-prefix}-underline:after { content: replace(@fa-var-underline, "\\\\", "\\\\10"); }
.fad.@{fa-css-prefix}-undo:after { content: replace(@fa-var-undo, "\\\\", "\\\\10"); }
.fad.@{fa-css-prefix}-undo-alt:after { content: replace(@fa-var-undo-alt, "\\\\", "\\\\10"); }
.fad.@{fa-css-prefix}-unicorn:after { content: replace(@fa-var-unicorn, "\\\\", "\\\\10"); }
.fad.@{fa-css-prefix}-union:after { content: replace(@fa-var-union, "\\\\", "\\\\10"); }
.fad.@{fa-css-prefix}-universal-access:after { content: replace(@fa-var-universal-access, "\\\\", "\\\\10"); }
.fad.@{fa-css-prefix}-university:after { content: replace(@fa-var-university, "\\\\", "\\\\10"); }
.fad.@{fa-css-prefix}-unlink:after { content: replace(@fa-var-unlink, "\\\\", "\\\\10"); }
.fad.@{fa-css-prefix}-unlock:after { content: replace(@fa-var-unlock, "\\\\", "\\\\10"); }
.fad.@{fa-css-prefix}-unlock-alt:after { content: replace(@fa-var-unlock-alt, "\\\\", "\\\\10"); }
.fad.@{fa-css-prefix}-upload:after { content: replace(@fa-var-upload, "\\\\", "\\\\10"); }
.fad.@{fa-css-prefix}-usd-circle:after { content: replace(@fa-var-usd-circle, "\\\\", "\\\\10"); }
.fad.@{fa-css-prefix}-usd-square:after { content: replace(@fa-var-usd-square, "\\\\", "\\\\10"); }
.fad.@{fa-css-prefix}-user:after { content: replace(@fa-var-user, "\\\\", "\\\\10"); }
.fad.@{fa-css-prefix}-user-alt:after { content: replace(@fa-var-user-alt, "\\\\", "\\\\10"); }
.fad.@{fa-css-prefix}-user-alt-slash:after { content: replace(@fa-var-user-alt-slash, "\\\\", "\\\\10"); }
.fad.@{fa-css-prefix}-user-astronaut:after { content: replace(@fa-var-user-astronaut, "\\\\", "\\\\10"); }
.fad.@{fa-css-prefix}-user-chart:after { content: replace(@fa-var-user-chart, "\\\\", "\\\\10"); }
.fad.@{fa-css-prefix}-user-check:after { content: replace(@fa-var-user-check, "\\\\", "\\\\10"); }
.fad.@{fa-css-prefix}-user-circle:after { content: replace(@fa-var-user-circle, "\\\\", "\\\\10"); }
.fad.@{fa-css-prefix}-user-clock:after { content: replace(@fa-var-user-clock, "\\\\", "\\\\10"); }
.fad.@{fa-css-prefix}-user-cog:after { content: replace(@fa-var-user-cog, "\\\\", "\\\\10"); }
.fad.@{fa-css-prefix}-user-crown:after { content: replace(@fa-var-user-crown, "\\\\", "\\\\10"); }
.fad.@{fa-css-prefix}-user-edit:after { content: replace(@fa-var-user-edit, "\\\\", "\\\\10"); }
.fad.@{fa-css-prefix}-user-friends:after { content: replace(@fa-var-user-friends, "\\\\", "\\\\10"); }
.fad.@{fa-css-prefix}-user-graduate:after { content: replace(@fa-var-user-graduate, "\\\\", "\\\\10"); }
.fad.@{fa-css-prefix}-user-hard-hat:after { content: replace(@fa-var-user-hard-hat, "\\\\", "\\\\10"); }
.fad.@{fa-css-prefix}-user-headset:after { content: replace(@fa-var-user-headset, "\\\\", "\\\\10"); }
.fad.@{fa-css-prefix}-user-injured:after { content: replace(@fa-var-user-injured, "\\\\", "\\\\10"); }
.fad.@{fa-css-prefix}-user-lock:after { content: replace(@fa-var-user-lock, "\\\\", "\\\\10"); }
.fad.@{fa-css-prefix}-user-md:after { content: replace(@fa-var-user-md, "\\\\", "\\\\10"); }
.fad.@{fa-css-prefix}-user-md-chat:after { content: replace(@fa-var-user-md-chat, "\\\\", "\\\\10"); }
.fad.@{fa-css-prefix}-user-minus:after { content: replace(@fa-var-user-minus, "\\\\", "\\\\10"); }
.fad.@{fa-css-prefix}-user-ninja:after { content: replace(@fa-var-user-ninja, "\\\\", "\\\\10"); }
.fad.@{fa-css-prefix}-user-nurse:after { content: replace(@fa-var-user-nurse, "\\\\", "\\\\10"); }
.fad.@{fa-css-prefix}-user-plus:after { content: replace(@fa-var-user-plus, "\\\\", "\\\\10"); }
.fad.@{fa-css-prefix}-user-secret:after { content: replace(@fa-var-user-secret, "\\\\", "\\\\10"); }
.fad.@{fa-css-prefix}-user-shield:after { content: replace(@fa-var-user-shield, "\\\\", "\\\\10"); }
.fad.@{fa-css-prefix}-user-slash:after { content: replace(@fa-var-user-slash, "\\\\", "\\\\10"); }
.fad.@{fa-css-prefix}-user-tag:after { content: replace(@fa-var-user-tag, "\\\\", "\\\\10"); }
.fad.@{fa-css-prefix}-user-tie:after { content: replace(@fa-var-user-tie, "\\\\", "\\\\10"); }
.fad.@{fa-css-prefix}-user-times:after { content: replace(@fa-var-user-times, "\\\\", "\\\\10"); }
.fad.@{fa-css-prefix}-users:after { content: replace(@fa-var-users, "\\\\", "\\\\10"); }
.fad.@{fa-css-prefix}-users-class:after { content: replace(@fa-var-users-class, "\\\\", "\\\\10"); }
.fad.@{fa-css-prefix}-users-cog:after { content: replace(@fa-var-users-cog, "\\\\", "\\\\10"); }
.fad.@{fa-css-prefix}-users-crown:after { content: replace(@fa-var-users-crown, "\\\\", "\\\\10"); }
.fad.@{fa-css-prefix}-users-medical:after { content: replace(@fa-var-users-medical, "\\\\", "\\\\10"); }
.fad.@{fa-css-prefix}-utensil-fork:after { content: replace(@fa-var-utensil-fork, "\\\\", "\\\\10"); }
.fad.@{fa-css-prefix}-utensil-knife:after { content: replace(@fa-var-utensil-knife, "\\\\", "\\\\10"); }
.fad.@{fa-css-prefix}-utensil-spoon:after { content: replace(@fa-var-utensil-spoon, "\\\\", "\\\\10"); }
.fad.@{fa-css-prefix}-utensils:after { content: replace(@fa-var-utensils, "\\\\", "\\\\10"); }
.fad.@{fa-css-prefix}-utensils-alt:after { content: replace(@fa-var-utensils-alt, "\\\\", "\\\\10"); }
.fad.@{fa-css-prefix}-value-absolute:after { content: replace(@fa-var-value-absolute, "\\\\", "\\\\10"); }
.fad.@{fa-css-prefix}-vector-square:after { content: replace(@fa-var-vector-square, "\\\\", "\\\\10"); }
.fad.@{fa-css-prefix}-venus:after { content: replace(@fa-var-venus, "\\\\", "\\\\10"); }
.fad.@{fa-css-prefix}-venus-double:after { content: replace(@fa-var-venus-double, "\\\\", "\\\\10"); }
.fad.@{fa-css-prefix}-venus-mars:after { content: replace(@fa-var-venus-mars, "\\\\", "\\\\10"); }
.fad.@{fa-css-prefix}-vial:after { content: replace(@fa-var-vial, "\\\\", "\\\\10"); }
.fad.@{fa-css-prefix}-vials:after { content: replace(@fa-var-vials, "\\\\", "\\\\10"); }
.fad.@{fa-css-prefix}-video:after { content: replace(@fa-var-video, "\\\\", "\\\\10"); }
.fad.@{fa-css-prefix}-video-plus:after { content: replace(@fa-var-video-plus, "\\\\", "\\\\10"); }
.fad.@{fa-css-prefix}-video-slash:after { content: replace(@fa-var-video-slash, "\\\\", "\\\\10"); }
.fad.@{fa-css-prefix}-vihara:after { content: replace(@fa-var-vihara, "\\\\", "\\\\10"); }
.fad.@{fa-css-prefix}-voicemail:after { content: replace(@fa-var-voicemail, "\\\\", "\\\\10"); }
.fad.@{fa-css-prefix}-volcano:after { content: replace(@fa-var-volcano, "\\\\", "\\\\10"); }
.fad.@{fa-css-prefix}-volleyball-ball:after { content: replace(@fa-var-volleyball-ball, "\\\\", "\\\\10"); }
.fad.@{fa-css-prefix}-volume:after { content: replace(@fa-var-volume, "\\\\", "\\\\10"); }
.fad.@{fa-css-prefix}-volume-down:after { content: replace(@fa-var-volume-down, "\\\\", "\\\\10"); }
.fad.@{fa-css-prefix}-volume-mute:after { content: replace(@fa-var-volume-mute, "\\\\", "\\\\10"); }
.fad.@{fa-css-prefix}-volume-off:after { content: replace(@fa-var-volume-off, "\\\\", "\\\\10"); }
.fad.@{fa-css-prefix}-volume-slash:after { content: replace(@fa-var-volume-slash, "\\\\", "\\\\10"); }
.fad.@{fa-css-prefix}-volume-up:after { content: replace(@fa-var-volume-up, "\\\\", "\\\\10"); }
.fad.@{fa-css-prefix}-vote-nay:after { content: replace(@fa-var-vote-nay, "\\\\", "\\\\10"); }
.fad.@{fa-css-prefix}-vote-yea:after { content: replace(@fa-var-vote-yea, "\\\\", "\\\\10"); }
.fad.@{fa-css-prefix}-vr-cardboard:after { content: replace(@fa-var-vr-cardboard, "\\\\", "\\\\10"); }
.fad.@{fa-css-prefix}-walker:after { content: replace(@fa-var-walker, "\\\\", "\\\\10"); }
.fad.@{fa-css-prefix}-walking:after { content: replace(@fa-var-walking, "\\\\", "\\\\10"); }
.fad.@{fa-css-prefix}-wallet:after { content: replace(@fa-var-wallet, "\\\\", "\\\\10"); }
.fad.@{fa-css-prefix}-wand:after { content: replace(@fa-var-wand, "\\\\", "\\\\10"); }
.fad.@{fa-css-prefix}-wand-magic:after { content: replace(@fa-var-wand-magic, "\\\\", "\\\\10"); }
.fad.@{fa-css-prefix}-warehouse:after { content: replace(@fa-var-warehouse, "\\\\", "\\\\10"); }
.fad.@{fa-css-prefix}-warehouse-alt:after { content: replace(@fa-var-warehouse-alt, "\\\\", "\\\\10"); }
.fad.@{fa-css-prefix}-washer:after { content: replace(@fa-var-washer, "\\\\", "\\\\10"); }
.fad.@{fa-css-prefix}-watch:after { content: replace(@fa-var-watch, "\\\\", "\\\\10"); }
.fad.@{fa-css-prefix}-watch-fitness:after { content: replace(@fa-var-watch-fitness, "\\\\", "\\\\10"); }
.fad.@{fa-css-prefix}-water:after { content: replace(@fa-var-water, "\\\\", "\\\\10"); }
.fad.@{fa-css-prefix}-water-lower:after { content: replace(@fa-var-water-lower, "\\\\", "\\\\10"); }
.fad.@{fa-css-prefix}-water-rise:after { content: replace(@fa-var-water-rise, "\\\\", "\\\\10"); }
.fad.@{fa-css-prefix}-wave-sine:after { content: replace(@fa-var-wave-sine, "\\\\", "\\\\10"); }
.fad.@{fa-css-prefix}-wave-square:after { content: replace(@fa-var-wave-square, "\\\\", "\\\\10"); }
.fad.@{fa-css-prefix}-wave-triangle:after { content: replace(@fa-var-wave-triangle, "\\\\", "\\\\10"); }
.fad.@{fa-css-prefix}-webcam:after { content: replace(@fa-var-webcam, "\\\\", "\\\\10"); }
.fad.@{fa-css-prefix}-webcam-slash:after { content: replace(@fa-var-webcam-slash, "\\\\", "\\\\10"); }
.fad.@{fa-css-prefix}-weight:after { content: replace(@fa-var-weight, "\\\\", "\\\\10"); }
.fad.@{fa-css-prefix}-weight-hanging:after { content: replace(@fa-var-weight-hanging, "\\\\", "\\\\10"); }
.fad.@{fa-css-prefix}-whale:after { content: replace(@fa-var-whale, "\\\\", "\\\\10"); }
.fad.@{fa-css-prefix}-wheat:after { content: replace(@fa-var-wheat, "\\\\", "\\\\10"); }
.fad.@{fa-css-prefix}-wheelchair:after { content: replace(@fa-var-wheelchair, "\\\\", "\\\\10"); }
.fad.@{fa-css-prefix}-whistle:after { content: replace(@fa-var-whistle, "\\\\", "\\\\10"); }
.fad.@{fa-css-prefix}-wifi:after { content: replace(@fa-var-wifi, "\\\\", "\\\\10"); }
.fad.@{fa-css-prefix}-wifi-1:after { content: replace(@fa-var-wifi-1, "\\\\", "\\\\10"); }
.fad.@{fa-css-prefix}-wifi-2:after { content: replace(@fa-var-wifi-2, "\\\\", "\\\\10"); }
.fad.@{fa-css-prefix}-wifi-slash:after { content: replace(@fa-var-wifi-slash, "\\\\", "\\\\10"); }
.fad.@{fa-css-prefix}-wind:after { content: replace(@fa-var-wind, "\\\\", "\\\\10"); }
.fad.@{fa-css-prefix}-wind-turbine:after { content: replace(@fa-var-wind-turbine, "\\\\", "\\\\10"); }
.fad.@{fa-css-prefix}-wind-warning:after { content: replace(@fa-var-wind-warning, "\\\\", "\\\\10"); }
.fad.@{fa-css-prefix}-window:after { content: replace(@fa-var-window, "\\\\", "\\\\10"); }
.fad.@{fa-css-prefix}-window-alt:after { content: replace(@fa-var-window-alt, "\\\\", "\\\\10"); }
.fad.@{fa-css-prefix}-window-close:after { content: replace(@fa-var-window-close, "\\\\", "\\\\10"); }
.fad.@{fa-css-prefix}-window-maximize:after { content: replace(@fa-var-window-maximize, "\\\\", "\\\\10"); }
.fad.@{fa-css-prefix}-window-minimize:after { content: replace(@fa-var-window-minimize, "\\\\", "\\\\10"); }
.fad.@{fa-css-prefix}-window-restore:after { content: replace(@fa-var-window-restore, "\\\\", "\\\\10"); }
.fad.@{fa-css-prefix}-windsock:after { content: replace(@fa-var-windsock, "\\\\", "\\\\10"); }
.fad.@{fa-css-prefix}-wine-bottle:after { content: replace(@fa-var-wine-bottle, "\\\\", "\\\\10"); }
.fad.@{fa-css-prefix}-wine-glass:after { content: replace(@fa-var-wine-glass, "\\\\", "\\\\10"); }
.fad.@{fa-css-prefix}-wine-glass-alt:after { content: replace(@fa-var-wine-glass-alt, "\\\\", "\\\\10"); }
.fad.@{fa-css-prefix}-won-sign:after { content: replace(@fa-var-won-sign, "\\\\", "\\\\10"); }
.fad.@{fa-css-prefix}-wreath:after { content: replace(@fa-var-wreath, "\\\\", "\\\\10"); }
.fad.@{fa-css-prefix}-wrench:after { content: replace(@fa-var-wrench, "\\\\", "\\\\10"); }
.fad.@{fa-css-prefix}-x-ray:after { content: replace(@fa-var-x-ray, "\\\\", "\\\\10"); }
.fad.@{fa-css-prefix}-yen-sign:after { content: replace(@fa-var-yen-sign, "\\\\", "\\\\10"); }
.fad.@{fa-css-prefix}-yin-yang:after { content: replace(@fa-var-yin-yang, "\\\\", "\\\\10"); }


/*!
 * Font Awesome Pro by @fontawesome - https://fontawesome.com
 * License - https://fontawesome.com/license (Commercial License)
 */
@font-face {
  font-family: \'Font Awesome 5 Pro\';
  font-style: normal;
  font-weight: 300;
  src: url(\'@{fa-font-path}/fa-light-300.woff2\') format(\'woff2\'),
    url(\'@{fa-font-path}/fa-light-300.woff\') format(\'woff\');
}

.fal {
  font-family: \'Font Awesome 5 Pro\';
  position: relative;
  font-weight: 300;
}

/*!
 * Font Awesome Pro by @fontawesome - https://fontawesome.com
 * License - https://fontawesome.com/license (Commercial License)
 */
@font-face {
  font-family: \'Font Awesome 5 Pro\';
  font-style: normal;
  font-weight: 400;
  src: url(\'@{fa-font-path}/fa-regular-400.woff2\') format(\'woff2\'),
    url(\'@{fa-font-path}/fa-regular-400.woff\') format(\'woff\');
}

.far {
  font-family: \'Font Awesome 5 Pro\';
  position: relative;
  font-weight: 400;
}

/*!
 * Font Awesome Pro by @fontawesome - https://fontawesome.com
 * License - https://fontawesome.com/license (Commercial License)
 */
@font-face {
  font-family: \'Font Awesome 5 Pro\';
  font-style: normal;
  font-weight: 900;
  src: url(\'@{fa-font-path}/fa-solid-900.woff2\') format(\'woff2\'),
    url(\'@{fa-font-path}/fa-solid-900.woff\') format(\'woff\');
}

.fa,
.fas {
  font-family: \'Font Awesome 5 Pro\';
  position: relative;
  font-weight: 900;
}

/*!
 * Font Awesome Pro by @fontawesome - https://fontawesome.com
 * License - https://fontawesome.com/license (Commercial License)
 */
@font-face {
  font-family: \'Font Awesome 5 Brands\';
  font-style: normal;
  font-weight: 400;
  src: url(\'@{fa-font-path}/fa-brands-400.woff2\') format(\'woff2\'),
    url(\'@{fa-font-path}/fa-brands-400.woff\') format(\'woff\');
}

.fab {
  font-family: \'Font Awesome 5 Brands\';
  position: relative;
  font-weight: 400;
}

// Base Class Definition
// -------------------------

.@{fa-css-prefix}, .fas, .far, .fal, .fad, .fab {
  -moz-osx-font-smoothing: grayscale;
  -webkit-font-smoothing: antialiased;
  display: inline-block;
  font-style: normal;
  font-variant: normal;
  text-rendering: auto;
  line-height: 1;
}


// Icon Sizes
// -------------------------

.larger(@factor) when (@factor > 0) {
  .larger((@factor - 1));

  .@{fa-css-prefix}-@{factor}x {
    font-size: (@factor * 1em);
  }
}

/* makes the font 33% larger relative to the icon container */
.@{fa-css-prefix}-lg {
  font-size: (4em / 3);
  line-height: (3em / 4);
  vertical-align: -.0667em;
}

.@{fa-css-prefix}-xs {
  font-size: .75em;
}

.@{fa-css-prefix}-sm {
  font-size: .875em;
}

.larger(10);


// Fixed Width Icons
// -------------------------
.@{fa-css-prefix}-fw {
  text-align: center;
  width: (20em / 16);
}


// List Icons
// -------------------------

.@{fa-css-prefix}-ul {
  list-style-type: none;
  margin-left: (@fa-li-width * 5/4);
  padding-left: 0;

  > li { position: relative; }
}

.@{fa-css-prefix}-li {
  left: -@fa-li-width;
  position: absolute;
  text-align: center;
  width: @fa-li-width;
  line-height: inherit;
}


// Bordered & Pulled
// -------------------------

.@{fa-css-prefix}-border {
  border-radius: .1em;
  border: solid .08em @fa-border-color;
  padding: .2em .25em .15em;
}

.@{fa-css-prefix}-pull-left { float: left; }
.@{fa-css-prefix}-pull-right { float: right; }

.@{fa-css-prefix}, .fas, .far, .fal, .fab {
  &.@{fa-css-prefix}-pull-left { margin-right: .3em; }
  &.@{fa-css-prefix}-pull-right { margin-left: .3em; }
}


// Animated Icons
// --------------------------

.@{fa-css-prefix}-spin {
  animation: fa-spin 2s infinite linear;
}

.@{fa-css-prefix}-pulse {
  animation: fa-spin 1s infinite steps(8);
}

@keyframes fa-spin {
  0% {
    transform: rotate(0deg);
  }
  100% {
    transform: rotate(360deg);
  }
}


// Rotated & Flipped Icons
// -------------------------

.@{fa-css-prefix}-rotate-90  { .fa-icon-rotate(90deg, 1);  }
.@{fa-css-prefix}-rotate-180 { .fa-icon-rotate(180deg, 2); }
.@{fa-css-prefix}-rotate-270 { .fa-icon-rotate(270deg, 3); }

.@{fa-css-prefix}-flip-horizontal { .fa-icon-flip(-1, 1, 0); }
.@{fa-css-prefix}-flip-vertical   { .fa-icon-flip(1, -1, 2); }
.@{fa-css-prefix}-flip-both, .@{fa-css-prefix}-flip-horizontal.@{fa-css-prefix}-flip-vertical { .fa-icon-flip(-1, -1, 2); }

// Hook for IE8-9
// -------------------------

:root {
  .@{fa-css-prefix}-rotate-90,
  .@{fa-css-prefix}-rotate-180,
  .@{fa-css-prefix}-rotate-270,
  .@{fa-css-prefix}-flip-horizontal,
  .@{fa-css-prefix}-flip-vertical,
  .@{fa-css-prefix}-flip-both {
    filter: none;
  }
}


// Stacked Icons
// -------------------------

.@{fa-css-prefix}-stack {
  display: inline-block;
  height: 2em;
  line-height: 2em;
  position: relative;
  vertical-align: middle;
  width: 2em;
}

.@{fa-css-prefix}-stack-1x, .@{fa-css-prefix}-stack-2x {
  left: 0;
  position: absolute;
  text-align: center;
  width: 100%;
}

.@{fa-css-prefix}-stack-1x { line-height: inherit; }
.@{fa-css-prefix}-stack-2x { font-size: 2em; }
.@{fa-css-prefix}-inverse { color: @fa-inverse; }


/* Font Awesome uses the Unicode Private Use Area (PUA) to ensure screen
   readers do not read off random characters that represent icons */

.@{fa-css-prefix}-500px:before { content: @fa-var-500px; }
.@{fa-css-prefix}-abacus:before { content: @fa-var-abacus; }
.@{fa-css-prefix}-accessible-icon:before { content: @fa-var-accessible-icon; }
.@{fa-css-prefix}-accusoft:before { content: @fa-var-accusoft; }
.@{fa-css-prefix}-acorn:before { content: @fa-var-acorn; }
.@{fa-css-prefix}-acquisitions-incorporated:before { content: @fa-var-acquisitions-incorporated; }
.@{fa-css-prefix}-ad:before { content: @fa-var-ad; }
.@{fa-css-prefix}-address-book:before { content: @fa-var-address-book; }
.@{fa-css-prefix}-address-card:before { content: @fa-var-address-card; }
.@{fa-css-prefix}-adjust:before { content: @fa-var-adjust; }
.@{fa-css-prefix}-adn:before { content: @fa-var-adn; }
.@{fa-css-prefix}-adobe:before { content: @fa-var-adobe; }
.@{fa-css-prefix}-adversal:before { content: @fa-var-adversal; }
.@{fa-css-prefix}-affiliatetheme:before { content: @fa-var-affiliatetheme; }
.@{fa-css-prefix}-air-freshener:before { content: @fa-var-air-freshener; }
.@{fa-css-prefix}-airbnb:before { content: @fa-var-airbnb; }
.@{fa-css-prefix}-alarm-clock:before { content: @fa-var-alarm-clock; }
.@{fa-css-prefix}-alarm-exclamation:before { content: @fa-var-alarm-exclamation; }
.@{fa-css-prefix}-alarm-plus:before { content: @fa-var-alarm-plus; }
.@{fa-css-prefix}-alarm-snooze:before { content: @fa-var-alarm-snooze; }
.@{fa-css-prefix}-algolia:before { content: @fa-var-algolia; }
.@{fa-css-prefix}-alicorn:before { content: @fa-var-alicorn; }
.@{fa-css-prefix}-align-center:before { content: @fa-var-align-center; }
.@{fa-css-prefix}-align-justify:before { content: @fa-var-align-justify; }
.@{fa-css-prefix}-align-left:before { content: @fa-var-align-left; }
.@{fa-css-prefix}-align-right:before { content: @fa-var-align-right; }
.@{fa-css-prefix}-align-slash:before { content: @fa-var-align-slash; }
.@{fa-css-prefix}-alipay:before { content: @fa-var-alipay; }
.@{fa-css-prefix}-allergies:before { content: @fa-var-allergies; }
.@{fa-css-prefix}-amazon:before { content: @fa-var-amazon; }
.@{fa-css-prefix}-amazon-pay:before { content: @fa-var-amazon-pay; }
.@{fa-css-prefix}-ambulance:before { content: @fa-var-ambulance; }
.@{fa-css-prefix}-american-sign-language-interpreting:before { content: @fa-var-american-sign-language-interpreting; }
.@{fa-css-prefix}-amilia:before { content: @fa-var-amilia; }
.@{fa-css-prefix}-analytics:before { content: @fa-var-analytics; }
.@{fa-css-prefix}-anchor:before { content: @fa-var-anchor; }
.@{fa-css-prefix}-android:before { content: @fa-var-android; }
.@{fa-css-prefix}-angel:before { content: @fa-var-angel; }
.@{fa-css-prefix}-angellist:before { content: @fa-var-angellist; }
.@{fa-css-prefix}-angle-double-down:before { content: @fa-var-angle-double-down; }
.@{fa-css-prefix}-angle-double-left:before { content: @fa-var-angle-double-left; }
.@{fa-css-prefix}-angle-double-right:before { content: @fa-var-angle-double-right; }
.@{fa-css-prefix}-angle-double-up:before { content: @fa-var-angle-double-up; }
.@{fa-css-prefix}-angle-down:before { content: @fa-var-angle-down; }
.@{fa-css-prefix}-angle-left:before { content: @fa-var-angle-left; }
.@{fa-css-prefix}-angle-right:before { content: @fa-var-angle-right; }
.@{fa-css-prefix}-angle-up:before { content: @fa-var-angle-up; }
.@{fa-css-prefix}-angry:before { content: @fa-var-angry; }
.@{fa-css-prefix}-angrycreative:before { content: @fa-var-angrycreative; }
.@{fa-css-prefix}-angular:before { content: @fa-var-angular; }
.@{fa-css-prefix}-ankh:before { content: @fa-var-ankh; }
.@{fa-css-prefix}-app-store:before { content: @fa-var-app-store; }
.@{fa-css-prefix}-app-store-ios:before { content: @fa-var-app-store-ios; }
.@{fa-css-prefix}-apper:before { content: @fa-var-apper; }
.@{fa-css-prefix}-apple:before { content: @fa-var-apple; }
.@{fa-css-prefix}-apple-alt:before { content: @fa-var-apple-alt; }
.@{fa-css-prefix}-apple-crate:before { content: @fa-var-apple-crate; }
.@{fa-css-prefix}-apple-pay:before { content: @fa-var-apple-pay; }
.@{fa-css-prefix}-archive:before { content: @fa-var-archive; }
.@{fa-css-prefix}-archway:before { content: @fa-var-archway; }
.@{fa-css-prefix}-arrow-alt-circle-down:before { content: @fa-var-arrow-alt-circle-down; }
.@{fa-css-prefix}-arrow-alt-circle-left:before { content: @fa-var-arrow-alt-circle-left; }
.@{fa-css-prefix}-arrow-alt-circle-right:before { content: @fa-var-arrow-alt-circle-right; }
.@{fa-css-prefix}-arrow-alt-circle-up:before { content: @fa-var-arrow-alt-circle-up; }
.@{fa-css-prefix}-arrow-alt-down:before { content: @fa-var-arrow-alt-down; }
.@{fa-css-prefix}-arrow-alt-from-bottom:before { content: @fa-var-arrow-alt-from-bottom; }
.@{fa-css-prefix}-arrow-alt-from-left:before { content: @fa-var-arrow-alt-from-left; }
.@{fa-css-prefix}-arrow-alt-from-right:before { content: @fa-var-arrow-alt-from-right; }
.@{fa-css-prefix}-arrow-alt-from-top:before { content: @fa-var-arrow-alt-from-top; }
.@{fa-css-prefix}-arrow-alt-left:before { content: @fa-var-arrow-alt-left; }
.@{fa-css-prefix}-arrow-alt-right:before { content: @fa-var-arrow-alt-right; }
.@{fa-css-prefix}-arrow-alt-square-down:before { content: @fa-var-arrow-alt-square-down; }
.@{fa-css-prefix}-arrow-alt-square-left:before { content: @fa-var-arrow-alt-square-left; }
.@{fa-css-prefix}-arrow-alt-square-right:before { content: @fa-var-arrow-alt-square-right; }
.@{fa-css-prefix}-arrow-alt-square-up:before { content: @fa-var-arrow-alt-square-up; }
.@{fa-css-prefix}-arrow-alt-to-bottom:before { content: @fa-var-arrow-alt-to-bottom; }
.@{fa-css-prefix}-arrow-alt-to-left:before { content: @fa-var-arrow-alt-to-left; }
.@{fa-css-prefix}-arrow-alt-to-right:before { content: @fa-var-arrow-alt-to-right; }
.@{fa-css-prefix}-arrow-alt-to-top:before { content: @fa-var-arrow-alt-to-top; }
.@{fa-css-prefix}-arrow-alt-up:before { content: @fa-var-arrow-alt-up; }
.@{fa-css-prefix}-arrow-circle-down:before { content: @fa-var-arrow-circle-down; }
.@{fa-css-prefix}-arrow-circle-left:before { content: @fa-var-arrow-circle-left; }
.@{fa-css-prefix}-arrow-circle-right:before { content: @fa-var-arrow-circle-right; }
.@{fa-css-prefix}-arrow-circle-up:before { content: @fa-var-arrow-circle-up; }
.@{fa-css-prefix}-arrow-down:before { content: @fa-var-arrow-down; }
.@{fa-css-prefix}-arrow-from-bottom:before { content: @fa-var-arrow-from-bottom; }
.@{fa-css-prefix}-arrow-from-left:before { content: @fa-var-arrow-from-left; }
.@{fa-css-prefix}-arrow-from-right:before { content: @fa-var-arrow-from-right; }
.@{fa-css-prefix}-arrow-from-top:before { content: @fa-var-arrow-from-top; }
.@{fa-css-prefix}-arrow-left:before { content: @fa-var-arrow-left; }
.@{fa-css-prefix}-arrow-right:before { content: @fa-var-arrow-right; }
.@{fa-css-prefix}-arrow-square-down:before { content: @fa-var-arrow-square-down; }
.@{fa-css-prefix}-arrow-square-left:before { content: @fa-var-arrow-square-left; }
.@{fa-css-prefix}-arrow-square-right:before { content: @fa-var-arrow-square-right; }
.@{fa-css-prefix}-arrow-square-up:before { content: @fa-var-arrow-square-up; }
.@{fa-css-prefix}-arrow-to-bottom:before { content: @fa-var-arrow-to-bottom; }
.@{fa-css-prefix}-arrow-to-left:before { content: @fa-var-arrow-to-left; }
.@{fa-css-prefix}-arrow-to-right:before { content: @fa-var-arrow-to-right; }
.@{fa-css-prefix}-arrow-to-top:before { content: @fa-var-arrow-to-top; }
.@{fa-css-prefix}-arrow-up:before { content: @fa-var-arrow-up; }
.@{fa-css-prefix}-arrows:before { content: @fa-var-arrows; }
.@{fa-css-prefix}-arrows-alt:before { content: @fa-var-arrows-alt; }
.@{fa-css-prefix}-arrows-alt-h:before { content: @fa-var-arrows-alt-h; }
.@{fa-css-prefix}-arrows-alt-v:before { content: @fa-var-arrows-alt-v; }
.@{fa-css-prefix}-arrows-h:before { content: @fa-var-arrows-h; }
.@{fa-css-prefix}-arrows-v:before { content: @fa-var-arrows-v; }
.@{fa-css-prefix}-artstation:before { content: @fa-var-artstation; }
.@{fa-css-prefix}-assistive-listening-systems:before { content: @fa-var-assistive-listening-systems; }
.@{fa-css-prefix}-asterisk:before { content: @fa-var-asterisk; }
.@{fa-css-prefix}-asymmetrik:before { content: @fa-var-asymmetrik; }
.@{fa-css-prefix}-at:before { content: @fa-var-at; }
.@{fa-css-prefix}-atlas:before { content: @fa-var-atlas; }
.@{fa-css-prefix}-atlassian:before { content: @fa-var-atlassian; }
.@{fa-css-prefix}-atom:before { content: @fa-var-atom; }
.@{fa-css-prefix}-atom-alt:before { content: @fa-var-atom-alt; }
.@{fa-css-prefix}-audible:before { content: @fa-var-audible; }
.@{fa-css-prefix}-audio-description:before { content: @fa-var-audio-description; }
.@{fa-css-prefix}-autoprefixer:before { content: @fa-var-autoprefixer; }
.@{fa-css-prefix}-avianex:before { content: @fa-var-avianex; }
.@{fa-css-prefix}-aviato:before { content: @fa-var-aviato; }
.@{fa-css-prefix}-award:before { content: @fa-var-award; }
.@{fa-css-prefix}-aws:before { content: @fa-var-aws; }
.@{fa-css-prefix}-axe:before { content: @fa-var-axe; }
.@{fa-css-prefix}-axe-battle:before { content: @fa-var-axe-battle; }
.@{fa-css-prefix}-baby:before { content: @fa-var-baby; }
.@{fa-css-prefix}-baby-carriage:before { content: @fa-var-baby-carriage; }
.@{fa-css-prefix}-backpack:before { content: @fa-var-backpack; }
.@{fa-css-prefix}-backspace:before { content: @fa-var-backspace; }
.@{fa-css-prefix}-backward:before { content: @fa-var-backward; }
.@{fa-css-prefix}-bacon:before { content: @fa-var-bacon; }
.@{fa-css-prefix}-badge:before { content: @fa-var-badge; }
.@{fa-css-prefix}-badge-check:before { content: @fa-var-badge-check; }
.@{fa-css-prefix}-badge-dollar:before { content: @fa-var-badge-dollar; }
.@{fa-css-prefix}-badge-percent:before { content: @fa-var-badge-percent; }
.@{fa-css-prefix}-badger-honey:before { content: @fa-var-badger-honey; }
.@{fa-css-prefix}-bags-shopping:before { content: @fa-var-bags-shopping; }
.@{fa-css-prefix}-balance-scale:before { content: @fa-var-balance-scale; }
.@{fa-css-prefix}-balance-scale-left:before { content: @fa-var-balance-scale-left; }
.@{fa-css-prefix}-balance-scale-right:before { content: @fa-var-balance-scale-right; }
.@{fa-css-prefix}-ball-pile:before { content: @fa-var-ball-pile; }
.@{fa-css-prefix}-ballot:before { content: @fa-var-ballot; }
.@{fa-css-prefix}-ballot-check:before { content: @fa-var-ballot-check; }
.@{fa-css-prefix}-ban:before { content: @fa-var-ban; }
.@{fa-css-prefix}-band-aid:before { content: @fa-var-band-aid; }
.@{fa-css-prefix}-bandcamp:before { content: @fa-var-bandcamp; }
.@{fa-css-prefix}-barcode:before { content: @fa-var-barcode; }
.@{fa-css-prefix}-barcode-alt:before { content: @fa-var-barcode-alt; }
.@{fa-css-prefix}-barcode-read:before { content: @fa-var-barcode-read; }
.@{fa-css-prefix}-barcode-scan:before { content: @fa-var-barcode-scan; }
.@{fa-css-prefix}-bars:before { content: @fa-var-bars; }
.@{fa-css-prefix}-baseball:before { content: @fa-var-baseball; }
.@{fa-css-prefix}-baseball-ball:before { content: @fa-var-baseball-ball; }
.@{fa-css-prefix}-basketball-ball:before { content: @fa-var-basketball-ball; }
.@{fa-css-prefix}-basketball-hoop:before { content: @fa-var-basketball-hoop; }
.@{fa-css-prefix}-bat:before { content: @fa-var-bat; }
.@{fa-css-prefix}-bath:before { content: @fa-var-bath; }
.@{fa-css-prefix}-battery-bolt:before { content: @fa-var-battery-bolt; }
.@{fa-css-prefix}-battery-empty:before { content: @fa-var-battery-empty; }
.@{fa-css-prefix}-battery-full:before { content: @fa-var-battery-full; }
.@{fa-css-prefix}-battery-half:before { content: @fa-var-battery-half; }
.@{fa-css-prefix}-battery-quarter:before { content: @fa-var-battery-quarter; }
.@{fa-css-prefix}-battery-slash:before { content: @fa-var-battery-slash; }
.@{fa-css-prefix}-battery-three-quarters:before { content: @fa-var-battery-three-quarters; }
.@{fa-css-prefix}-battle-net:before { content: @fa-var-battle-net; }
.@{fa-css-prefix}-bed:before { content: @fa-var-bed; }
.@{fa-css-prefix}-beer:before { content: @fa-var-beer; }
.@{fa-css-prefix}-behance:before { content: @fa-var-behance; }
.@{fa-css-prefix}-behance-square:before { content: @fa-var-behance-square; }
.@{fa-css-prefix}-bell:before { content: @fa-var-bell; }
.@{fa-css-prefix}-bell-exclamation:before { content: @fa-var-bell-exclamation; }
.@{fa-css-prefix}-bell-plus:before { content: @fa-var-bell-plus; }
.@{fa-css-prefix}-bell-school:before { content: @fa-var-bell-school; }
.@{fa-css-prefix}-bell-school-slash:before { content: @fa-var-bell-school-slash; }
.@{fa-css-prefix}-bell-slash:before { content: @fa-var-bell-slash; }
.@{fa-css-prefix}-bells:before { content: @fa-var-bells; }
.@{fa-css-prefix}-bezier-curve:before { content: @fa-var-bezier-curve; }
.@{fa-css-prefix}-bible:before { content: @fa-var-bible; }
.@{fa-css-prefix}-bicycle:before { content: @fa-var-bicycle; }
.@{fa-css-prefix}-biking:before { content: @fa-var-biking; }
.@{fa-css-prefix}-biking-mountain:before { content: @fa-var-biking-mountain; }
.@{fa-css-prefix}-bimobject:before { content: @fa-var-bimobject; }
.@{fa-css-prefix}-binoculars:before { content: @fa-var-binoculars; }
.@{fa-css-prefix}-biohazard:before { content: @fa-var-biohazard; }
.@{fa-css-prefix}-birthday-cake:before { content: @fa-var-birthday-cake; }
.@{fa-css-prefix}-bitbucket:before { content: @fa-var-bitbucket; }
.@{fa-css-prefix}-bitcoin:before { content: @fa-var-bitcoin; }
.@{fa-css-prefix}-bity:before { content: @fa-var-bity; }
.@{fa-css-prefix}-black-tie:before { content: @fa-var-black-tie; }
.@{fa-css-prefix}-blackberry:before { content: @fa-var-blackberry; }
.@{fa-css-prefix}-blanket:before { content: @fa-var-blanket; }
.@{fa-css-prefix}-blender:before { content: @fa-var-blender; }
.@{fa-css-prefix}-blender-phone:before { content: @fa-var-blender-phone; }
.@{fa-css-prefix}-blind:before { content: @fa-var-blind; }
.@{fa-css-prefix}-blog:before { content: @fa-var-blog; }
.@{fa-css-prefix}-blogger:before { content: @fa-var-blogger; }
.@{fa-css-prefix}-blogger-b:before { content: @fa-var-blogger-b; }
.@{fa-css-prefix}-bluetooth:before { content: @fa-var-bluetooth; }
.@{fa-css-prefix}-bluetooth-b:before { content: @fa-var-bluetooth-b; }
.@{fa-css-prefix}-bold:before { content: @fa-var-bold; }
.@{fa-css-prefix}-bolt:before { content: @fa-var-bolt; }
.@{fa-css-prefix}-bomb:before { content: @fa-var-bomb; }
.@{fa-css-prefix}-bone:before { content: @fa-var-bone; }
.@{fa-css-prefix}-bone-break:before { content: @fa-var-bone-break; }
.@{fa-css-prefix}-bong:before { content: @fa-var-bong; }
.@{fa-css-prefix}-book:before { content: @fa-var-book; }
.@{fa-css-prefix}-book-alt:before { content: @fa-var-book-alt; }
.@{fa-css-prefix}-book-dead:before { content: @fa-var-book-dead; }
.@{fa-css-prefix}-book-heart:before { content: @fa-var-book-heart; }
.@{fa-css-prefix}-book-medical:before { content: @fa-var-book-medical; }
.@{fa-css-prefix}-book-open:before { content: @fa-var-book-open; }
.@{fa-css-prefix}-book-reader:before { content: @fa-var-book-reader; }
.@{fa-css-prefix}-book-spells:before { content: @fa-var-book-spells; }
.@{fa-css-prefix}-book-user:before { content: @fa-var-book-user; }
.@{fa-css-prefix}-bookmark:before { content: @fa-var-bookmark; }
.@{fa-css-prefix}-books:before { content: @fa-var-books; }
.@{fa-css-prefix}-books-medical:before { content: @fa-var-books-medical; }
.@{fa-css-prefix}-boot:before { content: @fa-var-boot; }
.@{fa-css-prefix}-booth-curtain:before { content: @fa-var-booth-curtain; }
.@{fa-css-prefix}-bootstrap:before { content: @fa-var-bootstrap; }
.@{fa-css-prefix}-border-all:before { content: @fa-var-border-all; }
.@{fa-css-prefix}-border-bottom:before { content: @fa-var-border-bottom; }
.@{fa-css-prefix}-border-center-h:before { content: @fa-var-border-center-h; }
.@{fa-css-prefix}-border-center-v:before { content: @fa-var-border-center-v; }
.@{fa-css-prefix}-border-inner:before { content: @fa-var-border-inner; }
.@{fa-css-prefix}-border-left:before { content: @fa-var-border-left; }
.@{fa-css-prefix}-border-none:before { content: @fa-var-border-none; }
.@{fa-css-prefix}-border-outer:before { content: @fa-var-border-outer; }
.@{fa-css-prefix}-border-right:before { content: @fa-var-border-right; }
.@{fa-css-prefix}-border-style:before { content: @fa-var-border-style; }
.@{fa-css-prefix}-border-style-alt:before { content: @fa-var-border-style-alt; }
.@{fa-css-prefix}-border-top:before { content: @fa-var-border-top; }
.@{fa-css-prefix}-bow-arrow:before { content: @fa-var-bow-arrow; }
.@{fa-css-prefix}-bowling-ball:before { content: @fa-var-bowling-ball; }
.@{fa-css-prefix}-bowling-pins:before { content: @fa-var-bowling-pins; }
.@{fa-css-prefix}-box:before { content: @fa-var-box; }
.@{fa-css-prefix}-box-alt:before { content: @fa-var-box-alt; }
.@{fa-css-prefix}-box-ballot:before { content: @fa-var-box-ballot; }
.@{fa-css-prefix}-box-check:before { content: @fa-var-box-check; }
.@{fa-css-prefix}-box-fragile:before { content: @fa-var-box-fragile; }
.@{fa-css-prefix}-box-full:before { content: @fa-var-box-full; }
.@{fa-css-prefix}-box-heart:before { content: @fa-var-box-heart; }
.@{fa-css-prefix}-box-open:before { content: @fa-var-box-open; }
.@{fa-css-prefix}-box-up:before { content: @fa-var-box-up; }
.@{fa-css-prefix}-box-usd:before { content: @fa-var-box-usd; }
.@{fa-css-prefix}-boxes:before { content: @fa-var-boxes; }
.@{fa-css-prefix}-boxes-alt:before { content: @fa-var-boxes-alt; }
.@{fa-css-prefix}-boxing-glove:before { content: @fa-var-boxing-glove; }
.@{fa-css-prefix}-brackets:before { content: @fa-var-brackets; }
.@{fa-css-prefix}-brackets-curly:before { content: @fa-var-brackets-curly; }
.@{fa-css-prefix}-braille:before { content: @fa-var-braille; }
.@{fa-css-prefix}-brain:before { content: @fa-var-brain; }
.@{fa-css-prefix}-bread-loaf:before { content: @fa-var-bread-loaf; }
.@{fa-css-prefix}-bread-slice:before { content: @fa-var-bread-slice; }
.@{fa-css-prefix}-briefcase:before { content: @fa-var-briefcase; }
.@{fa-css-prefix}-briefcase-medical:before { content: @fa-var-briefcase-medical; }
.@{fa-css-prefix}-bring-forward:before { content: @fa-var-bring-forward; }
.@{fa-css-prefix}-bring-front:before { content: @fa-var-bring-front; }
.@{fa-css-prefix}-broadcast-tower:before { content: @fa-var-broadcast-tower; }
.@{fa-css-prefix}-broom:before { content: @fa-var-broom; }
.@{fa-css-prefix}-browser:before { content: @fa-var-browser; }
.@{fa-css-prefix}-brush:before { content: @fa-var-brush; }
.@{fa-css-prefix}-btc:before { content: @fa-var-btc; }
.@{fa-css-prefix}-buffer:before { content: @fa-var-buffer; }
.@{fa-css-prefix}-bug:before { content: @fa-var-bug; }
.@{fa-css-prefix}-building:before { content: @fa-var-building; }
.@{fa-css-prefix}-bullhorn:before { content: @fa-var-bullhorn; }
.@{fa-css-prefix}-bullseye:before { content: @fa-var-bullseye; }
.@{fa-css-prefix}-bullseye-arrow:before { content: @fa-var-bullseye-arrow; }
.@{fa-css-prefix}-bullseye-pointer:before { content: @fa-var-bullseye-pointer; }
.@{fa-css-prefix}-burger-soda:before { content: @fa-var-burger-soda; }
.@{fa-css-prefix}-burn:before { content: @fa-var-burn; }
.@{fa-css-prefix}-buromobelexperte:before { content: @fa-var-buromobelexperte; }
.@{fa-css-prefix}-burrito:before { content: @fa-var-burrito; }
.@{fa-css-prefix}-bus:before { content: @fa-var-bus; }
.@{fa-css-prefix}-bus-alt:before { content: @fa-var-bus-alt; }
.@{fa-css-prefix}-bus-school:before { content: @fa-var-bus-school; }
.@{fa-css-prefix}-business-time:before { content: @fa-var-business-time; }
.@{fa-css-prefix}-buysellads:before { content: @fa-var-buysellads; }
.@{fa-css-prefix}-cabinet-filing:before { content: @fa-var-cabinet-filing; }
.@{fa-css-prefix}-calculator:before { content: @fa-var-calculator; }
.@{fa-css-prefix}-calculator-alt:before { content: @fa-var-calculator-alt; }
.@{fa-css-prefix}-calendar:before { content: @fa-var-calendar; }
.@{fa-css-prefix}-calendar-alt:before { content: @fa-var-calendar-alt; }
.@{fa-css-prefix}-calendar-check:before { content: @fa-var-calendar-check; }
.@{fa-css-prefix}-calendar-day:before { content: @fa-var-calendar-day; }
.@{fa-css-prefix}-calendar-edit:before { content: @fa-var-calendar-edit; }
.@{fa-css-prefix}-calendar-exclamation:before { content: @fa-var-calendar-exclamation; }
.@{fa-css-prefix}-calendar-minus:before { content: @fa-var-calendar-minus; }
.@{fa-css-prefix}-calendar-plus:before { content: @fa-var-calendar-plus; }
.@{fa-css-prefix}-calendar-star:before { content: @fa-var-calendar-star; }
.@{fa-css-prefix}-calendar-times:before { content: @fa-var-calendar-times; }
.@{fa-css-prefix}-calendar-week:before { content: @fa-var-calendar-week; }
.@{fa-css-prefix}-camera:before { content: @fa-var-camera; }
.@{fa-css-prefix}-camera-alt:before { content: @fa-var-camera-alt; }
.@{fa-css-prefix}-camera-retro:before { content: @fa-var-camera-retro; }
.@{fa-css-prefix}-campfire:before { content: @fa-var-campfire; }
.@{fa-css-prefix}-campground:before { content: @fa-var-campground; }
.@{fa-css-prefix}-canadian-maple-leaf:before { content: @fa-var-canadian-maple-leaf; }
.@{fa-css-prefix}-candle-holder:before { content: @fa-var-candle-holder; }
.@{fa-css-prefix}-candy-cane:before { content: @fa-var-candy-cane; }
.@{fa-css-prefix}-candy-corn:before { content: @fa-var-candy-corn; }
.@{fa-css-prefix}-cannabis:before { content: @fa-var-cannabis; }
.@{fa-css-prefix}-capsules:before { content: @fa-var-capsules; }
.@{fa-css-prefix}-car:before { content: @fa-var-car; }
.@{fa-css-prefix}-car-alt:before { content: @fa-var-car-alt; }
.@{fa-css-prefix}-car-battery:before { content: @fa-var-car-battery; }
.@{fa-css-prefix}-car-building:before { content: @fa-var-car-building; }
.@{fa-css-prefix}-car-bump:before { content: @fa-var-car-bump; }
.@{fa-css-prefix}-car-bus:before { content: @fa-var-car-bus; }
.@{fa-css-prefix}-car-crash:before { content: @fa-var-car-crash; }
.@{fa-css-prefix}-car-garage:before { content: @fa-var-car-garage; }
.@{fa-css-prefix}-car-mechanic:before { content: @fa-var-car-mechanic; }
.@{fa-css-prefix}-car-side:before { content: @fa-var-car-side; }
.@{fa-css-prefix}-car-tilt:before { content: @fa-var-car-tilt; }
.@{fa-css-prefix}-car-wash:before { content: @fa-var-car-wash; }
.@{fa-css-prefix}-caret-circle-down:before { content: @fa-var-caret-circle-down; }
.@{fa-css-prefix}-caret-circle-left:before { content: @fa-var-caret-circle-left; }
.@{fa-css-prefix}-caret-circle-right:before { content: @fa-var-caret-circle-right; }
.@{fa-css-prefix}-caret-circle-up:before { content: @fa-var-caret-circle-up; }
.@{fa-css-prefix}-caret-down:before { content: @fa-var-caret-down; }
.@{fa-css-prefix}-caret-left:before { content: @fa-var-caret-left; }
.@{fa-css-prefix}-caret-right:before { content: @fa-var-caret-right; }
.@{fa-css-prefix}-caret-square-down:before { content: @fa-var-caret-square-down; }
.@{fa-css-prefix}-caret-square-left:before { content: @fa-var-caret-square-left; }
.@{fa-css-prefix}-caret-square-right:before { content: @fa-var-caret-square-right; }
.@{fa-css-prefix}-caret-square-up:before { content: @fa-var-caret-square-up; }
.@{fa-css-prefix}-caret-up:before { content: @fa-var-caret-up; }
.@{fa-css-prefix}-carrot:before { content: @fa-var-carrot; }
.@{fa-css-prefix}-cars:before { content: @fa-var-cars; }
.@{fa-css-prefix}-cart-arrow-down:before { content: @fa-var-cart-arrow-down; }
.@{fa-css-prefix}-cart-plus:before { content: @fa-var-cart-plus; }
.@{fa-css-prefix}-cash-register:before { content: @fa-var-cash-register; }
.@{fa-css-prefix}-cat:before { content: @fa-var-cat; }
.@{fa-css-prefix}-cauldron:before { content: @fa-var-cauldron; }
.@{fa-css-prefix}-cc-amazon-pay:before { content: @fa-var-cc-amazon-pay; }
.@{fa-css-prefix}-cc-amex:before { content: @fa-var-cc-amex; }
.@{fa-css-prefix}-cc-apple-pay:before { content: @fa-var-cc-apple-pay; }
.@{fa-css-prefix}-cc-diners-club:before { content: @fa-var-cc-diners-club; }
.@{fa-css-prefix}-cc-discover:before { content: @fa-var-cc-discover; }
.@{fa-css-prefix}-cc-jcb:before { content: @fa-var-cc-jcb; }
.@{fa-css-prefix}-cc-mastercard:before { content: @fa-var-cc-mastercard; }
.@{fa-css-prefix}-cc-paypal:before { content: @fa-var-cc-paypal; }
.@{fa-css-prefix}-cc-stripe:before { content: @fa-var-cc-stripe; }
.@{fa-css-prefix}-cc-visa:before { content: @fa-var-cc-visa; }
.@{fa-css-prefix}-centercode:before { content: @fa-var-centercode; }
.@{fa-css-prefix}-centos:before { content: @fa-var-centos; }
.@{fa-css-prefix}-certificate:before { content: @fa-var-certificate; }
.@{fa-css-prefix}-chair:before { content: @fa-var-chair; }
.@{fa-css-prefix}-chair-office:before { content: @fa-var-chair-office; }
.@{fa-css-prefix}-chalkboard:before { content: @fa-var-chalkboard; }
.@{fa-css-prefix}-chalkboard-teacher:before { content: @fa-var-chalkboard-teacher; }
.@{fa-css-prefix}-charging-station:before { content: @fa-var-charging-station; }
.@{fa-css-prefix}-chart-area:before { content: @fa-var-chart-area; }
.@{fa-css-prefix}-chart-bar:before { content: @fa-var-chart-bar; }
.@{fa-css-prefix}-chart-line:before { content: @fa-var-chart-line; }
.@{fa-css-prefix}-chart-line-down:before { content: @fa-var-chart-line-down; }
.@{fa-css-prefix}-chart-network:before { content: @fa-var-chart-network; }
.@{fa-css-prefix}-chart-pie:before { content: @fa-var-chart-pie; }
.@{fa-css-prefix}-chart-pie-alt:before { content: @fa-var-chart-pie-alt; }
.@{fa-css-prefix}-chart-scatter:before { content: @fa-var-chart-scatter; }
.@{fa-css-prefix}-check:before { content: @fa-var-check; }
.@{fa-css-prefix}-check-circle:before { content: @fa-var-check-circle; }
.@{fa-css-prefix}-check-double:before { content: @fa-var-check-double; }
.@{fa-css-prefix}-check-square:before { content: @fa-var-check-square; }
.@{fa-css-prefix}-cheese:before { content: @fa-var-cheese; }
.@{fa-css-prefix}-cheese-swiss:before { content: @fa-var-cheese-swiss; }
.@{fa-css-prefix}-cheeseburger:before { content: @fa-var-cheeseburger; }
.@{fa-css-prefix}-chess:before { content: @fa-var-chess; }
.@{fa-css-prefix}-chess-bishop:before { content: @fa-var-chess-bishop; }
.@{fa-css-prefix}-chess-bishop-alt:before { content: @fa-var-chess-bishop-alt; }
.@{fa-css-prefix}-chess-board:before { content: @fa-var-chess-board; }
.@{fa-css-prefix}-chess-clock:before { content: @fa-var-chess-clock; }
.@{fa-css-prefix}-chess-clock-alt:before { content: @fa-var-chess-clock-alt; }
.@{fa-css-prefix}-chess-king:before { content: @fa-var-chess-king; }
.@{fa-css-prefix}-chess-king-alt:before { content: @fa-var-chess-king-alt; }
.@{fa-css-prefix}-chess-knight:before { content: @fa-var-chess-knight; }
.@{fa-css-prefix}-chess-knight-alt:before { content: @fa-var-chess-knight-alt; }
.@{fa-css-prefix}-chess-pawn:before { content: @fa-var-chess-pawn; }
.@{fa-css-prefix}-chess-pawn-alt:before { content: @fa-var-chess-pawn-alt; }
.@{fa-css-prefix}-chess-queen:before { content: @fa-var-chess-queen; }
.@{fa-css-prefix}-chess-queen-alt:before { content: @fa-var-chess-queen-alt; }
.@{fa-css-prefix}-chess-rook:before { content: @fa-var-chess-rook; }
.@{fa-css-prefix}-chess-rook-alt:before { content: @fa-var-chess-rook-alt; }
.@{fa-css-prefix}-chevron-circle-down:before { content: @fa-var-chevron-circle-down; }
.@{fa-css-prefix}-chevron-circle-left:before { content: @fa-var-chevron-circle-left; }
.@{fa-css-prefix}-chevron-circle-right:before { content: @fa-var-chevron-circle-right; }
.@{fa-css-prefix}-chevron-circle-up:before { content: @fa-var-chevron-circle-up; }
.@{fa-css-prefix}-chevron-double-down:before { content: @fa-var-chevron-double-down; }
.@{fa-css-prefix}-chevron-double-left:before { content: @fa-var-chevron-double-left; }
.@{fa-css-prefix}-chevron-double-right:before { content: @fa-var-chevron-double-right; }
.@{fa-css-prefix}-chevron-double-up:before { content: @fa-var-chevron-double-up; }
.@{fa-css-prefix}-chevron-down:before { content: @fa-var-chevron-down; }
.@{fa-css-prefix}-chevron-left:before { content: @fa-var-chevron-left; }
.@{fa-css-prefix}-chevron-right:before { content: @fa-var-chevron-right; }
.@{fa-css-prefix}-chevron-square-down:before { content: @fa-var-chevron-square-down; }
.@{fa-css-prefix}-chevron-square-left:before { content: @fa-var-chevron-square-left; }
.@{fa-css-prefix}-chevron-square-right:before { content: @fa-var-chevron-square-right; }
.@{fa-css-prefix}-chevron-square-up:before { content: @fa-var-chevron-square-up; }
.@{fa-css-prefix}-chevron-up:before { content: @fa-var-chevron-up; }
.@{fa-css-prefix}-child:before { content: @fa-var-child; }
.@{fa-css-prefix}-chimney:before { content: @fa-var-chimney; }
.@{fa-css-prefix}-chrome:before { content: @fa-var-chrome; }
.@{fa-css-prefix}-chromecast:before { content: @fa-var-chromecast; }
.@{fa-css-prefix}-church:before { content: @fa-var-church; }
.@{fa-css-prefix}-circle:before { content: @fa-var-circle; }
.@{fa-css-prefix}-circle-notch:before { content: @fa-var-circle-notch; }
.@{fa-css-prefix}-city:before { content: @fa-var-city; }
.@{fa-css-prefix}-claw-marks:before { content: @fa-var-claw-marks; }
.@{fa-css-prefix}-clinic-medical:before { content: @fa-var-clinic-medical; }
.@{fa-css-prefix}-clipboard:before { content: @fa-var-clipboard; }
.@{fa-css-prefix}-clipboard-check:before { content: @fa-var-clipboard-check; }
.@{fa-css-prefix}-clipboard-list:before { content: @fa-var-clipboard-list; }
.@{fa-css-prefix}-clipboard-list-check:before { content: @fa-var-clipboard-list-check; }
.@{fa-css-prefix}-clipboard-prescription:before { content: @fa-var-clipboard-prescription; }
.@{fa-css-prefix}-clipboard-user:before { content: @fa-var-clipboard-user; }
.@{fa-css-prefix}-clock:before { content: @fa-var-clock; }
.@{fa-css-prefix}-clone:before { content: @fa-var-clone; }
.@{fa-css-prefix}-closed-captioning:before { content: @fa-var-closed-captioning; }
.@{fa-css-prefix}-cloud:before { content: @fa-var-cloud; }
.@{fa-css-prefix}-cloud-download:before { content: @fa-var-cloud-download; }
.@{fa-css-prefix}-cloud-download-alt:before { content: @fa-var-cloud-download-alt; }
.@{fa-css-prefix}-cloud-drizzle:before { content: @fa-var-cloud-drizzle; }
.@{fa-css-prefix}-cloud-hail:before { content: @fa-var-cloud-hail; }
.@{fa-css-prefix}-cloud-hail-mixed:before { content: @fa-var-cloud-hail-mixed; }
.@{fa-css-prefix}-cloud-meatball:before { content: @fa-var-cloud-meatball; }
.@{fa-css-prefix}-cloud-moon:before { content: @fa-var-cloud-moon; }
.@{fa-css-prefix}-cloud-moon-rain:before { content: @fa-var-cloud-moon-rain; }
.@{fa-css-prefix}-cloud-rain:before { content: @fa-var-cloud-rain; }
.@{fa-css-prefix}-cloud-rainbow:before { content: @fa-var-cloud-rainbow; }
.@{fa-css-prefix}-cloud-showers:before { content: @fa-var-cloud-showers; }
.@{fa-css-prefix}-cloud-showers-heavy:before { content: @fa-var-cloud-showers-heavy; }
.@{fa-css-prefix}-cloud-sleet:before { content: @fa-var-cloud-sleet; }
.@{fa-css-prefix}-cloud-snow:before { content: @fa-var-cloud-snow; }
.@{fa-css-prefix}-cloud-sun:before { content: @fa-var-cloud-sun; }
.@{fa-css-prefix}-cloud-sun-rain:before { content: @fa-var-cloud-sun-rain; }
.@{fa-css-prefix}-cloud-upload:before { content: @fa-var-cloud-upload; }
.@{fa-css-prefix}-cloud-upload-alt:before { content: @fa-var-cloud-upload-alt; }
.@{fa-css-prefix}-clouds:before { content: @fa-var-clouds; }
.@{fa-css-prefix}-clouds-moon:before { content: @fa-var-clouds-moon; }
.@{fa-css-prefix}-clouds-sun:before { content: @fa-var-clouds-sun; }
.@{fa-css-prefix}-cloudscale:before { content: @fa-var-cloudscale; }
.@{fa-css-prefix}-cloudsmith:before { content: @fa-var-cloudsmith; }
.@{fa-css-prefix}-cloudversify:before { content: @fa-var-cloudversify; }
.@{fa-css-prefix}-club:before { content: @fa-var-club; }
.@{fa-css-prefix}-cocktail:before { content: @fa-var-cocktail; }
.@{fa-css-prefix}-code:before { content: @fa-var-code; }
.@{fa-css-prefix}-code-branch:before { content: @fa-var-code-branch; }
.@{fa-css-prefix}-code-commit:before { content: @fa-var-code-commit; }
.@{fa-css-prefix}-code-merge:before { content: @fa-var-code-merge; }
.@{fa-css-prefix}-codepen:before { content: @fa-var-codepen; }
.@{fa-css-prefix}-codiepie:before { content: @fa-var-codiepie; }
.@{fa-css-prefix}-coffee:before { content: @fa-var-coffee; }
.@{fa-css-prefix}-coffee-togo:before { content: @fa-var-coffee-togo; }
.@{fa-css-prefix}-coffin:before { content: @fa-var-coffin; }
.@{fa-css-prefix}-cog:before { content: @fa-var-cog; }
.@{fa-css-prefix}-cogs:before { content: @fa-var-cogs; }
.@{fa-css-prefix}-coin:before { content: @fa-var-coin; }
.@{fa-css-prefix}-coins:before { content: @fa-var-coins; }
.@{fa-css-prefix}-columns:before { content: @fa-var-columns; }
.@{fa-css-prefix}-comment:before { content: @fa-var-comment; }
.@{fa-css-prefix}-comment-alt:before { content: @fa-var-comment-alt; }
.@{fa-css-prefix}-comment-alt-check:before { content: @fa-var-comment-alt-check; }
.@{fa-css-prefix}-comment-alt-dollar:before { content: @fa-var-comment-alt-dollar; }
.@{fa-css-prefix}-comment-alt-dots:before { content: @fa-var-comment-alt-dots; }
.@{fa-css-prefix}-comment-alt-edit:before { content: @fa-var-comment-alt-edit; }
.@{fa-css-prefix}-comment-alt-exclamation:before { content: @fa-var-comment-alt-exclamation; }
.@{fa-css-prefix}-comment-alt-lines:before { content: @fa-var-comment-alt-lines; }
.@{fa-css-prefix}-comment-alt-medical:before { content: @fa-var-comment-alt-medical; }
.@{fa-css-prefix}-comment-alt-minus:before { content: @fa-var-comment-alt-minus; }
.@{fa-css-prefix}-comment-alt-plus:before { content: @fa-var-comment-alt-plus; }
.@{fa-css-prefix}-comment-alt-slash:before { content: @fa-var-comment-alt-slash; }
.@{fa-css-prefix}-comment-alt-smile:before { content: @fa-var-comment-alt-smile; }
.@{fa-css-prefix}-comment-alt-times:before { content: @fa-var-comment-alt-times; }
.@{fa-css-prefix}-comment-check:before { content: @fa-var-comment-check; }
.@{fa-css-prefix}-comment-dollar:before { content: @fa-var-comment-dollar; }
.@{fa-css-prefix}-comment-dots:before { content: @fa-var-comment-dots; }
.@{fa-css-prefix}-comment-edit:before { content: @fa-var-comment-edit; }
.@{fa-css-prefix}-comment-exclamation:before { content: @fa-var-comment-exclamation; }
.@{fa-css-prefix}-comment-lines:before { content: @fa-var-comment-lines; }
.@{fa-css-prefix}-comment-medical:before { content: @fa-var-comment-medical; }
.@{fa-css-prefix}-comment-minus:before { content: @fa-var-comment-minus; }
.@{fa-css-prefix}-comment-plus:before { content: @fa-var-comment-plus; }
.@{fa-css-prefix}-comment-slash:before { content: @fa-var-comment-slash; }
.@{fa-css-prefix}-comment-smile:before { content: @fa-var-comment-smile; }
.@{fa-css-prefix}-comment-times:before { content: @fa-var-comment-times; }
.@{fa-css-prefix}-comments:before { content: @fa-var-comments; }
.@{fa-css-prefix}-comments-alt:before { content: @fa-var-comments-alt; }
.@{fa-css-prefix}-comments-alt-dollar:before { content: @fa-var-comments-alt-dollar; }
.@{fa-css-prefix}-comments-dollar:before { content: @fa-var-comments-dollar; }
.@{fa-css-prefix}-compact-disc:before { content: @fa-var-compact-disc; }
.@{fa-css-prefix}-compass:before { content: @fa-var-compass; }
.@{fa-css-prefix}-compass-slash:before { content: @fa-var-compass-slash; }
.@{fa-css-prefix}-compress:before { content: @fa-var-compress; }
.@{fa-css-prefix}-compress-alt:before { content: @fa-var-compress-alt; }
.@{fa-css-prefix}-compress-arrows-alt:before { content: @fa-var-compress-arrows-alt; }
.@{fa-css-prefix}-compress-wide:before { content: @fa-var-compress-wide; }
.@{fa-css-prefix}-concierge-bell:before { content: @fa-var-concierge-bell; }
.@{fa-css-prefix}-confluence:before { content: @fa-var-confluence; }
.@{fa-css-prefix}-connectdevelop:before { content: @fa-var-connectdevelop; }
.@{fa-css-prefix}-construction:before { content: @fa-var-construction; }
.@{fa-css-prefix}-container-storage:before { content: @fa-var-container-storage; }
.@{fa-css-prefix}-contao:before { content: @fa-var-contao; }
.@{fa-css-prefix}-conveyor-belt:before { content: @fa-var-conveyor-belt; }
.@{fa-css-prefix}-conveyor-belt-alt:before { content: @fa-var-conveyor-belt-alt; }
.@{fa-css-prefix}-cookie:before { content: @fa-var-cookie; }
.@{fa-css-prefix}-cookie-bite:before { content: @fa-var-cookie-bite; }
.@{fa-css-prefix}-copy:before { content: @fa-var-copy; }
.@{fa-css-prefix}-copyright:before { content: @fa-var-copyright; }
.@{fa-css-prefix}-corn:before { content: @fa-var-corn; }
.@{fa-css-prefix}-cotton-bureau:before { content: @fa-var-cotton-bureau; }
.@{fa-css-prefix}-couch:before { content: @fa-var-couch; }
.@{fa-css-prefix}-cow:before { content: @fa-var-cow; }
.@{fa-css-prefix}-cpanel:before { content: @fa-var-cpanel; }
.@{fa-css-prefix}-creative-commons:before { content: @fa-var-creative-commons; }
.@{fa-css-prefix}-creative-commons-by:before { content: @fa-var-creative-commons-by; }
.@{fa-css-prefix}-creative-commons-nc:before { content: @fa-var-creative-commons-nc; }
.@{fa-css-prefix}-creative-commons-nc-eu:before { content: @fa-var-creative-commons-nc-eu; }
.@{fa-css-prefix}-creative-commons-nc-jp:before { content: @fa-var-creative-commons-nc-jp; }
.@{fa-css-prefix}-creative-commons-nd:before { content: @fa-var-creative-commons-nd; }
.@{fa-css-prefix}-creative-commons-pd:before { content: @fa-var-creative-commons-pd; }
.@{fa-css-prefix}-creative-commons-pd-alt:before { content: @fa-var-creative-commons-pd-alt; }
.@{fa-css-prefix}-creative-commons-remix:before { content: @fa-var-creative-commons-remix; }
.@{fa-css-prefix}-creative-commons-sa:before { content: @fa-var-creative-commons-sa; }
.@{fa-css-prefix}-creative-commons-sampling:before { content: @fa-var-creative-commons-sampling; }
.@{fa-css-prefix}-creative-commons-sampling-plus:before { content: @fa-var-creative-commons-sampling-plus; }
.@{fa-css-prefix}-creative-commons-share:before { content: @fa-var-creative-commons-share; }
.@{fa-css-prefix}-creative-commons-zero:before { content: @fa-var-creative-commons-zero; }
.@{fa-css-prefix}-credit-card:before { content: @fa-var-credit-card; }
.@{fa-css-prefix}-credit-card-blank:before { content: @fa-var-credit-card-blank; }
.@{fa-css-prefix}-credit-card-front:before { content: @fa-var-credit-card-front; }
.@{fa-css-prefix}-cricket:before { content: @fa-var-cricket; }
.@{fa-css-prefix}-critical-role:before { content: @fa-var-critical-role; }
.@{fa-css-prefix}-croissant:before { content: @fa-var-croissant; }
.@{fa-css-prefix}-crop:before { content: @fa-var-crop; }
.@{fa-css-prefix}-crop-alt:before { content: @fa-var-crop-alt; }
.@{fa-css-prefix}-cross:before { content: @fa-var-cross; }
.@{fa-css-prefix}-crosshairs:before { content: @fa-var-crosshairs; }
.@{fa-css-prefix}-crow:before { content: @fa-var-crow; }
.@{fa-css-prefix}-crown:before { content: @fa-var-crown; }
.@{fa-css-prefix}-crutch:before { content: @fa-var-crutch; }
.@{fa-css-prefix}-crutches:before { content: @fa-var-crutches; }
.@{fa-css-prefix}-css3:before { content: @fa-var-css3; }
.@{fa-css-prefix}-css3-alt:before { content: @fa-var-css3-alt; }
.@{fa-css-prefix}-cube:before { content: @fa-var-cube; }
.@{fa-css-prefix}-cubes:before { content: @fa-var-cubes; }
.@{fa-css-prefix}-curling:before { content: @fa-var-curling; }
.@{fa-css-prefix}-cut:before { content: @fa-var-cut; }
.@{fa-css-prefix}-cuttlefish:before { content: @fa-var-cuttlefish; }
.@{fa-css-prefix}-d-and-d:before { content: @fa-var-d-and-d; }
.@{fa-css-prefix}-d-and-d-beyond:before { content: @fa-var-d-and-d-beyond; }
.@{fa-css-prefix}-dagger:before { content: @fa-var-dagger; }
.@{fa-css-prefix}-dashcube:before { content: @fa-var-dashcube; }
.@{fa-css-prefix}-database:before { content: @fa-var-database; }
.@{fa-css-prefix}-deaf:before { content: @fa-var-deaf; }
.@{fa-css-prefix}-debug:before { content: @fa-var-debug; }
.@{fa-css-prefix}-deer:before { content: @fa-var-deer; }
.@{fa-css-prefix}-deer-rudolph:before { content: @fa-var-deer-rudolph; }
.@{fa-css-prefix}-delicious:before { content: @fa-var-delicious; }
.@{fa-css-prefix}-democrat:before { content: @fa-var-democrat; }
.@{fa-css-prefix}-deploydog:before { content: @fa-var-deploydog; }
.@{fa-css-prefix}-deskpro:before { content: @fa-var-deskpro; }
.@{fa-css-prefix}-desktop:before { content: @fa-var-desktop; }
.@{fa-css-prefix}-desktop-alt:before { content: @fa-var-desktop-alt; }
.@{fa-css-prefix}-dev:before { content: @fa-var-dev; }
.@{fa-css-prefix}-deviantart:before { content: @fa-var-deviantart; }
.@{fa-css-prefix}-dewpoint:before { content: @fa-var-dewpoint; }
.@{fa-css-prefix}-dharmachakra:before { content: @fa-var-dharmachakra; }
.@{fa-css-prefix}-dhl:before { content: @fa-var-dhl; }
.@{fa-css-prefix}-diagnoses:before { content: @fa-var-diagnoses; }
.@{fa-css-prefix}-diamond:before { content: @fa-var-diamond; }
.@{fa-css-prefix}-diaspora:before { content: @fa-var-diaspora; }
.@{fa-css-prefix}-dice:before { content: @fa-var-dice; }
.@{fa-css-prefix}-dice-d10:before { content: @fa-var-dice-d10; }
.@{fa-css-prefix}-dice-d12:before { content: @fa-var-dice-d12; }
.@{fa-css-prefix}-dice-d20:before { content: @fa-var-dice-d20; }
.@{fa-css-prefix}-dice-d4:before { content: @fa-var-dice-d4; }
.@{fa-css-prefix}-dice-d6:before { content: @fa-var-dice-d6; }
.@{fa-css-prefix}-dice-d8:before { content: @fa-var-dice-d8; }
.@{fa-css-prefix}-dice-five:before { content: @fa-var-dice-five; }
.@{fa-css-prefix}-dice-four:before { content: @fa-var-dice-four; }
.@{fa-css-prefix}-dice-one:before { content: @fa-var-dice-one; }
.@{fa-css-prefix}-dice-six:before { content: @fa-var-dice-six; }
.@{fa-css-prefix}-dice-three:before { content: @fa-var-dice-three; }
.@{fa-css-prefix}-dice-two:before { content: @fa-var-dice-two; }
.@{fa-css-prefix}-digg:before { content: @fa-var-digg; }
.@{fa-css-prefix}-digging:before { content: @fa-var-digging; }
.@{fa-css-prefix}-digital-ocean:before { content: @fa-var-digital-ocean; }
.@{fa-css-prefix}-digital-tachograph:before { content: @fa-var-digital-tachograph; }
.@{fa-css-prefix}-diploma:before { content: @fa-var-diploma; }
.@{fa-css-prefix}-directions:before { content: @fa-var-directions; }
.@{fa-css-prefix}-discord:before { content: @fa-var-discord; }
.@{fa-css-prefix}-discourse:before { content: @fa-var-discourse; }
.@{fa-css-prefix}-disease:before { content: @fa-var-disease; }
.@{fa-css-prefix}-divide:before { content: @fa-var-divide; }
.@{fa-css-prefix}-dizzy:before { content: @fa-var-dizzy; }
.@{fa-css-prefix}-dna:before { content: @fa-var-dna; }
.@{fa-css-prefix}-do-not-enter:before { content: @fa-var-do-not-enter; }
.@{fa-css-prefix}-dochub:before { content: @fa-var-dochub; }
.@{fa-css-prefix}-docker:before { content: @fa-var-docker; }
.@{fa-css-prefix}-dog:before { content: @fa-var-dog; }
.@{fa-css-prefix}-dog-leashed:before { content: @fa-var-dog-leashed; }
.@{fa-css-prefix}-dollar-sign:before { content: @fa-var-dollar-sign; }
.@{fa-css-prefix}-dolly:before { content: @fa-var-dolly; }
.@{fa-css-prefix}-dolly-empty:before { content: @fa-var-dolly-empty; }
.@{fa-css-prefix}-dolly-flatbed:before { content: @fa-var-dolly-flatbed; }
.@{fa-css-prefix}-dolly-flatbed-alt:before { content: @fa-var-dolly-flatbed-alt; }
.@{fa-css-prefix}-dolly-flatbed-empty:before { content: @fa-var-dolly-flatbed-empty; }
.@{fa-css-prefix}-donate:before { content: @fa-var-donate; }
.@{fa-css-prefix}-door-closed:before { content: @fa-var-door-closed; }
.@{fa-css-prefix}-door-open:before { content: @fa-var-door-open; }
.@{fa-css-prefix}-dot-circle:before { content: @fa-var-dot-circle; }
.@{fa-css-prefix}-dove:before { content: @fa-var-dove; }
.@{fa-css-prefix}-download:before { content: @fa-var-download; }
.@{fa-css-prefix}-draft2digital:before { content: @fa-var-draft2digital; }
.@{fa-css-prefix}-drafting-compass:before { content: @fa-var-drafting-compass; }
.@{fa-css-prefix}-dragon:before { content: @fa-var-dragon; }
.@{fa-css-prefix}-draw-circle:before { content: @fa-var-draw-circle; }
.@{fa-css-prefix}-draw-polygon:before { content: @fa-var-draw-polygon; }
.@{fa-css-prefix}-draw-square:before { content: @fa-var-draw-square; }
.@{fa-css-prefix}-dreidel:before { content: @fa-var-dreidel; }
.@{fa-css-prefix}-dribbble:before { content: @fa-var-dribbble; }
.@{fa-css-prefix}-dribbble-square:before { content: @fa-var-dribbble-square; }
.@{fa-css-prefix}-drone:before { content: @fa-var-drone; }
.@{fa-css-prefix}-drone-alt:before { content: @fa-var-drone-alt; }
.@{fa-css-prefix}-dropbox:before { content: @fa-var-dropbox; }
.@{fa-css-prefix}-drum:before { content: @fa-var-drum; }
.@{fa-css-prefix}-drum-steelpan:before { content: @fa-var-drum-steelpan; }
.@{fa-css-prefix}-drumstick:before { content: @fa-var-drumstick; }
.@{fa-css-prefix}-drumstick-bite:before { content: @fa-var-drumstick-bite; }
.@{fa-css-prefix}-drupal:before { content: @fa-var-drupal; }
.@{fa-css-prefix}-dryer:before { content: @fa-var-dryer; }
.@{fa-css-prefix}-dryer-alt:before { content: @fa-var-dryer-alt; }
.@{fa-css-prefix}-duck:before { content: @fa-var-duck; }
.@{fa-css-prefix}-dumbbell:before { content: @fa-var-dumbbell; }
.@{fa-css-prefix}-dumpster:before { content: @fa-var-dumpster; }
.@{fa-css-prefix}-dumpster-fire:before { content: @fa-var-dumpster-fire; }
.@{fa-css-prefix}-dungeon:before { content: @fa-var-dungeon; }
.@{fa-css-prefix}-dyalog:before { content: @fa-var-dyalog; }
.@{fa-css-prefix}-ear:before { content: @fa-var-ear; }
.@{fa-css-prefix}-ear-muffs:before { content: @fa-var-ear-muffs; }
.@{fa-css-prefix}-earlybirds:before { content: @fa-var-earlybirds; }
.@{fa-css-prefix}-ebay:before { content: @fa-var-ebay; }
.@{fa-css-prefix}-eclipse:before { content: @fa-var-eclipse; }
.@{fa-css-prefix}-eclipse-alt:before { content: @fa-var-eclipse-alt; }
.@{fa-css-prefix}-edge:before { content: @fa-var-edge; }
.@{fa-css-prefix}-edit:before { content: @fa-var-edit; }
.@{fa-css-prefix}-egg:before { content: @fa-var-egg; }
.@{fa-css-prefix}-egg-fried:before { content: @fa-var-egg-fried; }
.@{fa-css-prefix}-eject:before { content: @fa-var-eject; }
.@{fa-css-prefix}-elementor:before { content: @fa-var-elementor; }
.@{fa-css-prefix}-elephant:before { content: @fa-var-elephant; }
.@{fa-css-prefix}-ellipsis-h:before { content: @fa-var-ellipsis-h; }
.@{fa-css-prefix}-ellipsis-h-alt:before { content: @fa-var-ellipsis-h-alt; }
.@{fa-css-prefix}-ellipsis-v:before { content: @fa-var-ellipsis-v; }
.@{fa-css-prefix}-ellipsis-v-alt:before { content: @fa-var-ellipsis-v-alt; }
.@{fa-css-prefix}-ello:before { content: @fa-var-ello; }
.@{fa-css-prefix}-ember:before { content: @fa-var-ember; }
.@{fa-css-prefix}-empire:before { content: @fa-var-empire; }
.@{fa-css-prefix}-empty-set:before { content: @fa-var-empty-set; }
.@{fa-css-prefix}-engine-warning:before { content: @fa-var-engine-warning; }
.@{fa-css-prefix}-envelope:before { content: @fa-var-envelope; }
.@{fa-css-prefix}-envelope-open:before { content: @fa-var-envelope-open; }
.@{fa-css-prefix}-envelope-open-dollar:before { content: @fa-var-envelope-open-dollar; }
.@{fa-css-prefix}-envelope-open-text:before { content: @fa-var-envelope-open-text; }
.@{fa-css-prefix}-envelope-square:before { content: @fa-var-envelope-square; }
.@{fa-css-prefix}-envira:before { content: @fa-var-envira; }
.@{fa-css-prefix}-equals:before { content: @fa-var-equals; }
.@{fa-css-prefix}-eraser:before { content: @fa-var-eraser; }
.@{fa-css-prefix}-erlang:before { content: @fa-var-erlang; }
.@{fa-css-prefix}-ethereum:before { content: @fa-var-ethereum; }
.@{fa-css-prefix}-ethernet:before { content: @fa-var-ethernet; }
.@{fa-css-prefix}-etsy:before { content: @fa-var-etsy; }
.@{fa-css-prefix}-euro-sign:before { content: @fa-var-euro-sign; }
.@{fa-css-prefix}-evernote:before { content: @fa-var-evernote; }
.@{fa-css-prefix}-exchange:before { content: @fa-var-exchange; }
.@{fa-css-prefix}-exchange-alt:before { content: @fa-var-exchange-alt; }
.@{fa-css-prefix}-exclamation:before { content: @fa-var-exclamation; }
.@{fa-css-prefix}-exclamation-circle:before { content: @fa-var-exclamation-circle; }
.@{fa-css-prefix}-exclamation-square:before { content: @fa-var-exclamation-square; }
.@{fa-css-prefix}-exclamation-triangle:before { content: @fa-var-exclamation-triangle; }
.@{fa-css-prefix}-expand:before { content: @fa-var-expand; }
.@{fa-css-prefix}-expand-alt:before { content: @fa-var-expand-alt; }
.@{fa-css-prefix}-expand-arrows:before { content: @fa-var-expand-arrows; }
.@{fa-css-prefix}-expand-arrows-alt:before { content: @fa-var-expand-arrows-alt; }
.@{fa-css-prefix}-expand-wide:before { content: @fa-var-expand-wide; }
.@{fa-css-prefix}-expeditedssl:before { content: @fa-var-expeditedssl; }
.@{fa-css-prefix}-external-link:before { content: @fa-var-external-link; }
.@{fa-css-prefix}-external-link-alt:before { content: @fa-var-external-link-alt; }
.@{fa-css-prefix}-external-link-square:before { content: @fa-var-external-link-square; }
.@{fa-css-prefix}-external-link-square-alt:before { content: @fa-var-external-link-square-alt; }
.@{fa-css-prefix}-eye:before { content: @fa-var-eye; }
.@{fa-css-prefix}-eye-dropper:before { content: @fa-var-eye-dropper; }
.@{fa-css-prefix}-eye-evil:before { content: @fa-var-eye-evil; }
.@{fa-css-prefix}-eye-slash:before { content: @fa-var-eye-slash; }
.@{fa-css-prefix}-facebook:before { content: @fa-var-facebook; }
.@{fa-css-prefix}-facebook-f:before { content: @fa-var-facebook-f; }
.@{fa-css-prefix}-facebook-messenger:before { content: @fa-var-facebook-messenger; }
.@{fa-css-prefix}-facebook-square:before { content: @fa-var-facebook-square; }
.@{fa-css-prefix}-fan:before { content: @fa-var-fan; }
.@{fa-css-prefix}-fantasy-flight-games:before { content: @fa-var-fantasy-flight-games; }
.@{fa-css-prefix}-farm:before { content: @fa-var-farm; }
.@{fa-css-prefix}-fast-backward:before { content: @fa-var-fast-backward; }
.@{fa-css-prefix}-fast-forward:before { content: @fa-var-fast-forward; }
.@{fa-css-prefix}-fax:before { content: @fa-var-fax; }
.@{fa-css-prefix}-feather:before { content: @fa-var-feather; }
.@{fa-css-prefix}-feather-alt:before { content: @fa-var-feather-alt; }
.@{fa-css-prefix}-fedex:before { content: @fa-var-fedex; }
.@{fa-css-prefix}-fedora:before { content: @fa-var-fedora; }
.@{fa-css-prefix}-female:before { content: @fa-var-female; }
.@{fa-css-prefix}-field-hockey:before { content: @fa-var-field-hockey; }
.@{fa-css-prefix}-fighter-jet:before { content: @fa-var-fighter-jet; }
.@{fa-css-prefix}-figma:before { content: @fa-var-figma; }
.@{fa-css-prefix}-file:before { content: @fa-var-file; }
.@{fa-css-prefix}-file-alt:before { content: @fa-var-file-alt; }
.@{fa-css-prefix}-file-archive:before { content: @fa-var-file-archive; }
.@{fa-css-prefix}-file-audio:before { content: @fa-var-file-audio; }
.@{fa-css-prefix}-file-certificate:before { content: @fa-var-file-certificate; }
.@{fa-css-prefix}-file-chart-line:before { content: @fa-var-file-chart-line; }
.@{fa-css-prefix}-file-chart-pie:before { content: @fa-var-file-chart-pie; }
.@{fa-css-prefix}-file-check:before { content: @fa-var-file-check; }
.@{fa-css-prefix}-file-code:before { content: @fa-var-file-code; }
.@{fa-css-prefix}-file-contract:before { content: @fa-var-file-contract; }
.@{fa-css-prefix}-file-csv:before { content: @fa-var-file-csv; }
.@{fa-css-prefix}-file-download:before { content: @fa-var-file-download; }
.@{fa-css-prefix}-file-edit:before { content: @fa-var-file-edit; }
.@{fa-css-prefix}-file-excel:before { content: @fa-var-file-excel; }
.@{fa-css-prefix}-file-exclamation:before { content: @fa-var-file-exclamation; }
.@{fa-css-prefix}-file-export:before { content: @fa-var-file-export; }
.@{fa-css-prefix}-file-image:before { content: @fa-var-file-image; }
.@{fa-css-prefix}-file-import:before { content: @fa-var-file-import; }
.@{fa-css-prefix}-file-invoice:before { content: @fa-var-file-invoice; }
.@{fa-css-prefix}-file-invoice-dollar:before { content: @fa-var-file-invoice-dollar; }
.@{fa-css-prefix}-file-medical:before { content: @fa-var-file-medical; }
.@{fa-css-prefix}-file-medical-alt:before { content: @fa-var-file-medical-alt; }
.@{fa-css-prefix}-file-minus:before { content: @fa-var-file-minus; }
.@{fa-css-prefix}-file-pdf:before { content: @fa-var-file-pdf; }
.@{fa-css-prefix}-file-plus:before { content: @fa-var-file-plus; }
.@{fa-css-prefix}-file-powerpoint:before { content: @fa-var-file-powerpoint; }
.@{fa-css-prefix}-file-prescription:before { content: @fa-var-file-prescription; }
.@{fa-css-prefix}-file-search:before { content: @fa-var-file-search; }
.@{fa-css-prefix}-file-signature:before { content: @fa-var-file-signature; }
.@{fa-css-prefix}-file-spreadsheet:before { content: @fa-var-file-spreadsheet; }
.@{fa-css-prefix}-file-times:before { content: @fa-var-file-times; }
.@{fa-css-prefix}-file-upload:before { content: @fa-var-file-upload; }
.@{fa-css-prefix}-file-user:before { content: @fa-var-file-user; }
.@{fa-css-prefix}-file-video:before { content: @fa-var-file-video; }
.@{fa-css-prefix}-file-word:before { content: @fa-var-file-word; }
.@{fa-css-prefix}-files-medical:before { content: @fa-var-files-medical; }
.@{fa-css-prefix}-fill:before { content: @fa-var-fill; }
.@{fa-css-prefix}-fill-drip:before { content: @fa-var-fill-drip; }
.@{fa-css-prefix}-film:before { content: @fa-var-film; }
.@{fa-css-prefix}-film-alt:before { content: @fa-var-film-alt; }
.@{fa-css-prefix}-filter:before { content: @fa-var-filter; }
.@{fa-css-prefix}-fingerprint:before { content: @fa-var-fingerprint; }
.@{fa-css-prefix}-fire:before { content: @fa-var-fire; }
.@{fa-css-prefix}-fire-alt:before { content: @fa-var-fire-alt; }
.@{fa-css-prefix}-fire-extinguisher:before { content: @fa-var-fire-extinguisher; }
.@{fa-css-prefix}-fire-smoke:before { content: @fa-var-fire-smoke; }
.@{fa-css-prefix}-firefox:before { content: @fa-var-firefox; }
.@{fa-css-prefix}-fireplace:before { content: @fa-var-fireplace; }
.@{fa-css-prefix}-first-aid:before { content: @fa-var-first-aid; }
.@{fa-css-prefix}-first-order:before { content: @fa-var-first-order; }
.@{fa-css-prefix}-first-order-alt:before { content: @fa-var-first-order-alt; }
.@{fa-css-prefix}-firstdraft:before { content: @fa-var-firstdraft; }
.@{fa-css-prefix}-fish:before { content: @fa-var-fish; }
.@{fa-css-prefix}-fish-cooked:before { content: @fa-var-fish-cooked; }
.@{fa-css-prefix}-fist-raised:before { content: @fa-var-fist-raised; }
.@{fa-css-prefix}-flag:before { content: @fa-var-flag; }
.@{fa-css-prefix}-flag-alt:before { content: @fa-var-flag-alt; }
.@{fa-css-prefix}-flag-checkered:before { content: @fa-var-flag-checkered; }
.@{fa-css-prefix}-flag-usa:before { content: @fa-var-flag-usa; }
.@{fa-css-prefix}-flame:before { content: @fa-var-flame; }
.@{fa-css-prefix}-flask:before { content: @fa-var-flask; }
.@{fa-css-prefix}-flask-poison:before { content: @fa-var-flask-poison; }
.@{fa-css-prefix}-flask-potion:before { content: @fa-var-flask-potion; }
.@{fa-css-prefix}-flickr:before { content: @fa-var-flickr; }
.@{fa-css-prefix}-flipboard:before { content: @fa-var-flipboard; }
.@{fa-css-prefix}-flower:before { content: @fa-var-flower; }
.@{fa-css-prefix}-flower-daffodil:before { content: @fa-var-flower-daffodil; }
.@{fa-css-prefix}-flower-tulip:before { content: @fa-var-flower-tulip; }
.@{fa-css-prefix}-flushed:before { content: @fa-var-flushed; }
.@{fa-css-prefix}-fly:before { content: @fa-var-fly; }
.@{fa-css-prefix}-fog:before { content: @fa-var-fog; }
.@{fa-css-prefix}-folder:before { content: @fa-var-folder; }
.@{fa-css-prefix}-folder-minus:before { content: @fa-var-folder-minus; }
.@{fa-css-prefix}-folder-open:before { content: @fa-var-folder-open; }
.@{fa-css-prefix}-folder-plus:before { content: @fa-var-folder-plus; }
.@{fa-css-prefix}-folder-times:before { content: @fa-var-folder-times; }
.@{fa-css-prefix}-folder-tree:before { content: @fa-var-folder-tree; }
.@{fa-css-prefix}-folders:before { content: @fa-var-folders; }
.@{fa-css-prefix}-font:before { content: @fa-var-font; }
.@{fa-css-prefix}-font-awesome:before { content: @fa-var-font-awesome; }
.@{fa-css-prefix}-font-awesome-alt:before { content: @fa-var-font-awesome-alt; }
.@{fa-css-prefix}-font-awesome-flag:before { content: @fa-var-font-awesome-flag; }
.@{fa-css-prefix}-font-awesome-logo-full:before { content: @fa-var-font-awesome-logo-full; }
.@{fa-css-prefix}-font-case:before { content: @fa-var-font-case; }
.@{fa-css-prefix}-fonticons:before { content: @fa-var-fonticons; }
.@{fa-css-prefix}-fonticons-fi:before { content: @fa-var-fonticons-fi; }
.@{fa-css-prefix}-football-ball:before { content: @fa-var-football-ball; }
.@{fa-css-prefix}-football-helmet:before { content: @fa-var-football-helmet; }
.@{fa-css-prefix}-forklift:before { content: @fa-var-forklift; }
.@{fa-css-prefix}-fort-awesome:before { content: @fa-var-fort-awesome; }
.@{fa-css-prefix}-fort-awesome-alt:before { content: @fa-var-fort-awesome-alt; }
.@{fa-css-prefix}-forumbee:before { content: @fa-var-forumbee; }
.@{fa-css-prefix}-forward:before { content: @fa-var-forward; }
.@{fa-css-prefix}-foursquare:before { content: @fa-var-foursquare; }
.@{fa-css-prefix}-fragile:before { content: @fa-var-fragile; }
.@{fa-css-prefix}-free-code-camp:before { content: @fa-var-free-code-camp; }
.@{fa-css-prefix}-freebsd:before { content: @fa-var-freebsd; }
.@{fa-css-prefix}-french-fries:before { content: @fa-var-french-fries; }
.@{fa-css-prefix}-frog:before { content: @fa-var-frog; }
.@{fa-css-prefix}-frosty-head:before { content: @fa-var-frosty-head; }
.@{fa-css-prefix}-frown:before { content: @fa-var-frown; }
.@{fa-css-prefix}-frown-open:before { content: @fa-var-frown-open; }
.@{fa-css-prefix}-fulcrum:before { content: @fa-var-fulcrum; }
.@{fa-css-prefix}-function:before { content: @fa-var-function; }
.@{fa-css-prefix}-funnel-dollar:before { content: @fa-var-funnel-dollar; }
.@{fa-css-prefix}-futbol:before { content: @fa-var-futbol; }
.@{fa-css-prefix}-galactic-republic:before { content: @fa-var-galactic-republic; }
.@{fa-css-prefix}-galactic-senate:before { content: @fa-var-galactic-senate; }
.@{fa-css-prefix}-game-board:before { content: @fa-var-game-board; }
.@{fa-css-prefix}-game-board-alt:before { content: @fa-var-game-board-alt; }
.@{fa-css-prefix}-gamepad:before { content: @fa-var-gamepad; }
.@{fa-css-prefix}-gas-pump:before { content: @fa-var-gas-pump; }
.@{fa-css-prefix}-gas-pump-slash:before { content: @fa-var-gas-pump-slash; }
.@{fa-css-prefix}-gavel:before { content: @fa-var-gavel; }
.@{fa-css-prefix}-gem:before { content: @fa-var-gem; }
.@{fa-css-prefix}-genderless:before { content: @fa-var-genderless; }
.@{fa-css-prefix}-get-pocket:before { content: @fa-var-get-pocket; }
.@{fa-css-prefix}-gg:before { content: @fa-var-gg; }
.@{fa-css-prefix}-gg-circle:before { content: @fa-var-gg-circle; }
.@{fa-css-prefix}-ghost:before { content: @fa-var-ghost; }
.@{fa-css-prefix}-gift:before { content: @fa-var-gift; }
.@{fa-css-prefix}-gift-card:before { content: @fa-var-gift-card; }
.@{fa-css-prefix}-gifts:before { content: @fa-var-gifts; }
.@{fa-css-prefix}-gingerbread-man:before { content: @fa-var-gingerbread-man; }
.@{fa-css-prefix}-git:before { content: @fa-var-git; }
.@{fa-css-prefix}-git-alt:before { content: @fa-var-git-alt; }
.@{fa-css-prefix}-git-square:before { content: @fa-var-git-square; }
.@{fa-css-prefix}-github:before { content: @fa-var-github; }
.@{fa-css-prefix}-github-alt:before { content: @fa-var-github-alt; }
.@{fa-css-prefix}-github-square:before { content: @fa-var-github-square; }
.@{fa-css-prefix}-gitkraken:before { content: @fa-var-gitkraken; }
.@{fa-css-prefix}-gitlab:before { content: @fa-var-gitlab; }
.@{fa-css-prefix}-gitter:before { content: @fa-var-gitter; }
.@{fa-css-prefix}-glass:before { content: @fa-var-glass; }
.@{fa-css-prefix}-glass-champagne:before { content: @fa-var-glass-champagne; }
.@{fa-css-prefix}-glass-cheers:before { content: @fa-var-glass-cheers; }
.@{fa-css-prefix}-glass-citrus:before { content: @fa-var-glass-citrus; }
.@{fa-css-prefix}-glass-martini:before { content: @fa-var-glass-martini; }
.@{fa-css-prefix}-glass-martini-alt:before { content: @fa-var-glass-martini-alt; }
.@{fa-css-prefix}-glass-whiskey:before { content: @fa-var-glass-whiskey; }
.@{fa-css-prefix}-glass-whiskey-rocks:before { content: @fa-var-glass-whiskey-rocks; }
.@{fa-css-prefix}-glasses:before { content: @fa-var-glasses; }
.@{fa-css-prefix}-glasses-alt:before { content: @fa-var-glasses-alt; }
.@{fa-css-prefix}-glide:before { content: @fa-var-glide; }
.@{fa-css-prefix}-glide-g:before { content: @fa-var-glide-g; }
.@{fa-css-prefix}-globe:before { content: @fa-var-globe; }
.@{fa-css-prefix}-globe-africa:before { content: @fa-var-globe-africa; }
.@{fa-css-prefix}-globe-americas:before { content: @fa-var-globe-americas; }
.@{fa-css-prefix}-globe-asia:before { content: @fa-var-globe-asia; }
.@{fa-css-prefix}-globe-europe:before { content: @fa-var-globe-europe; }
.@{fa-css-prefix}-globe-snow:before { content: @fa-var-globe-snow; }
.@{fa-css-prefix}-globe-stand:before { content: @fa-var-globe-stand; }
.@{fa-css-prefix}-gofore:before { content: @fa-var-gofore; }
.@{fa-css-prefix}-golf-ball:before { content: @fa-var-golf-ball; }
.@{fa-css-prefix}-golf-club:before { content: @fa-var-golf-club; }
.@{fa-css-prefix}-goodreads:before { content: @fa-var-goodreads; }
.@{fa-css-prefix}-goodreads-g:before { content: @fa-var-goodreads-g; }
.@{fa-css-prefix}-google:before { content: @fa-var-google; }
.@{fa-css-prefix}-google-drive:before { content: @fa-var-google-drive; }
.@{fa-css-prefix}-google-play:before { content: @fa-var-google-play; }
.@{fa-css-prefix}-google-plus:before { content: @fa-var-google-plus; }
.@{fa-css-prefix}-google-plus-g:before { content: @fa-var-google-plus-g; }
.@{fa-css-prefix}-google-plus-square:before { content: @fa-var-google-plus-square; }
.@{fa-css-prefix}-google-wallet:before { content: @fa-var-google-wallet; }
.@{fa-css-prefix}-gopuram:before { content: @fa-var-gopuram; }
.@{fa-css-prefix}-graduation-cap:before { content: @fa-var-graduation-cap; }
.@{fa-css-prefix}-gratipay:before { content: @fa-var-gratipay; }
.@{fa-css-prefix}-grav:before { content: @fa-var-grav; }
.@{fa-css-prefix}-greater-than:before { content: @fa-var-greater-than; }
.@{fa-css-prefix}-greater-than-equal:before { content: @fa-var-greater-than-equal; }
.@{fa-css-prefix}-grimace:before { content: @fa-var-grimace; }
.@{fa-css-prefix}-grin:before { content: @fa-var-grin; }
.@{fa-css-prefix}-grin-alt:before { content: @fa-var-grin-alt; }
.@{fa-css-prefix}-grin-beam:before { content: @fa-var-grin-beam; }
.@{fa-css-prefix}-grin-beam-sweat:before { content: @fa-var-grin-beam-sweat; }
.@{fa-css-prefix}-grin-hearts:before { content: @fa-var-grin-hearts; }
.@{fa-css-prefix}-grin-squint:before { content: @fa-var-grin-squint; }
.@{fa-css-prefix}-grin-squint-tears:before { content: @fa-var-grin-squint-tears; }
.@{fa-css-prefix}-grin-stars:before { content: @fa-var-grin-stars; }
.@{fa-css-prefix}-grin-tears:before { content: @fa-var-grin-tears; }
.@{fa-css-prefix}-grin-tongue:before { content: @fa-var-grin-tongue; }
.@{fa-css-prefix}-grin-tongue-squint:before { content: @fa-var-grin-tongue-squint; }
.@{fa-css-prefix}-grin-tongue-wink:before { content: @fa-var-grin-tongue-wink; }
.@{fa-css-prefix}-grin-wink:before { content: @fa-var-grin-wink; }
.@{fa-css-prefix}-grip-horizontal:before { content: @fa-var-grip-horizontal; }
.@{fa-css-prefix}-grip-lines:before { content: @fa-var-grip-lines; }
.@{fa-css-prefix}-grip-lines-vertical:before { content: @fa-var-grip-lines-vertical; }
.@{fa-css-prefix}-grip-vertical:before { content: @fa-var-grip-vertical; }
.@{fa-css-prefix}-gripfire:before { content: @fa-var-gripfire; }
.@{fa-css-prefix}-grunt:before { content: @fa-var-grunt; }
.@{fa-css-prefix}-guitar:before { content: @fa-var-guitar; }
.@{fa-css-prefix}-gulp:before { content: @fa-var-gulp; }
.@{fa-css-prefix}-h-square:before { content: @fa-var-h-square; }
.@{fa-css-prefix}-h1:before { content: @fa-var-h1; }
.@{fa-css-prefix}-h2:before { content: @fa-var-h2; }
.@{fa-css-prefix}-h3:before { content: @fa-var-h3; }
.@{fa-css-prefix}-h4:before { content: @fa-var-h4; }
.@{fa-css-prefix}-hacker-news:before { content: @fa-var-hacker-news; }
.@{fa-css-prefix}-hacker-news-square:before { content: @fa-var-hacker-news-square; }
.@{fa-css-prefix}-hackerrank:before { content: @fa-var-hackerrank; }
.@{fa-css-prefix}-hamburger:before { content: @fa-var-hamburger; }
.@{fa-css-prefix}-hammer:before { content: @fa-var-hammer; }
.@{fa-css-prefix}-hammer-war:before { content: @fa-var-hammer-war; }
.@{fa-css-prefix}-hamsa:before { content: @fa-var-hamsa; }
.@{fa-css-prefix}-hand-heart:before { content: @fa-var-hand-heart; }
.@{fa-css-prefix}-hand-holding:before { content: @fa-var-hand-holding; }
.@{fa-css-prefix}-hand-holding-box:before { content: @fa-var-hand-holding-box; }
.@{fa-css-prefix}-hand-holding-heart:before { content: @fa-var-hand-holding-heart; }
.@{fa-css-prefix}-hand-holding-magic:before { content: @fa-var-hand-holding-magic; }
.@{fa-css-prefix}-hand-holding-seedling:before { content: @fa-var-hand-holding-seedling; }
.@{fa-css-prefix}-hand-holding-usd:before { content: @fa-var-hand-holding-usd; }
.@{fa-css-prefix}-hand-holding-water:before { content: @fa-var-hand-holding-water; }
.@{fa-css-prefix}-hand-lizard:before { content: @fa-var-hand-lizard; }
.@{fa-css-prefix}-hand-middle-finger:before { content: @fa-var-hand-middle-finger; }
.@{fa-css-prefix}-hand-paper:before { content: @fa-var-hand-paper; }
.@{fa-css-prefix}-hand-peace:before { content: @fa-var-hand-peace; }
.@{fa-css-prefix}-hand-point-down:before { content: @fa-var-hand-point-down; }
.@{fa-css-prefix}-hand-point-left:before { content: @fa-var-hand-point-left; }
.@{fa-css-prefix}-hand-point-right:before { content: @fa-var-hand-point-right; }
.@{fa-css-prefix}-hand-point-up:before { content: @fa-var-hand-point-up; }
.@{fa-css-prefix}-hand-pointer:before { content: @fa-var-hand-pointer; }
.@{fa-css-prefix}-hand-receiving:before { content: @fa-var-hand-receiving; }
.@{fa-css-prefix}-hand-rock:before { content: @fa-var-hand-rock; }
.@{fa-css-prefix}-hand-scissors:before { content: @fa-var-hand-scissors; }
.@{fa-css-prefix}-hand-spock:before { content: @fa-var-hand-spock; }
.@{fa-css-prefix}-hands:before { content: @fa-var-hands; }
.@{fa-css-prefix}-hands-heart:before { content: @fa-var-hands-heart; }
.@{fa-css-prefix}-hands-helping:before { content: @fa-var-hands-helping; }
.@{fa-css-prefix}-hands-usd:before { content: @fa-var-hands-usd; }
.@{fa-css-prefix}-handshake:before { content: @fa-var-handshake; }
.@{fa-css-prefix}-handshake-alt:before { content: @fa-var-handshake-alt; }
.@{fa-css-prefix}-hanukiah:before { content: @fa-var-hanukiah; }
.@{fa-css-prefix}-hard-hat:before { content: @fa-var-hard-hat; }
.@{fa-css-prefix}-hashtag:before { content: @fa-var-hashtag; }
.@{fa-css-prefix}-hat-chef:before { content: @fa-var-hat-chef; }
.@{fa-css-prefix}-hat-santa:before { content: @fa-var-hat-santa; }
.@{fa-css-prefix}-hat-winter:before { content: @fa-var-hat-winter; }
.@{fa-css-prefix}-hat-witch:before { content: @fa-var-hat-witch; }
.@{fa-css-prefix}-hat-wizard:before { content: @fa-var-hat-wizard; }
.@{fa-css-prefix}-haykal:before { content: @fa-var-haykal; }
.@{fa-css-prefix}-hdd:before { content: @fa-var-hdd; }
.@{fa-css-prefix}-head-side:before { content: @fa-var-head-side; }
.@{fa-css-prefix}-head-side-brain:before { content: @fa-var-head-side-brain; }
.@{fa-css-prefix}-head-side-medical:before { content: @fa-var-head-side-medical; }
.@{fa-css-prefix}-head-vr:before { content: @fa-var-head-vr; }
.@{fa-css-prefix}-heading:before { content: @fa-var-heading; }
.@{fa-css-prefix}-headphones:before { content: @fa-var-headphones; }
.@{fa-css-prefix}-headphones-alt:before { content: @fa-var-headphones-alt; }
.@{fa-css-prefix}-headset:before { content: @fa-var-headset; }
.@{fa-css-prefix}-heart:before { content: @fa-var-heart; }
.@{fa-css-prefix}-heart-broken:before { content: @fa-var-heart-broken; }
.@{fa-css-prefix}-heart-circle:before { content: @fa-var-heart-circle; }
.@{fa-css-prefix}-heart-rate:before { content: @fa-var-heart-rate; }
.@{fa-css-prefix}-heart-square:before { content: @fa-var-heart-square; }
.@{fa-css-prefix}-heartbeat:before { content: @fa-var-heartbeat; }
.@{fa-css-prefix}-helicopter:before { content: @fa-var-helicopter; }
.@{fa-css-prefix}-helmet-battle:before { content: @fa-var-helmet-battle; }
.@{fa-css-prefix}-hexagon:before { content: @fa-var-hexagon; }
.@{fa-css-prefix}-highlighter:before { content: @fa-var-highlighter; }
.@{fa-css-prefix}-hiking:before { content: @fa-var-hiking; }
.@{fa-css-prefix}-hippo:before { content: @fa-var-hippo; }
.@{fa-css-prefix}-hips:before { content: @fa-var-hips; }
.@{fa-css-prefix}-hire-a-helper:before { content: @fa-var-hire-a-helper; }
.@{fa-css-prefix}-history:before { content: @fa-var-history; }
.@{fa-css-prefix}-hockey-mask:before { content: @fa-var-hockey-mask; }
.@{fa-css-prefix}-hockey-puck:before { content: @fa-var-hockey-puck; }
.@{fa-css-prefix}-hockey-sticks:before { content: @fa-var-hockey-sticks; }
.@{fa-css-prefix}-holly-berry:before { content: @fa-var-holly-berry; }
.@{fa-css-prefix}-home:before { content: @fa-var-home; }
.@{fa-css-prefix}-home-alt:before { content: @fa-var-home-alt; }
.@{fa-css-prefix}-home-heart:before { content: @fa-var-home-heart; }
.@{fa-css-prefix}-home-lg:before { content: @fa-var-home-lg; }
.@{fa-css-prefix}-home-lg-alt:before { content: @fa-var-home-lg-alt; }
.@{fa-css-prefix}-hood-cloak:before { content: @fa-var-hood-cloak; }
.@{fa-css-prefix}-hooli:before { content: @fa-var-hooli; }
.@{fa-css-prefix}-horizontal-rule:before { content: @fa-var-horizontal-rule; }
.@{fa-css-prefix}-hornbill:before { content: @fa-var-hornbill; }
.@{fa-css-prefix}-horse:before { content: @fa-var-horse; }
.@{fa-css-prefix}-horse-head:before { content: @fa-var-horse-head; }
.@{fa-css-prefix}-hospital:before { content: @fa-var-hospital; }
.@{fa-css-prefix}-hospital-alt:before { content: @fa-var-hospital-alt; }
.@{fa-css-prefix}-hospital-symbol:before { content: @fa-var-hospital-symbol; }
.@{fa-css-prefix}-hospital-user:before { content: @fa-var-hospital-user; }
.@{fa-css-prefix}-hospitals:before { content: @fa-var-hospitals; }
.@{fa-css-prefix}-hot-tub:before { content: @fa-var-hot-tub; }
.@{fa-css-prefix}-hotdog:before { content: @fa-var-hotdog; }
.@{fa-css-prefix}-hotel:before { content: @fa-var-hotel; }
.@{fa-css-prefix}-hotjar:before { content: @fa-var-hotjar; }
.@{fa-css-prefix}-hourglass:before { content: @fa-var-hourglass; }
.@{fa-css-prefix}-hourglass-end:before { content: @fa-var-hourglass-end; }
.@{fa-css-prefix}-hourglass-half:before { content: @fa-var-hourglass-half; }
.@{fa-css-prefix}-hourglass-start:before { content: @fa-var-hourglass-start; }
.@{fa-css-prefix}-house-damage:before { content: @fa-var-house-damage; }
.@{fa-css-prefix}-house-flood:before { content: @fa-var-house-flood; }
.@{fa-css-prefix}-houzz:before { content: @fa-var-houzz; }
.@{fa-css-prefix}-hryvnia:before { content: @fa-var-hryvnia; }
.@{fa-css-prefix}-html5:before { content: @fa-var-html5; }
.@{fa-css-prefix}-hubspot:before { content: @fa-var-hubspot; }
.@{fa-css-prefix}-humidity:before { content: @fa-var-humidity; }
.@{fa-css-prefix}-hurricane:before { content: @fa-var-hurricane; }
.@{fa-css-prefix}-i-cursor:before { content: @fa-var-i-cursor; }
.@{fa-css-prefix}-ice-cream:before { content: @fa-var-ice-cream; }
.@{fa-css-prefix}-ice-skate:before { content: @fa-var-ice-skate; }
.@{fa-css-prefix}-icicles:before { content: @fa-var-icicles; }
.@{fa-css-prefix}-icons:before { content: @fa-var-icons; }
.@{fa-css-prefix}-icons-alt:before { content: @fa-var-icons-alt; }
.@{fa-css-prefix}-id-badge:before { content: @fa-var-id-badge; }
.@{fa-css-prefix}-id-card:before { content: @fa-var-id-card; }
.@{fa-css-prefix}-id-card-alt:before { content: @fa-var-id-card-alt; }
.@{fa-css-prefix}-igloo:before { content: @fa-var-igloo; }
.@{fa-css-prefix}-image:before { content: @fa-var-image; }
.@{fa-css-prefix}-images:before { content: @fa-var-images; }
.@{fa-css-prefix}-imdb:before { content: @fa-var-imdb; }
.@{fa-css-prefix}-inbox:before { content: @fa-var-inbox; }
.@{fa-css-prefix}-inbox-in:before { content: @fa-var-inbox-in; }
.@{fa-css-prefix}-inbox-out:before { content: @fa-var-inbox-out; }
.@{fa-css-prefix}-indent:before { content: @fa-var-indent; }
.@{fa-css-prefix}-industry:before { content: @fa-var-industry; }
.@{fa-css-prefix}-industry-alt:before { content: @fa-var-industry-alt; }
.@{fa-css-prefix}-infinity:before { content: @fa-var-infinity; }
.@{fa-css-prefix}-info:before { content: @fa-var-info; }
.@{fa-css-prefix}-info-circle:before { content: @fa-var-info-circle; }
.@{fa-css-prefix}-info-square:before { content: @fa-var-info-square; }
.@{fa-css-prefix}-inhaler:before { content: @fa-var-inhaler; }
.@{fa-css-prefix}-instagram:before { content: @fa-var-instagram; }
.@{fa-css-prefix}-integral:before { content: @fa-var-integral; }
.@{fa-css-prefix}-intercom:before { content: @fa-var-intercom; }
.@{fa-css-prefix}-internet-explorer:before { content: @fa-var-internet-explorer; }
.@{fa-css-prefix}-intersection:before { content: @fa-var-intersection; }
.@{fa-css-prefix}-inventory:before { content: @fa-var-inventory; }
.@{fa-css-prefix}-invision:before { content: @fa-var-invision; }
.@{fa-css-prefix}-ioxhost:before { content: @fa-var-ioxhost; }
.@{fa-css-prefix}-island-tropical:before { content: @fa-var-island-tropical; }
.@{fa-css-prefix}-italic:before { content: @fa-var-italic; }
.@{fa-css-prefix}-itch-io:before { content: @fa-var-itch-io; }
.@{fa-css-prefix}-itunes:before { content: @fa-var-itunes; }
.@{fa-css-prefix}-itunes-note:before { content: @fa-var-itunes-note; }
.@{fa-css-prefix}-jack-o-lantern:before { content: @fa-var-jack-o-lantern; }
.@{fa-css-prefix}-java:before { content: @fa-var-java; }
.@{fa-css-prefix}-jedi:before { content: @fa-var-jedi; }
.@{fa-css-prefix}-jedi-order:before { content: @fa-var-jedi-order; }
.@{fa-css-prefix}-jenkins:before { content: @fa-var-jenkins; }
.@{fa-css-prefix}-jira:before { content: @fa-var-jira; }
.@{fa-css-prefix}-joget:before { content: @fa-var-joget; }
.@{fa-css-prefix}-joint:before { content: @fa-var-joint; }
.@{fa-css-prefix}-joomla:before { content: @fa-var-joomla; }
.@{fa-css-prefix}-journal-whills:before { content: @fa-var-journal-whills; }
.@{fa-css-prefix}-js:before { content: @fa-var-js; }
.@{fa-css-prefix}-js-square:before { content: @fa-var-js-square; }
.@{fa-css-prefix}-jsfiddle:before { content: @fa-var-jsfiddle; }
.@{fa-css-prefix}-kaaba:before { content: @fa-var-kaaba; }
.@{fa-css-prefix}-kaggle:before { content: @fa-var-kaggle; }
.@{fa-css-prefix}-kerning:before { content: @fa-var-kerning; }
.@{fa-css-prefix}-key:before { content: @fa-var-key; }
.@{fa-css-prefix}-key-skeleton:before { content: @fa-var-key-skeleton; }
.@{fa-css-prefix}-keybase:before { content: @fa-var-keybase; }
.@{fa-css-prefix}-keyboard:before { content: @fa-var-keyboard; }
.@{fa-css-prefix}-keycdn:before { content: @fa-var-keycdn; }
.@{fa-css-prefix}-keynote:before { content: @fa-var-keynote; }
.@{fa-css-prefix}-khanda:before { content: @fa-var-khanda; }
.@{fa-css-prefix}-kickstarter:before { content: @fa-var-kickstarter; }
.@{fa-css-prefix}-kickstarter-k:before { content: @fa-var-kickstarter-k; }
.@{fa-css-prefix}-kidneys:before { content: @fa-var-kidneys; }
.@{fa-css-prefix}-kiss:before { content: @fa-var-kiss; }
.@{fa-css-prefix}-kiss-beam:before { content: @fa-var-kiss-beam; }
.@{fa-css-prefix}-kiss-wink-heart:before { content: @fa-var-kiss-wink-heart; }
.@{fa-css-prefix}-kite:before { content: @fa-var-kite; }
.@{fa-css-prefix}-kiwi-bird:before { content: @fa-var-kiwi-bird; }
.@{fa-css-prefix}-knife-kitchen:before { content: @fa-var-knife-kitchen; }
.@{fa-css-prefix}-korvue:before { content: @fa-var-korvue; }
.@{fa-css-prefix}-lambda:before { content: @fa-var-lambda; }
.@{fa-css-prefix}-lamp:before { content: @fa-var-lamp; }
.@{fa-css-prefix}-landmark:before { content: @fa-var-landmark; }
.@{fa-css-prefix}-landmark-alt:before { content: @fa-var-landmark-alt; }
.@{fa-css-prefix}-language:before { content: @fa-var-language; }
.@{fa-css-prefix}-laptop:before { content: @fa-var-laptop; }
.@{fa-css-prefix}-laptop-code:before { content: @fa-var-laptop-code; }
.@{fa-css-prefix}-laptop-medical:before { content: @fa-var-laptop-medical; }
.@{fa-css-prefix}-laravel:before { content: @fa-var-laravel; }
.@{fa-css-prefix}-lastfm:before { content: @fa-var-lastfm; }
.@{fa-css-prefix}-lastfm-square:before { content: @fa-var-lastfm-square; }
.@{fa-css-prefix}-laugh:before { content: @fa-var-laugh; }
.@{fa-css-prefix}-laugh-beam:before { content: @fa-var-laugh-beam; }
.@{fa-css-prefix}-laugh-squint:before { content: @fa-var-laugh-squint; }
.@{fa-css-prefix}-laugh-wink:before { content: @fa-var-laugh-wink; }
.@{fa-css-prefix}-layer-group:before { content: @fa-var-layer-group; }
.@{fa-css-prefix}-layer-minus:before { content: @fa-var-layer-minus; }
.@{fa-css-prefix}-layer-plus:before { content: @fa-var-layer-plus; }
.@{fa-css-prefix}-leaf:before { content: @fa-var-leaf; }
.@{fa-css-prefix}-leaf-heart:before { content: @fa-var-leaf-heart; }
.@{fa-css-prefix}-leaf-maple:before { content: @fa-var-leaf-maple; }
.@{fa-css-prefix}-leaf-oak:before { content: @fa-var-leaf-oak; }
.@{fa-css-prefix}-leanpub:before { content: @fa-var-leanpub; }
.@{fa-css-prefix}-lemon:before { content: @fa-var-lemon; }
.@{fa-css-prefix}-less:before { content: @fa-var-less; }
.@{fa-css-prefix}-less-than:before { content: @fa-var-less-than; }
.@{fa-css-prefix}-less-than-equal:before { content: @fa-var-less-than-equal; }
.@{fa-css-prefix}-level-down:before { content: @fa-var-level-down; }
.@{fa-css-prefix}-level-down-alt:before { content: @fa-var-level-down-alt; }
.@{fa-css-prefix}-level-up:before { content: @fa-var-level-up; }
.@{fa-css-prefix}-level-up-alt:before { content: @fa-var-level-up-alt; }
.@{fa-css-prefix}-life-ring:before { content: @fa-var-life-ring; }
.@{fa-css-prefix}-lightbulb:before { content: @fa-var-lightbulb; }
.@{fa-css-prefix}-lightbulb-dollar:before { content: @fa-var-lightbulb-dollar; }
.@{fa-css-prefix}-lightbulb-exclamation:before { content: @fa-var-lightbulb-exclamation; }
.@{fa-css-prefix}-lightbulb-on:before { content: @fa-var-lightbulb-on; }
.@{fa-css-prefix}-lightbulb-slash:before { content: @fa-var-lightbulb-slash; }
.@{fa-css-prefix}-lights-holiday:before { content: @fa-var-lights-holiday; }
.@{fa-css-prefix}-line:before { content: @fa-var-line; }
.@{fa-css-prefix}-line-columns:before { content: @fa-var-line-columns; }
.@{fa-css-prefix}-line-height:before { content: @fa-var-line-height; }
.@{fa-css-prefix}-link:before { content: @fa-var-link; }
.@{fa-css-prefix}-linkedin:before { content: @fa-var-linkedin; }
.@{fa-css-prefix}-linkedin-in:before { content: @fa-var-linkedin-in; }
.@{fa-css-prefix}-linode:before { content: @fa-var-linode; }
.@{fa-css-prefix}-linux:before { content: @fa-var-linux; }
.@{fa-css-prefix}-lips:before { content: @fa-var-lips; }
.@{fa-css-prefix}-lira-sign:before { content: @fa-var-lira-sign; }
.@{fa-css-prefix}-list:before { content: @fa-var-list; }
.@{fa-css-prefix}-list-alt:before { content: @fa-var-list-alt; }
.@{fa-css-prefix}-list-ol:before { content: @fa-var-list-ol; }
.@{fa-css-prefix}-list-ul:before { content: @fa-var-list-ul; }
.@{fa-css-prefix}-location:before { content: @fa-var-location; }
.@{fa-css-prefix}-location-arrow:before { content: @fa-var-location-arrow; }
.@{fa-css-prefix}-location-circle:before { content: @fa-var-location-circle; }
.@{fa-css-prefix}-location-slash:before { content: @fa-var-location-slash; }
.@{fa-css-prefix}-lock:before { content: @fa-var-lock; }
.@{fa-css-prefix}-lock-alt:before { content: @fa-var-lock-alt; }
.@{fa-css-prefix}-lock-open:before { content: @fa-var-lock-open; }
.@{fa-css-prefix}-lock-open-alt:before { content: @fa-var-lock-open-alt; }
.@{fa-css-prefix}-long-arrow-alt-down:before { content: @fa-var-long-arrow-alt-down; }
.@{fa-css-prefix}-long-arrow-alt-left:before { content: @fa-var-long-arrow-alt-left; }
.@{fa-css-prefix}-long-arrow-alt-right:before { content: @fa-var-long-arrow-alt-right; }
.@{fa-css-prefix}-long-arrow-alt-up:before { content: @fa-var-long-arrow-alt-up; }
.@{fa-css-prefix}-long-arrow-down:before { content: @fa-var-long-arrow-down; }
.@{fa-css-prefix}-long-arrow-left:before { content: @fa-var-long-arrow-left; }
.@{fa-css-prefix}-long-arrow-right:before { content: @fa-var-long-arrow-right; }
.@{fa-css-prefix}-long-arrow-up:before { content: @fa-var-long-arrow-up; }
.@{fa-css-prefix}-loveseat:before { content: @fa-var-loveseat; }
.@{fa-css-prefix}-low-vision:before { content: @fa-var-low-vision; }
.@{fa-css-prefix}-luchador:before { content: @fa-var-luchador; }
.@{fa-css-prefix}-luggage-cart:before { content: @fa-var-luggage-cart; }
.@{fa-css-prefix}-lungs:before { content: @fa-var-lungs; }
.@{fa-css-prefix}-lyft:before { content: @fa-var-lyft; }
.@{fa-css-prefix}-mace:before { content: @fa-var-mace; }
.@{fa-css-prefix}-magento:before { content: @fa-var-magento; }
.@{fa-css-prefix}-magic:before { content: @fa-var-magic; }
.@{fa-css-prefix}-magnet:before { content: @fa-var-magnet; }
.@{fa-css-prefix}-mail-bulk:before { content: @fa-var-mail-bulk; }
.@{fa-css-prefix}-mailbox:before { content: @fa-var-mailbox; }
.@{fa-css-prefix}-mailchimp:before { content: @fa-var-mailchimp; }
.@{fa-css-prefix}-male:before { content: @fa-var-male; }
.@{fa-css-prefix}-mandalorian:before { content: @fa-var-mandalorian; }
.@{fa-css-prefix}-mandolin:before { content: @fa-var-mandolin; }
.@{fa-css-prefix}-map:before { content: @fa-var-map; }
.@{fa-css-prefix}-map-marked:before { content: @fa-var-map-marked; }
.@{fa-css-prefix}-map-marked-alt:before { content: @fa-var-map-marked-alt; }
.@{fa-css-prefix}-map-marker:before { content: @fa-var-map-marker; }
.@{fa-css-prefix}-map-marker-alt:before { content: @fa-var-map-marker-alt; }
.@{fa-css-prefix}-map-marker-alt-slash:before { content: @fa-var-map-marker-alt-slash; }
.@{fa-css-prefix}-map-marker-check:before { content: @fa-var-map-marker-check; }
.@{fa-css-prefix}-map-marker-edit:before { content: @fa-var-map-marker-edit; }
.@{fa-css-prefix}-map-marker-exclamation:before { content: @fa-var-map-marker-exclamation; }
.@{fa-css-prefix}-map-marker-minus:before { content: @fa-var-map-marker-minus; }
.@{fa-css-prefix}-map-marker-plus:before { content: @fa-var-map-marker-plus; }
.@{fa-css-prefix}-map-marker-question:before { content: @fa-var-map-marker-question; }
.@{fa-css-prefix}-map-marker-slash:before { content: @fa-var-map-marker-slash; }
.@{fa-css-prefix}-map-marker-smile:before { content: @fa-var-map-marker-smile; }
.@{fa-css-prefix}-map-marker-times:before { content: @fa-var-map-marker-times; }
.@{fa-css-prefix}-map-pin:before { content: @fa-var-map-pin; }
.@{fa-css-prefix}-map-signs:before { content: @fa-var-map-signs; }
.@{fa-css-prefix}-markdown:before { content: @fa-var-markdown; }
.@{fa-css-prefix}-marker:before { content: @fa-var-marker; }
.@{fa-css-prefix}-mars:before { content: @fa-var-mars; }
.@{fa-css-prefix}-mars-double:before { content: @fa-var-mars-double; }
.@{fa-css-prefix}-mars-stroke:before { content: @fa-var-mars-stroke; }
.@{fa-css-prefix}-mars-stroke-h:before { content: @fa-var-mars-stroke-h; }
.@{fa-css-prefix}-mars-stroke-v:before { content: @fa-var-mars-stroke-v; }
.@{fa-css-prefix}-mask:before { content: @fa-var-mask; }
.@{fa-css-prefix}-mastodon:before { content: @fa-var-mastodon; }
.@{fa-css-prefix}-maxcdn:before { content: @fa-var-maxcdn; }
.@{fa-css-prefix}-meat:before { content: @fa-var-meat; }
.@{fa-css-prefix}-medal:before { content: @fa-var-medal; }
.@{fa-css-prefix}-medapps:before { content: @fa-var-medapps; }
.@{fa-css-prefix}-medium:before { content: @fa-var-medium; }
.@{fa-css-prefix}-medium-m:before { content: @fa-var-medium-m; }
.@{fa-css-prefix}-medkit:before { content: @fa-var-medkit; }
.@{fa-css-prefix}-medrt:before { content: @fa-var-medrt; }
.@{fa-css-prefix}-meetup:before { content: @fa-var-meetup; }
.@{fa-css-prefix}-megaphone:before { content: @fa-var-megaphone; }
.@{fa-css-prefix}-megaport:before { content: @fa-var-megaport; }
.@{fa-css-prefix}-meh:before { content: @fa-var-meh; }
.@{fa-css-prefix}-meh-blank:before { content: @fa-var-meh-blank; }
.@{fa-css-prefix}-meh-rolling-eyes:before { content: @fa-var-meh-rolling-eyes; }
.@{fa-css-prefix}-memory:before { content: @fa-var-memory; }
.@{fa-css-prefix}-mendeley:before { content: @fa-var-mendeley; }
.@{fa-css-prefix}-menorah:before { content: @fa-var-menorah; }
.@{fa-css-prefix}-mercury:before { content: @fa-var-mercury; }
.@{fa-css-prefix}-meteor:before { content: @fa-var-meteor; }
.@{fa-css-prefix}-microchip:before { content: @fa-var-microchip; }
.@{fa-css-prefix}-microphone:before { content: @fa-var-microphone; }
.@{fa-css-prefix}-microphone-alt:before { content: @fa-var-microphone-alt; }
.@{fa-css-prefix}-microphone-alt-slash:before { content: @fa-var-microphone-alt-slash; }
.@{fa-css-prefix}-microphone-slash:before { content: @fa-var-microphone-slash; }
.@{fa-css-prefix}-microscope:before { content: @fa-var-microscope; }
.@{fa-css-prefix}-microsoft:before { content: @fa-var-microsoft; }
.@{fa-css-prefix}-mind-share:before { content: @fa-var-mind-share; }
.@{fa-css-prefix}-minus:before { content: @fa-var-minus; }
.@{fa-css-prefix}-minus-circle:before { content: @fa-var-minus-circle; }
.@{fa-css-prefix}-minus-hexagon:before { content: @fa-var-minus-hexagon; }
.@{fa-css-prefix}-minus-octagon:before { content: @fa-var-minus-octagon; }
.@{fa-css-prefix}-minus-square:before { content: @fa-var-minus-square; }
.@{fa-css-prefix}-mistletoe:before { content: @fa-var-mistletoe; }
.@{fa-css-prefix}-mitten:before { content: @fa-var-mitten; }
.@{fa-css-prefix}-mix:before { content: @fa-var-mix; }
.@{fa-css-prefix}-mixcloud:before { content: @fa-var-mixcloud; }
.@{fa-css-prefix}-mizuni:before { content: @fa-var-mizuni; }
.@{fa-css-prefix}-mobile:before { content: @fa-var-mobile; }
.@{fa-css-prefix}-mobile-alt:before { content: @fa-var-mobile-alt; }
.@{fa-css-prefix}-mobile-android:before { content: @fa-var-mobile-android; }
.@{fa-css-prefix}-mobile-android-alt:before { content: @fa-var-mobile-android-alt; }
.@{fa-css-prefix}-modx:before { content: @fa-var-modx; }
.@{fa-css-prefix}-monero:before { content: @fa-var-monero; }
.@{fa-css-prefix}-money-bill:before { content: @fa-var-money-bill; }
.@{fa-css-prefix}-money-bill-alt:before { content: @fa-var-money-bill-alt; }
.@{fa-css-prefix}-money-bill-wave:before { content: @fa-var-money-bill-wave; }
.@{fa-css-prefix}-money-bill-wave-alt:before { content: @fa-var-money-bill-wave-alt; }
.@{fa-css-prefix}-money-check:before { content: @fa-var-money-check; }
.@{fa-css-prefix}-money-check-alt:before { content: @fa-var-money-check-alt; }
.@{fa-css-prefix}-money-check-edit:before { content: @fa-var-money-check-edit; }
.@{fa-css-prefix}-money-check-edit-alt:before { content: @fa-var-money-check-edit-alt; }
.@{fa-css-prefix}-monitor-heart-rate:before { content: @fa-var-monitor-heart-rate; }
.@{fa-css-prefix}-monkey:before { content: @fa-var-monkey; }
.@{fa-css-prefix}-monument:before { content: @fa-var-monument; }
.@{fa-css-prefix}-moon:before { content: @fa-var-moon; }
.@{fa-css-prefix}-moon-cloud:before { content: @fa-var-moon-cloud; }
.@{fa-css-prefix}-moon-stars:before { content: @fa-var-moon-stars; }
.@{fa-css-prefix}-mortar-pestle:before { content: @fa-var-mortar-pestle; }
.@{fa-css-prefix}-mosque:before { content: @fa-var-mosque; }
.@{fa-css-prefix}-motorcycle:before { content: @fa-var-motorcycle; }
.@{fa-css-prefix}-mountain:before { content: @fa-var-mountain; }
.@{fa-css-prefix}-mountains:before { content: @fa-var-mountains; }
.@{fa-css-prefix}-mouse-pointer:before { content: @fa-var-mouse-pointer; }
.@{fa-css-prefix}-mug:before { content: @fa-var-mug; }
.@{fa-css-prefix}-mug-hot:before { content: @fa-var-mug-hot; }
.@{fa-css-prefix}-mug-marshmallows:before { content: @fa-var-mug-marshmallows; }
.@{fa-css-prefix}-mug-tea:before { content: @fa-var-mug-tea; }
.@{fa-css-prefix}-music:before { content: @fa-var-music; }
.@{fa-css-prefix}-napster:before { content: @fa-var-napster; }
.@{fa-css-prefix}-narwhal:before { content: @fa-var-narwhal; }
.@{fa-css-prefix}-neos:before { content: @fa-var-neos; }
.@{fa-css-prefix}-network-wired:before { content: @fa-var-network-wired; }
.@{fa-css-prefix}-neuter:before { content: @fa-var-neuter; }
.@{fa-css-prefix}-newspaper:before { content: @fa-var-newspaper; }
.@{fa-css-prefix}-nimblr:before { content: @fa-var-nimblr; }
.@{fa-css-prefix}-node:before { content: @fa-var-node; }
.@{fa-css-prefix}-node-js:before { content: @fa-var-node-js; }
.@{fa-css-prefix}-not-equal:before { content: @fa-var-not-equal; }
.@{fa-css-prefix}-notes-medical:before { content: @fa-var-notes-medical; }
.@{fa-css-prefix}-npm:before { content: @fa-var-npm; }
.@{fa-css-prefix}-ns8:before { content: @fa-var-ns8; }
.@{fa-css-prefix}-nutritionix:before { content: @fa-var-nutritionix; }
.@{fa-css-prefix}-object-group:before { content: @fa-var-object-group; }
.@{fa-css-prefix}-object-ungroup:before { content: @fa-var-object-ungroup; }
.@{fa-css-prefix}-octagon:before { content: @fa-var-octagon; }
.@{fa-css-prefix}-odnoklassniki:before { content: @fa-var-odnoklassniki; }
.@{fa-css-prefix}-odnoklassniki-square:before { content: @fa-var-odnoklassniki-square; }
.@{fa-css-prefix}-oil-can:before { content: @fa-var-oil-can; }
.@{fa-css-prefix}-oil-temp:before { content: @fa-var-oil-temp; }
.@{fa-css-prefix}-old-republic:before { content: @fa-var-old-republic; }
.@{fa-css-prefix}-om:before { content: @fa-var-om; }
.@{fa-css-prefix}-omega:before { content: @fa-var-omega; }
.@{fa-css-prefix}-opencart:before { content: @fa-var-opencart; }
.@{fa-css-prefix}-openid:before { content: @fa-var-openid; }
.@{fa-css-prefix}-opera:before { content: @fa-var-opera; }
.@{fa-css-prefix}-optin-monster:before { content: @fa-var-optin-monster; }
.@{fa-css-prefix}-ornament:before { content: @fa-var-ornament; }
.@{fa-css-prefix}-osi:before { content: @fa-var-osi; }
.@{fa-css-prefix}-otter:before { content: @fa-var-otter; }
.@{fa-css-prefix}-outdent:before { content: @fa-var-outdent; }
.@{fa-css-prefix}-overline:before { content: @fa-var-overline; }
.@{fa-css-prefix}-page-break:before { content: @fa-var-page-break; }
.@{fa-css-prefix}-page4:before { content: @fa-var-page4; }
.@{fa-css-prefix}-pagelines:before { content: @fa-var-pagelines; }
.@{fa-css-prefix}-pager:before { content: @fa-var-pager; }
.@{fa-css-prefix}-paint-brush:before { content: @fa-var-paint-brush; }
.@{fa-css-prefix}-paint-brush-alt:before { content: @fa-var-paint-brush-alt; }
.@{fa-css-prefix}-paint-roller:before { content: @fa-var-paint-roller; }
.@{fa-css-prefix}-palette:before { content: @fa-var-palette; }
.@{fa-css-prefix}-palfed:before { content: @fa-var-palfed; }
.@{fa-css-prefix}-pallet:before { content: @fa-var-pallet; }
.@{fa-css-prefix}-pallet-alt:before { content: @fa-var-pallet-alt; }
.@{fa-css-prefix}-paper-plane:before { content: @fa-var-paper-plane; }
.@{fa-css-prefix}-paperclip:before { content: @fa-var-paperclip; }
.@{fa-css-prefix}-parachute-box:before { content: @fa-var-parachute-box; }
.@{fa-css-prefix}-paragraph:before { content: @fa-var-paragraph; }
.@{fa-css-prefix}-paragraph-rtl:before { content: @fa-var-paragraph-rtl; }
.@{fa-css-prefix}-parking:before { content: @fa-var-parking; }
.@{fa-css-prefix}-parking-circle:before { content: @fa-var-parking-circle; }
.@{fa-css-prefix}-parking-circle-slash:before { content: @fa-var-parking-circle-slash; }
.@{fa-css-prefix}-parking-slash:before { content: @fa-var-parking-slash; }
.@{fa-css-prefix}-passport:before { content: @fa-var-passport; }
.@{fa-css-prefix}-pastafarianism:before { content: @fa-var-pastafarianism; }
.@{fa-css-prefix}-paste:before { content: @fa-var-paste; }
.@{fa-css-prefix}-patreon:before { content: @fa-var-patreon; }
.@{fa-css-prefix}-pause:before { content: @fa-var-pause; }
.@{fa-css-prefix}-pause-circle:before { content: @fa-var-pause-circle; }
.@{fa-css-prefix}-paw:before { content: @fa-var-paw; }
.@{fa-css-prefix}-paw-alt:before { content: @fa-var-paw-alt; }
.@{fa-css-prefix}-paw-claws:before { content: @fa-var-paw-claws; }
.@{fa-css-prefix}-paypal:before { content: @fa-var-paypal; }
.@{fa-css-prefix}-peace:before { content: @fa-var-peace; }
.@{fa-css-prefix}-pegasus:before { content: @fa-var-pegasus; }
.@{fa-css-prefix}-pen:before { content: @fa-var-pen; }
.@{fa-css-prefix}-pen-alt:before { content: @fa-var-pen-alt; }
.@{fa-css-prefix}-pen-fancy:before { content: @fa-var-pen-fancy; }
.@{fa-css-prefix}-pen-nib:before { content: @fa-var-pen-nib; }
.@{fa-css-prefix}-pen-square:before { content: @fa-var-pen-square; }
.@{fa-css-prefix}-pencil:before { content: @fa-var-pencil; }
.@{fa-css-prefix}-pencil-alt:before { content: @fa-var-pencil-alt; }
.@{fa-css-prefix}-pencil-paintbrush:before { content: @fa-var-pencil-paintbrush; }
.@{fa-css-prefix}-pencil-ruler:before { content: @fa-var-pencil-ruler; }
.@{fa-css-prefix}-pennant:before { content: @fa-var-pennant; }
.@{fa-css-prefix}-penny-arcade:before { content: @fa-var-penny-arcade; }
.@{fa-css-prefix}-people-carry:before { content: @fa-var-people-carry; }
.@{fa-css-prefix}-pepper-hot:before { content: @fa-var-pepper-hot; }
.@{fa-css-prefix}-percent:before { content: @fa-var-percent; }
.@{fa-css-prefix}-percentage:before { content: @fa-var-percentage; }
.@{fa-css-prefix}-periscope:before { content: @fa-var-periscope; }
.@{fa-css-prefix}-person-booth:before { content: @fa-var-person-booth; }
.@{fa-css-prefix}-person-carry:before { content: @fa-var-person-carry; }
.@{fa-css-prefix}-person-dolly:before { content: @fa-var-person-dolly; }
.@{fa-css-prefix}-person-dolly-empty:before { content: @fa-var-person-dolly-empty; }
.@{fa-css-prefix}-person-sign:before { content: @fa-var-person-sign; }
.@{fa-css-prefix}-phabricator:before { content: @fa-var-phabricator; }
.@{fa-css-prefix}-phoenix-framework:before { content: @fa-var-phoenix-framework; }
.@{fa-css-prefix}-phoenix-squadron:before { content: @fa-var-phoenix-squadron; }
.@{fa-css-prefix}-phone:before { content: @fa-var-phone; }
.@{fa-css-prefix}-phone-alt:before { content: @fa-var-phone-alt; }
.@{fa-css-prefix}-phone-laptop:before { content: @fa-var-phone-laptop; }
.@{fa-css-prefix}-phone-office:before { content: @fa-var-phone-office; }
.@{fa-css-prefix}-phone-plus:before { content: @fa-var-phone-plus; }
.@{fa-css-prefix}-phone-slash:before { content: @fa-var-phone-slash; }
.@{fa-css-prefix}-phone-square:before { content: @fa-var-phone-square; }
.@{fa-css-prefix}-phone-square-alt:before { content: @fa-var-phone-square-alt; }
.@{fa-css-prefix}-phone-volume:before { content: @fa-var-phone-volume; }
.@{fa-css-prefix}-photo-video:before { content: @fa-var-photo-video; }
.@{fa-css-prefix}-php:before { content: @fa-var-php; }
.@{fa-css-prefix}-pi:before { content: @fa-var-pi; }
.@{fa-css-prefix}-pie:before { content: @fa-var-pie; }
.@{fa-css-prefix}-pied-piper:before { content: @fa-var-pied-piper; }
.@{fa-css-prefix}-pied-piper-alt:before { content: @fa-var-pied-piper-alt; }
.@{fa-css-prefix}-pied-piper-hat:before { content: @fa-var-pied-piper-hat; }
.@{fa-css-prefix}-pied-piper-pp:before { content: @fa-var-pied-piper-pp; }
.@{fa-css-prefix}-pig:before { content: @fa-var-pig; }
.@{fa-css-prefix}-piggy-bank:before { content: @fa-var-piggy-bank; }
.@{fa-css-prefix}-pills:before { content: @fa-var-pills; }
.@{fa-css-prefix}-pinterest:before { content: @fa-var-pinterest; }
.@{fa-css-prefix}-pinterest-p:before { content: @fa-var-pinterest-p; }
.@{fa-css-prefix}-pinterest-square:before { content: @fa-var-pinterest-square; }
.@{fa-css-prefix}-pizza:before { content: @fa-var-pizza; }
.@{fa-css-prefix}-pizza-slice:before { content: @fa-var-pizza-slice; }
.@{fa-css-prefix}-place-of-worship:before { content: @fa-var-place-of-worship; }
.@{fa-css-prefix}-plane:before { content: @fa-var-plane; }
.@{fa-css-prefix}-plane-alt:before { content: @fa-var-plane-alt; }
.@{fa-css-prefix}-plane-arrival:before { content: @fa-var-plane-arrival; }
.@{fa-css-prefix}-plane-departure:before { content: @fa-var-plane-departure; }
.@{fa-css-prefix}-play:before { content: @fa-var-play; }
.@{fa-css-prefix}-play-circle:before { content: @fa-var-play-circle; }
.@{fa-css-prefix}-playstation:before { content: @fa-var-playstation; }
.@{fa-css-prefix}-plug:before { content: @fa-var-plug; }
.@{fa-css-prefix}-plus:before { content: @fa-var-plus; }
.@{fa-css-prefix}-plus-circle:before { content: @fa-var-plus-circle; }
.@{fa-css-prefix}-plus-hexagon:before { content: @fa-var-plus-hexagon; }
.@{fa-css-prefix}-plus-octagon:before { content: @fa-var-plus-octagon; }
.@{fa-css-prefix}-plus-square:before { content: @fa-var-plus-square; }
.@{fa-css-prefix}-podcast:before { content: @fa-var-podcast; }
.@{fa-css-prefix}-podium:before { content: @fa-var-podium; }
.@{fa-css-prefix}-podium-star:before { content: @fa-var-podium-star; }
.@{fa-css-prefix}-poll:before { content: @fa-var-poll; }
.@{fa-css-prefix}-poll-h:before { content: @fa-var-poll-h; }
.@{fa-css-prefix}-poll-people:before { content: @fa-var-poll-people; }
.@{fa-css-prefix}-poo:before { content: @fa-var-poo; }
.@{fa-css-prefix}-poo-storm:before { content: @fa-var-poo-storm; }
.@{fa-css-prefix}-poop:before { content: @fa-var-poop; }
.@{fa-css-prefix}-popcorn:before { content: @fa-var-popcorn; }
.@{fa-css-prefix}-portrait:before { content: @fa-var-portrait; }
.@{fa-css-prefix}-pound-sign:before { content: @fa-var-pound-sign; }
.@{fa-css-prefix}-power-off:before { content: @fa-var-power-off; }
.@{fa-css-prefix}-pray:before { content: @fa-var-pray; }
.@{fa-css-prefix}-praying-hands:before { content: @fa-var-praying-hands; }
.@{fa-css-prefix}-prescription:before { content: @fa-var-prescription; }
.@{fa-css-prefix}-prescription-bottle:before { content: @fa-var-prescription-bottle; }
.@{fa-css-prefix}-prescription-bottle-alt:before { content: @fa-var-prescription-bottle-alt; }
.@{fa-css-prefix}-presentation:before { content: @fa-var-presentation; }
.@{fa-css-prefix}-print:before { content: @fa-var-print; }
.@{fa-css-prefix}-print-search:before { content: @fa-var-print-search; }
.@{fa-css-prefix}-print-slash:before { content: @fa-var-print-slash; }
.@{fa-css-prefix}-procedures:before { content: @fa-var-procedures; }
.@{fa-css-prefix}-product-hunt:before { content: @fa-var-product-hunt; }
.@{fa-css-prefix}-project-diagram:before { content: @fa-var-project-diagram; }
.@{fa-css-prefix}-pumpkin:before { content: @fa-var-pumpkin; }
.@{fa-css-prefix}-pushed:before { content: @fa-var-pushed; }
.@{fa-css-prefix}-puzzle-piece:before { content: @fa-var-puzzle-piece; }
.@{fa-css-prefix}-python:before { content: @fa-var-python; }
.@{fa-css-prefix}-qq:before { content: @fa-var-qq; }
.@{fa-css-prefix}-qrcode:before { content: @fa-var-qrcode; }
.@{fa-css-prefix}-question:before { content: @fa-var-question; }
.@{fa-css-prefix}-question-circle:before { content: @fa-var-question-circle; }
.@{fa-css-prefix}-question-square:before { content: @fa-var-question-square; }
.@{fa-css-prefix}-quidditch:before { content: @fa-var-quidditch; }
.@{fa-css-prefix}-quinscape:before { content: @fa-var-quinscape; }
.@{fa-css-prefix}-quora:before { content: @fa-var-quora; }
.@{fa-css-prefix}-quote-left:before { content: @fa-var-quote-left; }
.@{fa-css-prefix}-quote-right:before { content: @fa-var-quote-right; }
.@{fa-css-prefix}-quran:before { content: @fa-var-quran; }
.@{fa-css-prefix}-r-project:before { content: @fa-var-r-project; }
.@{fa-css-prefix}-rabbit:before { content: @fa-var-rabbit; }
.@{fa-css-prefix}-rabbit-fast:before { content: @fa-var-rabbit-fast; }
.@{fa-css-prefix}-racquet:before { content: @fa-var-racquet; }
.@{fa-css-prefix}-radiation:before { content: @fa-var-radiation; }
.@{fa-css-prefix}-radiation-alt:before { content: @fa-var-radiation-alt; }
.@{fa-css-prefix}-rainbow:before { content: @fa-var-rainbow; }
.@{fa-css-prefix}-raindrops:before { content: @fa-var-raindrops; }
.@{fa-css-prefix}-ram:before { content: @fa-var-ram; }
.@{fa-css-prefix}-ramp-loading:before { content: @fa-var-ramp-loading; }
.@{fa-css-prefix}-random:before { content: @fa-var-random; }
.@{fa-css-prefix}-raspberry-pi:before { content: @fa-var-raspberry-pi; }
.@{fa-css-prefix}-ravelry:before { content: @fa-var-ravelry; }
.@{fa-css-prefix}-react:before { content: @fa-var-react; }
.@{fa-css-prefix}-reacteurope:before { content: @fa-var-reacteurope; }
.@{fa-css-prefix}-readme:before { content: @fa-var-readme; }
.@{fa-css-prefix}-rebel:before { content: @fa-var-rebel; }
.@{fa-css-prefix}-receipt:before { content: @fa-var-receipt; }
.@{fa-css-prefix}-rectangle-landscape:before { content: @fa-var-rectangle-landscape; }
.@{fa-css-prefix}-rectangle-portrait:before { content: @fa-var-rectangle-portrait; }
.@{fa-css-prefix}-rectangle-wide:before { content: @fa-var-rectangle-wide; }
.@{fa-css-prefix}-recycle:before { content: @fa-var-recycle; }
.@{fa-css-prefix}-red-river:before { content: @fa-var-red-river; }
.@{fa-css-prefix}-reddit:before { content: @fa-var-reddit; }
.@{fa-css-prefix}-reddit-alien:before { content: @fa-var-reddit-alien; }
.@{fa-css-prefix}-reddit-square:before { content: @fa-var-reddit-square; }
.@{fa-css-prefix}-redhat:before { content: @fa-var-redhat; }
.@{fa-css-prefix}-redo:before { content: @fa-var-redo; }
.@{fa-css-prefix}-redo-alt:before { content: @fa-var-redo-alt; }
.@{fa-css-prefix}-registered:before { content: @fa-var-registered; }
.@{fa-css-prefix}-remove-format:before { content: @fa-var-remove-format; }
.@{fa-css-prefix}-renren:before { content: @fa-var-renren; }
.@{fa-css-prefix}-repeat:before { content: @fa-var-repeat; }
.@{fa-css-prefix}-repeat-1:before { content: @fa-var-repeat-1; }
.@{fa-css-prefix}-repeat-1-alt:before { content: @fa-var-repeat-1-alt; }
.@{fa-css-prefix}-repeat-alt:before { content: @fa-var-repeat-alt; }
.@{fa-css-prefix}-reply:before { content: @fa-var-reply; }
.@{fa-css-prefix}-reply-all:before { content: @fa-var-reply-all; }
.@{fa-css-prefix}-replyd:before { content: @fa-var-replyd; }
.@{fa-css-prefix}-republican:before { content: @fa-var-republican; }
.@{fa-css-prefix}-researchgate:before { content: @fa-var-researchgate; }
.@{fa-css-prefix}-resolving:before { content: @fa-var-resolving; }
.@{fa-css-prefix}-restroom:before { content: @fa-var-restroom; }
.@{fa-css-prefix}-retweet:before { content: @fa-var-retweet; }
.@{fa-css-prefix}-retweet-alt:before { content: @fa-var-retweet-alt; }
.@{fa-css-prefix}-rev:before { content: @fa-var-rev; }
.@{fa-css-prefix}-ribbon:before { content: @fa-var-ribbon; }
.@{fa-css-prefix}-ring:before { content: @fa-var-ring; }
.@{fa-css-prefix}-rings-wedding:before { content: @fa-var-rings-wedding; }
.@{fa-css-prefix}-road:before { content: @fa-var-road; }
.@{fa-css-prefix}-robot:before { content: @fa-var-robot; }
.@{fa-css-prefix}-rocket:before { content: @fa-var-rocket; }
.@{fa-css-prefix}-rocketchat:before { content: @fa-var-rocketchat; }
.@{fa-css-prefix}-rockrms:before { content: @fa-var-rockrms; }
.@{fa-css-prefix}-route:before { content: @fa-var-route; }
.@{fa-css-prefix}-route-highway:before { content: @fa-var-route-highway; }
.@{fa-css-prefix}-route-interstate:before { content: @fa-var-route-interstate; }
.@{fa-css-prefix}-rss:before { content: @fa-var-rss; }
.@{fa-css-prefix}-rss-square:before { content: @fa-var-rss-square; }
.@{fa-css-prefix}-ruble-sign:before { content: @fa-var-ruble-sign; }
.@{fa-css-prefix}-ruler:before { content: @fa-var-ruler; }
.@{fa-css-prefix}-ruler-combined:before { content: @fa-var-ruler-combined; }
.@{fa-css-prefix}-ruler-horizontal:before { content: @fa-var-ruler-horizontal; }
.@{fa-css-prefix}-ruler-triangle:before { content: @fa-var-ruler-triangle; }
.@{fa-css-prefix}-ruler-vertical:before { content: @fa-var-ruler-vertical; }
.@{fa-css-prefix}-running:before { content: @fa-var-running; }
.@{fa-css-prefix}-rupee-sign:before { content: @fa-var-rupee-sign; }
.@{fa-css-prefix}-rv:before { content: @fa-var-rv; }
.@{fa-css-prefix}-sack:before { content: @fa-var-sack; }
.@{fa-css-prefix}-sack-dollar:before { content: @fa-var-sack-dollar; }
.@{fa-css-prefix}-sad-cry:before { content: @fa-var-sad-cry; }
.@{fa-css-prefix}-sad-tear:before { content: @fa-var-sad-tear; }
.@{fa-css-prefix}-safari:before { content: @fa-var-safari; }
.@{fa-css-prefix}-salad:before { content: @fa-var-salad; }
.@{fa-css-prefix}-salesforce:before { content: @fa-var-salesforce; }
.@{fa-css-prefix}-sandwich:before { content: @fa-var-sandwich; }
.@{fa-css-prefix}-sass:before { content: @fa-var-sass; }
.@{fa-css-prefix}-satellite:before { content: @fa-var-satellite; }
.@{fa-css-prefix}-satellite-dish:before { content: @fa-var-satellite-dish; }
.@{fa-css-prefix}-sausage:before { content: @fa-var-sausage; }
.@{fa-css-prefix}-save:before { content: @fa-var-save; }
.@{fa-css-prefix}-scalpel:before { content: @fa-var-scalpel; }
.@{fa-css-prefix}-scalpel-path:before { content: @fa-var-scalpel-path; }
.@{fa-css-prefix}-scanner:before { content: @fa-var-scanner; }
.@{fa-css-prefix}-scanner-keyboard:before { content: @fa-var-scanner-keyboard; }
.@{fa-css-prefix}-scanner-touchscreen:before { content: @fa-var-scanner-touchscreen; }
.@{fa-css-prefix}-scarecrow:before { content: @fa-var-scarecrow; }
.@{fa-css-prefix}-scarf:before { content: @fa-var-scarf; }
.@{fa-css-prefix}-schlix:before { content: @fa-var-schlix; }
.@{fa-css-prefix}-school:before { content: @fa-var-school; }
.@{fa-css-prefix}-screwdriver:before { content: @fa-var-screwdriver; }
.@{fa-css-prefix}-scribd:before { content: @fa-var-scribd; }
.@{fa-css-prefix}-scroll:before { content: @fa-var-scroll; }
.@{fa-css-prefix}-scroll-old:before { content: @fa-var-scroll-old; }
.@{fa-css-prefix}-scrubber:before { content: @fa-var-scrubber; }
.@{fa-css-prefix}-scythe:before { content: @fa-var-scythe; }
.@{fa-css-prefix}-sd-card:before { content: @fa-var-sd-card; }
.@{fa-css-prefix}-search:before { content: @fa-var-search; }
.@{fa-css-prefix}-search-dollar:before { content: @fa-var-search-dollar; }
.@{fa-css-prefix}-search-location:before { content: @fa-var-search-location; }
.@{fa-css-prefix}-search-minus:before { content: @fa-var-search-minus; }
.@{fa-css-prefix}-search-plus:before { content: @fa-var-search-plus; }
.@{fa-css-prefix}-searchengin:before { content: @fa-var-searchengin; }
.@{fa-css-prefix}-seedling:before { content: @fa-var-seedling; }
.@{fa-css-prefix}-sellcast:before { content: @fa-var-sellcast; }
.@{fa-css-prefix}-sellsy:before { content: @fa-var-sellsy; }
.@{fa-css-prefix}-send-back:before { content: @fa-var-send-back; }
.@{fa-css-prefix}-send-backward:before { content: @fa-var-send-backward; }
.@{fa-css-prefix}-server:before { content: @fa-var-server; }
.@{fa-css-prefix}-servicestack:before { content: @fa-var-servicestack; }
.@{fa-css-prefix}-shapes:before { content: @fa-var-shapes; }
.@{fa-css-prefix}-share:before { content: @fa-var-share; }
.@{fa-css-prefix}-share-all:before { content: @fa-var-share-all; }
.@{fa-css-prefix}-share-alt:before { content: @fa-var-share-alt; }
.@{fa-css-prefix}-share-alt-square:before { content: @fa-var-share-alt-square; }
.@{fa-css-prefix}-share-square:before { content: @fa-var-share-square; }
.@{fa-css-prefix}-sheep:before { content: @fa-var-sheep; }
.@{fa-css-prefix}-shekel-sign:before { content: @fa-var-shekel-sign; }
.@{fa-css-prefix}-shield:before { content: @fa-var-shield; }
.@{fa-css-prefix}-shield-alt:before { content: @fa-var-shield-alt; }
.@{fa-css-prefix}-shield-check:before { content: @fa-var-shield-check; }
.@{fa-css-prefix}-shield-cross:before { content: @fa-var-shield-cross; }
.@{fa-css-prefix}-ship:before { content: @fa-var-ship; }
.@{fa-css-prefix}-shipping-fast:before { content: @fa-var-shipping-fast; }
.@{fa-css-prefix}-shipping-timed:before { content: @fa-var-shipping-timed; }
.@{fa-css-prefix}-shirtsinbulk:before { content: @fa-var-shirtsinbulk; }
.@{fa-css-prefix}-shish-kebab:before { content: @fa-var-shish-kebab; }
.@{fa-css-prefix}-shoe-prints:before { content: @fa-var-shoe-prints; }
.@{fa-css-prefix}-shopping-bag:before { content: @fa-var-shopping-bag; }
.@{fa-css-prefix}-shopping-basket:before { content: @fa-var-shopping-basket; }
.@{fa-css-prefix}-shopping-cart:before { content: @fa-var-shopping-cart; }
.@{fa-css-prefix}-shopware:before { content: @fa-var-shopware; }
.@{fa-css-prefix}-shovel:before { content: @fa-var-shovel; }
.@{fa-css-prefix}-shovel-snow:before { content: @fa-var-shovel-snow; }
.@{fa-css-prefix}-shower:before { content: @fa-var-shower; }
.@{fa-css-prefix}-shredder:before { content: @fa-var-shredder; }
.@{fa-css-prefix}-shuttle-van:before { content: @fa-var-shuttle-van; }
.@{fa-css-prefix}-shuttlecock:before { content: @fa-var-shuttlecock; }
.@{fa-css-prefix}-sickle:before { content: @fa-var-sickle; }
.@{fa-css-prefix}-sigma:before { content: @fa-var-sigma; }
.@{fa-css-prefix}-sign:before { content: @fa-var-sign; }
.@{fa-css-prefix}-sign-in:before { content: @fa-var-sign-in; }
.@{fa-css-prefix}-sign-in-alt:before { content: @fa-var-sign-in-alt; }
.@{fa-css-prefix}-sign-language:before { content: @fa-var-sign-language; }
.@{fa-css-prefix}-sign-out:before { content: @fa-var-sign-out; }
.@{fa-css-prefix}-sign-out-alt:before { content: @fa-var-sign-out-alt; }
.@{fa-css-prefix}-signal:before { content: @fa-var-signal; }
.@{fa-css-prefix}-signal-1:before { content: @fa-var-signal-1; }
.@{fa-css-prefix}-signal-2:before { content: @fa-var-signal-2; }
.@{fa-css-prefix}-signal-3:before { content: @fa-var-signal-3; }
.@{fa-css-prefix}-signal-4:before { content: @fa-var-signal-4; }
.@{fa-css-prefix}-signal-alt:before { content: @fa-var-signal-alt; }
.@{fa-css-prefix}-signal-alt-1:before { content: @fa-var-signal-alt-1; }
.@{fa-css-prefix}-signal-alt-2:before { content: @fa-var-signal-alt-2; }
.@{fa-css-prefix}-signal-alt-3:before { content: @fa-var-signal-alt-3; }
.@{fa-css-prefix}-signal-alt-slash:before { content: @fa-var-signal-alt-slash; }
.@{fa-css-prefix}-signal-slash:before { content: @fa-var-signal-slash; }
.@{fa-css-prefix}-signature:before { content: @fa-var-signature; }
.@{fa-css-prefix}-sim-card:before { content: @fa-var-sim-card; }
.@{fa-css-prefix}-simplybuilt:before { content: @fa-var-simplybuilt; }
.@{fa-css-prefix}-sistrix:before { content: @fa-var-sistrix; }
.@{fa-css-prefix}-sitemap:before { content: @fa-var-sitemap; }
.@{fa-css-prefix}-sith:before { content: @fa-var-sith; }
.@{fa-css-prefix}-skating:before { content: @fa-var-skating; }
.@{fa-css-prefix}-skeleton:before { content: @fa-var-skeleton; }
.@{fa-css-prefix}-sketch:before { content: @fa-var-sketch; }
.@{fa-css-prefix}-ski-jump:before { content: @fa-var-ski-jump; }
.@{fa-css-prefix}-ski-lift:before { content: @fa-var-ski-lift; }
.@{fa-css-prefix}-skiing:before { content: @fa-var-skiing; }
.@{fa-css-prefix}-skiing-nordic:before { content: @fa-var-skiing-nordic; }
.@{fa-css-prefix}-skull:before { content: @fa-var-skull; }
.@{fa-css-prefix}-skull-crossbones:before { content: @fa-var-skull-crossbones; }
.@{fa-css-prefix}-skyatlas:before { content: @fa-var-skyatlas; }
.@{fa-css-prefix}-skype:before { content: @fa-var-skype; }
.@{fa-css-prefix}-slack:before { content: @fa-var-slack; }
.@{fa-css-prefix}-slack-hash:before { content: @fa-var-slack-hash; }
.@{fa-css-prefix}-slash:before { content: @fa-var-slash; }
.@{fa-css-prefix}-sledding:before { content: @fa-var-sledding; }
.@{fa-css-prefix}-sleigh:before { content: @fa-var-sleigh; }
.@{fa-css-prefix}-sliders-h:before { content: @fa-var-sliders-h; }
.@{fa-css-prefix}-sliders-h-square:before { content: @fa-var-sliders-h-square; }
.@{fa-css-prefix}-sliders-v:before { content: @fa-var-sliders-v; }
.@{fa-css-prefix}-sliders-v-square:before { content: @fa-var-sliders-v-square; }
.@{fa-css-prefix}-slideshare:before { content: @fa-var-slideshare; }
.@{fa-css-prefix}-smile:before { content: @fa-var-smile; }
.@{fa-css-prefix}-smile-beam:before { content: @fa-var-smile-beam; }
.@{fa-css-prefix}-smile-plus:before { content: @fa-var-smile-plus; }
.@{fa-css-prefix}-smile-wink:before { content: @fa-var-smile-wink; }
.@{fa-css-prefix}-smog:before { content: @fa-var-smog; }
.@{fa-css-prefix}-smoke:before { content: @fa-var-smoke; }
.@{fa-css-prefix}-smoking:before { content: @fa-var-smoking; }
.@{fa-css-prefix}-smoking-ban:before { content: @fa-var-smoking-ban; }
.@{fa-css-prefix}-sms:before { content: @fa-var-sms; }
.@{fa-css-prefix}-snake:before { content: @fa-var-snake; }
.@{fa-css-prefix}-snapchat:before { content: @fa-var-snapchat; }
.@{fa-css-prefix}-snapchat-ghost:before { content: @fa-var-snapchat-ghost; }
.@{fa-css-prefix}-snapchat-square:before { content: @fa-var-snapchat-square; }
.@{fa-css-prefix}-snooze:before { content: @fa-var-snooze; }
.@{fa-css-prefix}-snow-blowing:before { content: @fa-var-snow-blowing; }
.@{fa-css-prefix}-snowboarding:before { content: @fa-var-snowboarding; }
.@{fa-css-prefix}-snowflake:before { content: @fa-var-snowflake; }
.@{fa-css-prefix}-snowflakes:before { content: @fa-var-snowflakes; }
.@{fa-css-prefix}-snowman:before { content: @fa-var-snowman; }
.@{fa-css-prefix}-snowmobile:before { content: @fa-var-snowmobile; }
.@{fa-css-prefix}-snowplow:before { content: @fa-var-snowplow; }
.@{fa-css-prefix}-socks:before { content: @fa-var-socks; }
.@{fa-css-prefix}-solar-panel:before { content: @fa-var-solar-panel; }
.@{fa-css-prefix}-sort:before { content: @fa-var-sort; }
.@{fa-css-prefix}-sort-alpha-down:before { content: @fa-var-sort-alpha-down; }
.@{fa-css-prefix}-sort-alpha-down-alt:before { content: @fa-var-sort-alpha-down-alt; }
.@{fa-css-prefix}-sort-alpha-up:before { content: @fa-var-sort-alpha-up; }
.@{fa-css-prefix}-sort-alpha-up-alt:before { content: @fa-var-sort-alpha-up-alt; }
.@{fa-css-prefix}-sort-alt:before { content: @fa-var-sort-alt; }
.@{fa-css-prefix}-sort-amount-down:before { content: @fa-var-sort-amount-down; }
.@{fa-css-prefix}-sort-amount-down-alt:before { content: @fa-var-sort-amount-down-alt; }
.@{fa-css-prefix}-sort-amount-up:before { content: @fa-var-sort-amount-up; }
.@{fa-css-prefix}-sort-amount-up-alt:before { content: @fa-var-sort-amount-up-alt; }
.@{fa-css-prefix}-sort-down:before { content: @fa-var-sort-down; }
.@{fa-css-prefix}-sort-numeric-down:before { content: @fa-var-sort-numeric-down; }
.@{fa-css-prefix}-sort-numeric-down-alt:before { content: @fa-var-sort-numeric-down-alt; }
.@{fa-css-prefix}-sort-numeric-up:before { content: @fa-var-sort-numeric-up; }
.@{fa-css-prefix}-sort-numeric-up-alt:before { content: @fa-var-sort-numeric-up-alt; }
.@{fa-css-prefix}-sort-shapes-down:before { content: @fa-var-sort-shapes-down; }
.@{fa-css-prefix}-sort-shapes-down-alt:before { content: @fa-var-sort-shapes-down-alt; }
.@{fa-css-prefix}-sort-shapes-up:before { content: @fa-var-sort-shapes-up; }
.@{fa-css-prefix}-sort-shapes-up-alt:before { content: @fa-var-sort-shapes-up-alt; }
.@{fa-css-prefix}-sort-size-down:before { content: @fa-var-sort-size-down; }
.@{fa-css-prefix}-sort-size-down-alt:before { content: @fa-var-sort-size-down-alt; }
.@{fa-css-prefix}-sort-size-up:before { content: @fa-var-sort-size-up; }
.@{fa-css-prefix}-sort-size-up-alt:before { content: @fa-var-sort-size-up-alt; }
.@{fa-css-prefix}-sort-up:before { content: @fa-var-sort-up; }
.@{fa-css-prefix}-soundcloud:before { content: @fa-var-soundcloud; }
.@{fa-css-prefix}-soup:before { content: @fa-var-soup; }
.@{fa-css-prefix}-sourcetree:before { content: @fa-var-sourcetree; }
.@{fa-css-prefix}-spa:before { content: @fa-var-spa; }
.@{fa-css-prefix}-space-shuttle:before { content: @fa-var-space-shuttle; }
.@{fa-css-prefix}-spade:before { content: @fa-var-spade; }
.@{fa-css-prefix}-sparkles:before { content: @fa-var-sparkles; }
.@{fa-css-prefix}-speakap:before { content: @fa-var-speakap; }
.@{fa-css-prefix}-speaker-deck:before { content: @fa-var-speaker-deck; }
.@{fa-css-prefix}-spell-check:before { content: @fa-var-spell-check; }
.@{fa-css-prefix}-spider:before { content: @fa-var-spider; }
.@{fa-css-prefix}-spider-black-widow:before { content: @fa-var-spider-black-widow; }
.@{fa-css-prefix}-spider-web:before { content: @fa-var-spider-web; }
.@{fa-css-prefix}-spinner:before { content: @fa-var-spinner; }
.@{fa-css-prefix}-spinner-third:before { content: @fa-var-spinner-third; }
.@{fa-css-prefix}-splotch:before { content: @fa-var-splotch; }
.@{fa-css-prefix}-spotify:before { content: @fa-var-spotify; }
.@{fa-css-prefix}-spray-can:before { content: @fa-var-spray-can; }
.@{fa-css-prefix}-square:before { content: @fa-var-square; }
.@{fa-css-prefix}-square-full:before { content: @fa-var-square-full; }
.@{fa-css-prefix}-square-root:before { content: @fa-var-square-root; }
.@{fa-css-prefix}-square-root-alt:before { content: @fa-var-square-root-alt; }
.@{fa-css-prefix}-squarespace:before { content: @fa-var-squarespace; }
.@{fa-css-prefix}-squirrel:before { content: @fa-var-squirrel; }
.@{fa-css-prefix}-stack-exchange:before { content: @fa-var-stack-exchange; }
.@{fa-css-prefix}-stack-overflow:before { content: @fa-var-stack-overflow; }
.@{fa-css-prefix}-stackpath:before { content: @fa-var-stackpath; }
.@{fa-css-prefix}-staff:before { content: @fa-var-staff; }
.@{fa-css-prefix}-stamp:before { content: @fa-var-stamp; }
.@{fa-css-prefix}-star:before { content: @fa-var-star; }
.@{fa-css-prefix}-star-and-crescent:before { content: @fa-var-star-and-crescent; }
.@{fa-css-prefix}-star-christmas:before { content: @fa-var-star-christmas; }
.@{fa-css-prefix}-star-exclamation:before { content: @fa-var-star-exclamation; }
.@{fa-css-prefix}-star-half:before { content: @fa-var-star-half; }
.@{fa-css-prefix}-star-half-alt:before { content: @fa-var-star-half-alt; }
.@{fa-css-prefix}-star-of-david:before { content: @fa-var-star-of-david; }
.@{fa-css-prefix}-star-of-life:before { content: @fa-var-star-of-life; }
.@{fa-css-prefix}-stars:before { content: @fa-var-stars; }
.@{fa-css-prefix}-staylinked:before { content: @fa-var-staylinked; }
.@{fa-css-prefix}-steak:before { content: @fa-var-steak; }
.@{fa-css-prefix}-steam:before { content: @fa-var-steam; }
.@{fa-css-prefix}-steam-square:before { content: @fa-var-steam-square; }
.@{fa-css-prefix}-steam-symbol:before { content: @fa-var-steam-symbol; }
.@{fa-css-prefix}-steering-wheel:before { content: @fa-var-steering-wheel; }
.@{fa-css-prefix}-step-backward:before { content: @fa-var-step-backward; }
.@{fa-css-prefix}-step-forward:before { content: @fa-var-step-forward; }
.@{fa-css-prefix}-stethoscope:before { content: @fa-var-stethoscope; }
.@{fa-css-prefix}-sticker-mule:before { content: @fa-var-sticker-mule; }
.@{fa-css-prefix}-sticky-note:before { content: @fa-var-sticky-note; }
.@{fa-css-prefix}-stocking:before { content: @fa-var-stocking; }
.@{fa-css-prefix}-stomach:before { content: @fa-var-stomach; }
.@{fa-css-prefix}-stop:before { content: @fa-var-stop; }
.@{fa-css-prefix}-stop-circle:before { content: @fa-var-stop-circle; }
.@{fa-css-prefix}-stopwatch:before { content: @fa-var-stopwatch; }
.@{fa-css-prefix}-store:before { content: @fa-var-store; }
.@{fa-css-prefix}-store-alt:before { content: @fa-var-store-alt; }
.@{fa-css-prefix}-strava:before { content: @fa-var-strava; }
.@{fa-css-prefix}-stream:before { content: @fa-var-stream; }
.@{fa-css-prefix}-street-view:before { content: @fa-var-street-view; }
.@{fa-css-prefix}-stretcher:before { content: @fa-var-stretcher; }
.@{fa-css-prefix}-strikethrough:before { content: @fa-var-strikethrough; }
.@{fa-css-prefix}-stripe:before { content: @fa-var-stripe; }
.@{fa-css-prefix}-stripe-s:before { content: @fa-var-stripe-s; }
.@{fa-css-prefix}-stroopwafel:before { content: @fa-var-stroopwafel; }
.@{fa-css-prefix}-studiovinari:before { content: @fa-var-studiovinari; }
.@{fa-css-prefix}-stumbleupon:before { content: @fa-var-stumbleupon; }
.@{fa-css-prefix}-stumbleupon-circle:before { content: @fa-var-stumbleupon-circle; }
.@{fa-css-prefix}-subscript:before { content: @fa-var-subscript; }
.@{fa-css-prefix}-subway:before { content: @fa-var-subway; }
.@{fa-css-prefix}-suitcase:before { content: @fa-var-suitcase; }
.@{fa-css-prefix}-suitcase-rolling:before { content: @fa-var-suitcase-rolling; }
.@{fa-css-prefix}-sun:before { content: @fa-var-sun; }
.@{fa-css-prefix}-sun-cloud:before { content: @fa-var-sun-cloud; }
.@{fa-css-prefix}-sun-dust:before { content: @fa-var-sun-dust; }
.@{fa-css-prefix}-sun-haze:before { content: @fa-var-sun-haze; }
.@{fa-css-prefix}-sunglasses:before { content: @fa-var-sunglasses; }
.@{fa-css-prefix}-sunrise:before { content: @fa-var-sunrise; }
.@{fa-css-prefix}-sunset:before { content: @fa-var-sunset; }
.@{fa-css-prefix}-superpowers:before { content: @fa-var-superpowers; }
.@{fa-css-prefix}-superscript:before { content: @fa-var-superscript; }
.@{fa-css-prefix}-supple:before { content: @fa-var-supple; }
.@{fa-css-prefix}-surprise:before { content: @fa-var-surprise; }
.@{fa-css-prefix}-suse:before { content: @fa-var-suse; }
.@{fa-css-prefix}-swatchbook:before { content: @fa-var-swatchbook; }
.@{fa-css-prefix}-swimmer:before { content: @fa-var-swimmer; }
.@{fa-css-prefix}-swimming-pool:before { content: @fa-var-swimming-pool; }
.@{fa-css-prefix}-sword:before { content: @fa-var-sword; }
.@{fa-css-prefix}-swords:before { content: @fa-var-swords; }
.@{fa-css-prefix}-symfony:before { content: @fa-var-symfony; }
.@{fa-css-prefix}-synagogue:before { content: @fa-var-synagogue; }
.@{fa-css-prefix}-sync:before { content: @fa-var-sync; }
.@{fa-css-prefix}-sync-alt:before { content: @fa-var-sync-alt; }
.@{fa-css-prefix}-syringe:before { content: @fa-var-syringe; }
.@{fa-css-prefix}-table:before { content: @fa-var-table; }
.@{fa-css-prefix}-table-tennis:before { content: @fa-var-table-tennis; }
.@{fa-css-prefix}-tablet:before { content: @fa-var-tablet; }
.@{fa-css-prefix}-tablet-alt:before { content: @fa-var-tablet-alt; }
.@{fa-css-prefix}-tablet-android:before { content: @fa-var-tablet-android; }
.@{fa-css-prefix}-tablet-android-alt:before { content: @fa-var-tablet-android-alt; }
.@{fa-css-prefix}-tablet-rugged:before { content: @fa-var-tablet-rugged; }
.@{fa-css-prefix}-tablets:before { content: @fa-var-tablets; }
.@{fa-css-prefix}-tachometer:before { content: @fa-var-tachometer; }
.@{fa-css-prefix}-tachometer-alt:before { content: @fa-var-tachometer-alt; }
.@{fa-css-prefix}-tachometer-alt-average:before { content: @fa-var-tachometer-alt-average; }
.@{fa-css-prefix}-tachometer-alt-fast:before { content: @fa-var-tachometer-alt-fast; }
.@{fa-css-prefix}-tachometer-alt-fastest:before { content: @fa-var-tachometer-alt-fastest; }
.@{fa-css-prefix}-tachometer-alt-slow:before { content: @fa-var-tachometer-alt-slow; }
.@{fa-css-prefix}-tachometer-alt-slowest:before { content: @fa-var-tachometer-alt-slowest; }
.@{fa-css-prefix}-tachometer-average:before { content: @fa-var-tachometer-average; }
.@{fa-css-prefix}-tachometer-fast:before { content: @fa-var-tachometer-fast; }
.@{fa-css-prefix}-tachometer-fastest:before { content: @fa-var-tachometer-fastest; }
.@{fa-css-prefix}-tachometer-slow:before { content: @fa-var-tachometer-slow; }
.@{fa-css-prefix}-tachometer-slowest:before { content: @fa-var-tachometer-slowest; }
.@{fa-css-prefix}-taco:before { content: @fa-var-taco; }
.@{fa-css-prefix}-tag:before { content: @fa-var-tag; }
.@{fa-css-prefix}-tags:before { content: @fa-var-tags; }
.@{fa-css-prefix}-tally:before { content: @fa-var-tally; }
.@{fa-css-prefix}-tanakh:before { content: @fa-var-tanakh; }
.@{fa-css-prefix}-tape:before { content: @fa-var-tape; }
.@{fa-css-prefix}-tasks:before { content: @fa-var-tasks; }
.@{fa-css-prefix}-tasks-alt:before { content: @fa-var-tasks-alt; }
.@{fa-css-prefix}-taxi:before { content: @fa-var-taxi; }
.@{fa-css-prefix}-teamspeak:before { content: @fa-var-teamspeak; }
.@{fa-css-prefix}-teeth:before { content: @fa-var-teeth; }
.@{fa-css-prefix}-teeth-open:before { content: @fa-var-teeth-open; }
.@{fa-css-prefix}-telegram:before { content: @fa-var-telegram; }
.@{fa-css-prefix}-telegram-plane:before { content: @fa-var-telegram-plane; }
.@{fa-css-prefix}-temperature-frigid:before { content: @fa-var-temperature-frigid; }
.@{fa-css-prefix}-temperature-high:before { content: @fa-var-temperature-high; }
.@{fa-css-prefix}-temperature-hot:before { content: @fa-var-temperature-hot; }
.@{fa-css-prefix}-temperature-low:before { content: @fa-var-temperature-low; }
.@{fa-css-prefix}-tencent-weibo:before { content: @fa-var-tencent-weibo; }
.@{fa-css-prefix}-tenge:before { content: @fa-var-tenge; }
.@{fa-css-prefix}-tennis-ball:before { content: @fa-var-tennis-ball; }
.@{fa-css-prefix}-terminal:before { content: @fa-var-terminal; }
.@{fa-css-prefix}-text:before { content: @fa-var-text; }
.@{fa-css-prefix}-text-height:before { content: @fa-var-text-height; }
.@{fa-css-prefix}-text-size:before { content: @fa-var-text-size; }
.@{fa-css-prefix}-text-width:before { content: @fa-var-text-width; }
.@{fa-css-prefix}-th:before { content: @fa-var-th; }
.@{fa-css-prefix}-th-large:before { content: @fa-var-th-large; }
.@{fa-css-prefix}-th-list:before { content: @fa-var-th-list; }
.@{fa-css-prefix}-the-red-yeti:before { content: @fa-var-the-red-yeti; }
.@{fa-css-prefix}-theater-masks:before { content: @fa-var-theater-masks; }
.@{fa-css-prefix}-themeco:before { content: @fa-var-themeco; }
.@{fa-css-prefix}-themeisle:before { content: @fa-var-themeisle; }
.@{fa-css-prefix}-thermometer:before { content: @fa-var-thermometer; }
.@{fa-css-prefix}-thermometer-empty:before { content: @fa-var-thermometer-empty; }
.@{fa-css-prefix}-thermometer-full:before { content: @fa-var-thermometer-full; }
.@{fa-css-prefix}-thermometer-half:before { content: @fa-var-thermometer-half; }
.@{fa-css-prefix}-thermometer-quarter:before { content: @fa-var-thermometer-quarter; }
.@{fa-css-prefix}-thermometer-three-quarters:before { content: @fa-var-thermometer-three-quarters; }
.@{fa-css-prefix}-theta:before { content: @fa-var-theta; }
.@{fa-css-prefix}-think-peaks:before { content: @fa-var-think-peaks; }
.@{fa-css-prefix}-thumbs-down:before { content: @fa-var-thumbs-down; }
.@{fa-css-prefix}-thumbs-up:before { content: @fa-var-thumbs-up; }
.@{fa-css-prefix}-thumbtack:before { content: @fa-var-thumbtack; }
.@{fa-css-prefix}-thunderstorm:before { content: @fa-var-thunderstorm; }
.@{fa-css-prefix}-thunderstorm-moon:before { content: @fa-var-thunderstorm-moon; }
.@{fa-css-prefix}-thunderstorm-sun:before { content: @fa-var-thunderstorm-sun; }
.@{fa-css-prefix}-ticket:before { content: @fa-var-ticket; }
.@{fa-css-prefix}-ticket-alt:before { content: @fa-var-ticket-alt; }
.@{fa-css-prefix}-tilde:before { content: @fa-var-tilde; }
.@{fa-css-prefix}-times:before { content: @fa-var-times; }
.@{fa-css-prefix}-times-circle:before { content: @fa-var-times-circle; }
.@{fa-css-prefix}-times-hexagon:before { content: @fa-var-times-hexagon; }
.@{fa-css-prefix}-times-octagon:before { content: @fa-var-times-octagon; }
.@{fa-css-prefix}-times-square:before { content: @fa-var-times-square; }
.@{fa-css-prefix}-tint:before { content: @fa-var-tint; }
.@{fa-css-prefix}-tint-slash:before { content: @fa-var-tint-slash; }
.@{fa-css-prefix}-tire:before { content: @fa-var-tire; }
.@{fa-css-prefix}-tire-flat:before { content: @fa-var-tire-flat; }
.@{fa-css-prefix}-tire-pressure-warning:before { content: @fa-var-tire-pressure-warning; }
.@{fa-css-prefix}-tire-rugged:before { content: @fa-var-tire-rugged; }
.@{fa-css-prefix}-tired:before { content: @fa-var-tired; }
.@{fa-css-prefix}-toggle-off:before { content: @fa-var-toggle-off; }
.@{fa-css-prefix}-toggle-on:before { content: @fa-var-toggle-on; }
.@{fa-css-prefix}-toilet:before { content: @fa-var-toilet; }
.@{fa-css-prefix}-toilet-paper:before { content: @fa-var-toilet-paper; }
.@{fa-css-prefix}-toilet-paper-alt:before { content: @fa-var-toilet-paper-alt; }
.@{fa-css-prefix}-tombstone:before { content: @fa-var-tombstone; }
.@{fa-css-prefix}-tombstone-alt:before { content: @fa-var-tombstone-alt; }
.@{fa-css-prefix}-toolbox:before { content: @fa-var-toolbox; }
.@{fa-css-prefix}-tools:before { content: @fa-var-tools; }
.@{fa-css-prefix}-tooth:before { content: @fa-var-tooth; }
.@{fa-css-prefix}-toothbrush:before { content: @fa-var-toothbrush; }
.@{fa-css-prefix}-torah:before { content: @fa-var-torah; }
.@{fa-css-prefix}-torii-gate:before { content: @fa-var-torii-gate; }
.@{fa-css-prefix}-tornado:before { content: @fa-var-tornado; }
.@{fa-css-prefix}-tractor:before { content: @fa-var-tractor; }
.@{fa-css-prefix}-trade-federation:before { content: @fa-var-trade-federation; }
.@{fa-css-prefix}-trademark:before { content: @fa-var-trademark; }
.@{fa-css-prefix}-traffic-cone:before { content: @fa-var-traffic-cone; }
.@{fa-css-prefix}-traffic-light:before { content: @fa-var-traffic-light; }
.@{fa-css-prefix}-traffic-light-go:before { content: @fa-var-traffic-light-go; }
.@{fa-css-prefix}-traffic-light-slow:before { content: @fa-var-traffic-light-slow; }
.@{fa-css-prefix}-traffic-light-stop:before { content: @fa-var-traffic-light-stop; }
.@{fa-css-prefix}-train:before { content: @fa-var-train; }
.@{fa-css-prefix}-tram:before { content: @fa-var-tram; }
.@{fa-css-prefix}-transgender:before { content: @fa-var-transgender; }
.@{fa-css-prefix}-transgender-alt:before { content: @fa-var-transgender-alt; }
.@{fa-css-prefix}-trash:before { content: @fa-var-trash; }
.@{fa-css-prefix}-trash-alt:before { content: @fa-var-trash-alt; }
.@{fa-css-prefix}-trash-restore:before { content: @fa-var-trash-restore; }
.@{fa-css-prefix}-trash-restore-alt:before { content: @fa-var-trash-restore-alt; }
.@{fa-css-prefix}-trash-undo:before { content: @fa-var-trash-undo; }
.@{fa-css-prefix}-trash-undo-alt:before { content: @fa-var-trash-undo-alt; }
.@{fa-css-prefix}-treasure-chest:before { content: @fa-var-treasure-chest; }
.@{fa-css-prefix}-tree:before { content: @fa-var-tree; }
.@{fa-css-prefix}-tree-alt:before { content: @fa-var-tree-alt; }
.@{fa-css-prefix}-tree-christmas:before { content: @fa-var-tree-christmas; }
.@{fa-css-prefix}-tree-decorated:before { content: @fa-var-tree-decorated; }
.@{fa-css-prefix}-tree-large:before { content: @fa-var-tree-large; }
.@{fa-css-prefix}-tree-palm:before { content: @fa-var-tree-palm; }
.@{fa-css-prefix}-trees:before { content: @fa-var-trees; }
.@{fa-css-prefix}-trello:before { content: @fa-var-trello; }
.@{fa-css-prefix}-triangle:before { content: @fa-var-triangle; }
.@{fa-css-prefix}-tripadvisor:before { content: @fa-var-tripadvisor; }
.@{fa-css-prefix}-trophy:before { content: @fa-var-trophy; }
.@{fa-css-prefix}-trophy-alt:before { content: @fa-var-trophy-alt; }
.@{fa-css-prefix}-truck:before { content: @fa-var-truck; }
.@{fa-css-prefix}-truck-container:before { content: @fa-var-truck-container; }
.@{fa-css-prefix}-truck-couch:before { content: @fa-var-truck-couch; }
.@{fa-css-prefix}-truck-loading:before { content: @fa-var-truck-loading; }
.@{fa-css-prefix}-truck-monster:before { content: @fa-var-truck-monster; }
.@{fa-css-prefix}-truck-moving:before { content: @fa-var-truck-moving; }
.@{fa-css-prefix}-truck-pickup:before { content: @fa-var-truck-pickup; }
.@{fa-css-prefix}-truck-plow:before { content: @fa-var-truck-plow; }
.@{fa-css-prefix}-truck-ramp:before { content: @fa-var-truck-ramp; }
.@{fa-css-prefix}-tshirt:before { content: @fa-var-tshirt; }
.@{fa-css-prefix}-tty:before { content: @fa-var-tty; }
.@{fa-css-prefix}-tumblr:before { content: @fa-var-tumblr; }
.@{fa-css-prefix}-tumblr-square:before { content: @fa-var-tumblr-square; }
.@{fa-css-prefix}-turkey:before { content: @fa-var-turkey; }
.@{fa-css-prefix}-turtle:before { content: @fa-var-turtle; }
.@{fa-css-prefix}-tv:before { content: @fa-var-tv; }
.@{fa-css-prefix}-tv-retro:before { content: @fa-var-tv-retro; }
.@{fa-css-prefix}-twitch:before { content: @fa-var-twitch; }
.@{fa-css-prefix}-twitter:before { content: @fa-var-twitter; }
.@{fa-css-prefix}-twitter-square:before { content: @fa-var-twitter-square; }
.@{fa-css-prefix}-typo3:before { content: @fa-var-typo3; }
.@{fa-css-prefix}-uber:before { content: @fa-var-uber; }
.@{fa-css-prefix}-ubuntu:before { content: @fa-var-ubuntu; }
.@{fa-css-prefix}-uikit:before { content: @fa-var-uikit; }
.@{fa-css-prefix}-umbrella:before { content: @fa-var-umbrella; }
.@{fa-css-prefix}-umbrella-beach:before { content: @fa-var-umbrella-beach; }
.@{fa-css-prefix}-underline:before { content: @fa-var-underline; }
.@{fa-css-prefix}-undo:before { content: @fa-var-undo; }
.@{fa-css-prefix}-undo-alt:before { content: @fa-var-undo-alt; }
.@{fa-css-prefix}-unicorn:before { content: @fa-var-unicorn; }
.@{fa-css-prefix}-union:before { content: @fa-var-union; }
.@{fa-css-prefix}-uniregistry:before { content: @fa-var-uniregistry; }
.@{fa-css-prefix}-universal-access:before { content: @fa-var-universal-access; }
.@{fa-css-prefix}-university:before { content: @fa-var-university; }
.@{fa-css-prefix}-unlink:before { content: @fa-var-unlink; }
.@{fa-css-prefix}-unlock:before { content: @fa-var-unlock; }
.@{fa-css-prefix}-unlock-alt:before { content: @fa-var-unlock-alt; }
.@{fa-css-prefix}-untappd:before { content: @fa-var-untappd; }
.@{fa-css-prefix}-upload:before { content: @fa-var-upload; }
.@{fa-css-prefix}-ups:before { content: @fa-var-ups; }
.@{fa-css-prefix}-usb:before { content: @fa-var-usb; }
.@{fa-css-prefix}-usd-circle:before { content: @fa-var-usd-circle; }
.@{fa-css-prefix}-usd-square:before { content: @fa-var-usd-square; }
.@{fa-css-prefix}-user:before { content: @fa-var-user; }
.@{fa-css-prefix}-user-alt:before { content: @fa-var-user-alt; }
.@{fa-css-prefix}-user-alt-slash:before { content: @fa-var-user-alt-slash; }
.@{fa-css-prefix}-user-astronaut:before { content: @fa-var-user-astronaut; }
.@{fa-css-prefix}-user-chart:before { content: @fa-var-user-chart; }
.@{fa-css-prefix}-user-check:before { content: @fa-var-user-check; }
.@{fa-css-prefix}-user-circle:before { content: @fa-var-user-circle; }
.@{fa-css-prefix}-user-clock:before { content: @fa-var-user-clock; }
.@{fa-css-prefix}-user-cog:before { content: @fa-var-user-cog; }
.@{fa-css-prefix}-user-crown:before { content: @fa-var-user-crown; }
.@{fa-css-prefix}-user-edit:before { content: @fa-var-user-edit; }
.@{fa-css-prefix}-user-friends:before { content: @fa-var-user-friends; }
.@{fa-css-prefix}-user-graduate:before { content: @fa-var-user-graduate; }
.@{fa-css-prefix}-user-hard-hat:before { content: @fa-var-user-hard-hat; }
.@{fa-css-prefix}-user-headset:before { content: @fa-var-user-headset; }
.@{fa-css-prefix}-user-injured:before { content: @fa-var-user-injured; }
.@{fa-css-prefix}-user-lock:before { content: @fa-var-user-lock; }
.@{fa-css-prefix}-user-md:before { content: @fa-var-user-md; }
.@{fa-css-prefix}-user-md-chat:before { content: @fa-var-user-md-chat; }
.@{fa-css-prefix}-user-minus:before { content: @fa-var-user-minus; }
.@{fa-css-prefix}-user-ninja:before { content: @fa-var-user-ninja; }
.@{fa-css-prefix}-user-nurse:before { content: @fa-var-user-nurse; }
.@{fa-css-prefix}-user-plus:before { content: @fa-var-user-plus; }
.@{fa-css-prefix}-user-secret:before { content: @fa-var-user-secret; }
.@{fa-css-prefix}-user-shield:before { content: @fa-var-user-shield; }
.@{fa-css-prefix}-user-slash:before { content: @fa-var-user-slash; }
.@{fa-css-prefix}-user-tag:before { content: @fa-var-user-tag; }
.@{fa-css-prefix}-user-tie:before { content: @fa-var-user-tie; }
.@{fa-css-prefix}-user-times:before { content: @fa-var-user-times; }
.@{fa-css-prefix}-users:before { content: @fa-var-users; }
.@{fa-css-prefix}-users-class:before { content: @fa-var-users-class; }
.@{fa-css-prefix}-users-cog:before { content: @fa-var-users-cog; }
.@{fa-css-prefix}-users-crown:before { content: @fa-var-users-crown; }
.@{fa-css-prefix}-users-medical:before { content: @fa-var-users-medical; }
.@{fa-css-prefix}-usps:before { content: @fa-var-usps; }
.@{fa-css-prefix}-ussunnah:before { content: @fa-var-ussunnah; }
.@{fa-css-prefix}-utensil-fork:before { content: @fa-var-utensil-fork; }
.@{fa-css-prefix}-utensil-knife:before { content: @fa-var-utensil-knife; }
.@{fa-css-prefix}-utensil-spoon:before { content: @fa-var-utensil-spoon; }
.@{fa-css-prefix}-utensils:before { content: @fa-var-utensils; }
.@{fa-css-prefix}-utensils-alt:before { content: @fa-var-utensils-alt; }
.@{fa-css-prefix}-vaadin:before { content: @fa-var-vaadin; }
.@{fa-css-prefix}-value-absolute:before { content: @fa-var-value-absolute; }
.@{fa-css-prefix}-vector-square:before { content: @fa-var-vector-square; }
.@{fa-css-prefix}-venus:before { content: @fa-var-venus; }
.@{fa-css-prefix}-venus-double:before { content: @fa-var-venus-double; }
.@{fa-css-prefix}-venus-mars:before { content: @fa-var-venus-mars; }
.@{fa-css-prefix}-viacoin:before { content: @fa-var-viacoin; }
.@{fa-css-prefix}-viadeo:before { content: @fa-var-viadeo; }
.@{fa-css-prefix}-viadeo-square:before { content: @fa-var-viadeo-square; }
.@{fa-css-prefix}-vial:before { content: @fa-var-vial; }
.@{fa-css-prefix}-vials:before { content: @fa-var-vials; }
.@{fa-css-prefix}-viber:before { content: @fa-var-viber; }
.@{fa-css-prefix}-video:before { content: @fa-var-video; }
.@{fa-css-prefix}-video-plus:before { content: @fa-var-video-plus; }
.@{fa-css-prefix}-video-slash:before { content: @fa-var-video-slash; }
.@{fa-css-prefix}-vihara:before { content: @fa-var-vihara; }
.@{fa-css-prefix}-vimeo:before { content: @fa-var-vimeo; }
.@{fa-css-prefix}-vimeo-square:before { content: @fa-var-vimeo-square; }
.@{fa-css-prefix}-vimeo-v:before { content: @fa-var-vimeo-v; }
.@{fa-css-prefix}-vine:before { content: @fa-var-vine; }
.@{fa-css-prefix}-vk:before { content: @fa-var-vk; }
.@{fa-css-prefix}-vnv:before { content: @fa-var-vnv; }
.@{fa-css-prefix}-voicemail:before { content: @fa-var-voicemail; }
.@{fa-css-prefix}-volcano:before { content: @fa-var-volcano; }
.@{fa-css-prefix}-volleyball-ball:before { content: @fa-var-volleyball-ball; }
.@{fa-css-prefix}-volume:before { content: @fa-var-volume; }
.@{fa-css-prefix}-volume-down:before { content: @fa-var-volume-down; }
.@{fa-css-prefix}-volume-mute:before { content: @fa-var-volume-mute; }
.@{fa-css-prefix}-volume-off:before { content: @fa-var-volume-off; }
.@{fa-css-prefix}-volume-slash:before { content: @fa-var-volume-slash; }
.@{fa-css-prefix}-volume-up:before { content: @fa-var-volume-up; }
.@{fa-css-prefix}-vote-nay:before { content: @fa-var-vote-nay; }
.@{fa-css-prefix}-vote-yea:before { content: @fa-var-vote-yea; }
.@{fa-css-prefix}-vr-cardboard:before { content: @fa-var-vr-cardboard; }
.@{fa-css-prefix}-vuejs:before { content: @fa-var-vuejs; }
.@{fa-css-prefix}-walker:before { content: @fa-var-walker; }
.@{fa-css-prefix}-walking:before { content: @fa-var-walking; }
.@{fa-css-prefix}-wallet:before { content: @fa-var-wallet; }
.@{fa-css-prefix}-wand:before { content: @fa-var-wand; }
.@{fa-css-prefix}-wand-magic:before { content: @fa-var-wand-magic; }
.@{fa-css-prefix}-warehouse:before { content: @fa-var-warehouse; }
.@{fa-css-prefix}-warehouse-alt:before { content: @fa-var-warehouse-alt; }
.@{fa-css-prefix}-washer:before { content: @fa-var-washer; }
.@{fa-css-prefix}-watch:before { content: @fa-var-watch; }
.@{fa-css-prefix}-watch-fitness:before { content: @fa-var-watch-fitness; }
.@{fa-css-prefix}-water:before { content: @fa-var-water; }
.@{fa-css-prefix}-water-lower:before { content: @fa-var-water-lower; }
.@{fa-css-prefix}-water-rise:before { content: @fa-var-water-rise; }
.@{fa-css-prefix}-wave-sine:before { content: @fa-var-wave-sine; }
.@{fa-css-prefix}-wave-square:before { content: @fa-var-wave-square; }
.@{fa-css-prefix}-wave-triangle:before { content: @fa-var-wave-triangle; }
.@{fa-css-prefix}-waze:before { content: @fa-var-waze; }
.@{fa-css-prefix}-webcam:before { content: @fa-var-webcam; }
.@{fa-css-prefix}-webcam-slash:before { content: @fa-var-webcam-slash; }
.@{fa-css-prefix}-weebly:before { content: @fa-var-weebly; }
.@{fa-css-prefix}-weibo:before { content: @fa-var-weibo; }
.@{fa-css-prefix}-weight:before { content: @fa-var-weight; }
.@{fa-css-prefix}-weight-hanging:before { content: @fa-var-weight-hanging; }
.@{fa-css-prefix}-weixin:before { content: @fa-var-weixin; }
.@{fa-css-prefix}-whale:before { content: @fa-var-whale; }
.@{fa-css-prefix}-whatsapp:before { content: @fa-var-whatsapp; }
.@{fa-css-prefix}-whatsapp-square:before { content: @fa-var-whatsapp-square; }
.@{fa-css-prefix}-wheat:before { content: @fa-var-wheat; }
.@{fa-css-prefix}-wheelchair:before { content: @fa-var-wheelchair; }
.@{fa-css-prefix}-whistle:before { content: @fa-var-whistle; }
.@{fa-css-prefix}-whmcs:before { content: @fa-var-whmcs; }
.@{fa-css-prefix}-wifi:before { content: @fa-var-wifi; }
.@{fa-css-prefix}-wifi-1:before { content: @fa-var-wifi-1; }
.@{fa-css-prefix}-wifi-2:before { content: @fa-var-wifi-2; }
.@{fa-css-prefix}-wifi-slash:before { content: @fa-var-wifi-slash; }
.@{fa-css-prefix}-wikipedia-w:before { content: @fa-var-wikipedia-w; }
.@{fa-css-prefix}-wind:before { content: @fa-var-wind; }
.@{fa-css-prefix}-wind-turbine:before { content: @fa-var-wind-turbine; }
.@{fa-css-prefix}-wind-warning:before { content: @fa-var-wind-warning; }
.@{fa-css-prefix}-window:before { content: @fa-var-window; }
.@{fa-css-prefix}-window-alt:before { content: @fa-var-window-alt; }
.@{fa-css-prefix}-window-close:before { content: @fa-var-window-close; }
.@{fa-css-prefix}-window-maximize:before { content: @fa-var-window-maximize; }
.@{fa-css-prefix}-window-minimize:before { content: @fa-var-window-minimize; }
.@{fa-css-prefix}-window-restore:before { content: @fa-var-window-restore; }
.@{fa-css-prefix}-windows:before { content: @fa-var-windows; }
.@{fa-css-prefix}-windsock:before { content: @fa-var-windsock; }
.@{fa-css-prefix}-wine-bottle:before { content: @fa-var-wine-bottle; }
.@{fa-css-prefix}-wine-glass:before { content: @fa-var-wine-glass; }
.@{fa-css-prefix}-wine-glass-alt:before { content: @fa-var-wine-glass-alt; }
.@{fa-css-prefix}-wix:before { content: @fa-var-wix; }
.@{fa-css-prefix}-wizards-of-the-coast:before { content: @fa-var-wizards-of-the-coast; }
.@{fa-css-prefix}-wolf-pack-battalion:before { content: @fa-var-wolf-pack-battalion; }
.@{fa-css-prefix}-won-sign:before { content: @fa-var-won-sign; }
.@{fa-css-prefix}-wordpress:before { content: @fa-var-wordpress; }
.@{fa-css-prefix}-wordpress-simple:before { content: @fa-var-wordpress-simple; }
.@{fa-css-prefix}-wpbeginner:before { content: @fa-var-wpbeginner; }
.@{fa-css-prefix}-wpexplorer:before { content: @fa-var-wpexplorer; }
.@{fa-css-prefix}-wpforms:before { content: @fa-var-wpforms; }
.@{fa-css-prefix}-wpressr:before { content: @fa-var-wpressr; }
.@{fa-css-prefix}-wreath:before { content: @fa-var-wreath; }
.@{fa-css-prefix}-wrench:before { content: @fa-var-wrench; }
.@{fa-css-prefix}-x-ray:before { content: @fa-var-x-ray; }
.@{fa-css-prefix}-xbox:before { content: @fa-var-xbox; }
.@{fa-css-prefix}-xing:before { content: @fa-var-xing; }
.@{fa-css-prefix}-xing-square:before { content: @fa-var-xing-square; }
.@{fa-css-prefix}-y-combinator:before { content: @fa-var-y-combinator; }
.@{fa-css-prefix}-yahoo:before { content: @fa-var-yahoo; }
.@{fa-css-prefix}-yammer:before { content: @fa-var-yammer; }
.@{fa-css-prefix}-yandex:before { content: @fa-var-yandex; }
.@{fa-css-prefix}-yandex-international:before { content: @fa-var-yandex-international; }
.@{fa-css-prefix}-yarn:before { content: @fa-var-yarn; }
.@{fa-css-prefix}-yelp:before { content: @fa-var-yelp; }
.@{fa-css-prefix}-yen-sign:before { content: @fa-var-yen-sign; }
.@{fa-css-prefix}-yin-yang:before { content: @fa-var-yin-yang; }
.@{fa-css-prefix}-yoast:before { content: @fa-var-yoast; }
.@{fa-css-prefix}-youtube:before { content: @fa-var-youtube; }
.@{fa-css-prefix}-youtube-square:before { content: @fa-var-youtube-square; }
.@{fa-css-prefix}-zhihu:before { content: @fa-var-zhihu; }


// Screen Readers
// -------------------------

.sr-only { .sr-only(); }
.sr-only-focusable { .sr-only-focusable(); }';
	return $__finalCompiled;
});