<?php
// FROM HASH: 4082ebc93ff618ef3d324c993218c385
return array('macros' => array(), 'code' => function($__templater, array $__vars)
{
	$__finalCompiled = '';
	$__finalCompiled .= '/***** froala.less *****/

/***** variables.less *****/

// Theme Name.
@theme: \'theme\';

// UI colors
@ui-color: #1E88E5;
@ui-text: #222222;
@ui-hover-light-color: mix(@white, #999, 90%);
@ui-hover-color: mix(@white, #999, 80%);
@ui-focused-color: mix(@white, #999, 60%);
@ui-disabled-color: mix(@white, #000, 74%);
@ui-bg: @white;
@ui-font-size: 14px;
@ui-border-color: #222222;
@ui-border-top: 5px solid @ui-border-color;

// Separator
@separator-size: 1px;
@separator-color: mix(@white, #999, 80%);

// Generic.
@white: #FFF;
@black: #000;
@gray: #CCCCCC;
@font-family: Arial, Helvetica, sans-serif;
@border-radius: 2px;
@arrow-size: 5px;
@transition-timing: 0.2s ease 0s;

// Screen sizes.
@screen-xs: 480px;
@screen-sm: 768px;
@screen-md: 992px;
@screen-lg: 1200px;
@screen-xs-max: (@screen-sm - 1);
@screen-sm-max: (@screen-md - 1);
@screen-md-max: (@screen-lg - 1);
@screen-lg-max: \'auto\';

// Tooltip
@tooltip-bg: #222222;
@tooltip-text: #FFFFFF;
@tooltip-font-size: 11px;
@tooltip-line-height: 22px;

// Editor properties.
@editor-padding: 16px;
@editor-bg: #FFF;
@editor-text: #000;
@editor-shadow-level: 1;
@editor-border: 0px;

// Text selection colors.
@selection-bg: #b5d6fd;
@selection-text: #000;

// Placeholder properties.
@placeholder-size: 12px;
@placeholder-color: #AAA;

// Button colors.
@btn-text: #222222;
@btn-hover-text: #222222;
@btn-hover-bg: @ui-hover-color;

@btn-active-text: @ui-color;
@btn-active-bg: transparent;
@btn-active-hover-text: @ui-color;
@btn-active-hover-bg: @btn-hover-bg;

@btn-selected-text: @btn-text;
@btn-selected-bg: @ui-focused-color;
@btn-active-selected-text: @ui-color;
@btn-active-selected-bg: @btn-selected-bg;

@btn-disabled-color: @ui-disabled-color;

// Button size.
@btn-width: 38px;
@btn-height: 38px;
@btn-margin: 2px;
@btn-font-size: 14px;

// Image.
@image-margin: 5px;

// Image and Video
@handler-size: 12px;
@handler-size-lg: 10px;

// Code View
@code-view-bg: #FFF;
@code-view-text: #000;

// Table properties.
@table-border: 1px solid #DDD;
@table-resizer: 1px solid @ui-color;

// Insert table grid.
@insert-table-grid: @table-border;

// Quick insert.
@floating-btn-bg: #FFF;
@floating-btn-text: @ui-color;
@floating-btn-hover-bg: @ui-hover-color;
@floating-btn-hover-text: @ui-color;
@floating-btn-size: 32px;
@floating-btn-font-size: 14px;
@floating-btn-border: none;

// List menu.
@dropdown-arrow-width: 4px;
@dropdown-item-active-bg: @ui-focused-color;
@dropdown-max-height: 275px;
@dropdown-options-width: 16px;
@dropdown-options-margin-left: -5px;
@dropdown-options-border-left: solid 1px #FAFAFA;

// Image manager.
@modal-bg: @white;
@modal-overlay-color: #000;

// Destroy buttons (delete in image manager).
@modal-destroy-btn-bg: #B8312F;
@modal-destroy-btn-text: @white;
@modal-destroy-btn-hover-bg: mix(contrast(@modal-destroy-btn-bg, @black, @white, 50%), @modal-destroy-btn-bg, 10%);
@modal-destroy-btn-hover-text: @white;

// Popups.
@popup-layer-width: 300px;

// Inputs.
@input-border-color: mix(@white, #000, 74%);
@input-label-color: mix(@white, #000, 50%);

// Quote.
@blockquote-level1-color: #5E35B1;
@blockquote-level2-color: #00BCD4;
@blockquote-level3-color: #43A047;

// Char counter.
@char-counter-border: solid 1px mix(@white, #999, 80%);

@drag-helper-border: solid 1px @ui-color;
@drag-helper-bg: @ui-color;


// ################# CUSTOM OVERRIDES ################
' . $__templater->includeTemplate('editor_override.less', $__vars) . '


/***** mixins.less *****/

.resize(@val) {
  resize: @val;
  -moz-resize: @val;
  -webkit-resize: @val;
}

.opacity (@opacity: 0.5) {
	-webkit-opacity: 	@opacity;
	-moz-opacity: 		@opacity;
	opacity: 		@opacity;
  -ms-filter: "progid:DXImageTransform.Microsoft.Alpha(Opacity=0)";
}

.transition(@transition) {
	-webkit-transition: @transition;
	-moz-transition:    @transition;
	-ms-transition:     @transition;
	-o-transition:      @transition;
}

.transform(@string){
	-webkit-transform: @string;
	-moz-transform: 	 @string;
	-ms-transform: 		 @string;
	-o-transform: 		 @string;
}

.box-sizing (@type: border-box) {
	-webkit-box-sizing: @type;
	-moz-box-sizing:    @type;
	box-sizing:         @type;
}

.border-radius (@radius: 0) {
  border-radius: @radius;
  -moz-border-radius: @radius;
  -webkit-border-radius: @radius;

  -moz-background-clip:    padding;
	-webkit-background-clip: padding-box;
	background-clip:         padding-box;
}

.user-select(@select) {
  user-select: @select;
  -o-user-select:@select;
  -moz-user-select: @select;
  -khtml-user-select: @select;
  -webkit-user-select: @select;
  -ms-user-select: @select;
}

.box-shadow(@shadow) {
  -webkit-box-shadow: @shadow;
  -moz-box-shadow: @shadow;
  box-shadow: @shadow;
}

.material-box-shadow (@level: 1, @direction: 1) when (@level = 0) {
  .box-shadow(none);
}

.material-box-shadow (@level: 1, @direction: 1) when (@level = 1) {
  @shadow: 0 (@direction * 1px) 3px rgba(0,0,0,0.12), 0 (@direction * 1px) 1px 1px rgba(0,0,0,0.16);
  .box-shadow(@shadow);
}

.material-box-shadow (@level: 1, @direction: 1) when (@level = 2) {
  @shadow: 0 (@direction * 3px) 6px rgba(0,0,0,0.16), 0 (@direction * 2px) 2px 1px rgba(0,0,0,0.14);
  .box-shadow(@shadow);
}

.material-box-shadow (@level: 1, @direction: 1) when (@level = 3) {
  @shadow: 0 (@direction * 5px) 8px rgba(0,0,0,0.19), 0 (@direction * 4px) 3px 1px rgba(0,0,0,0.14);
  .box-shadow(@shadow);
}

.material-box-shadow (@level: 1, @direction: 1) when (@level = 4) {
  @shadow: 0 (@direction * 8px) 12px rgba(0,0,0,0.25), 0 (@direction * 6px) 3px 1px rgba(0,0,0,0.12);
  .box-shadow(@shadow);
}

.material-box-shadow (@level: 1, @direction: 1) when (@level >= 5) {
  @shadow: 0 (@direction * 10px) 16px rgba(0,0,0,0.30), 0 (@direction * 6px) 8px rgba(0,0,0,0.22);
  .box-shadow(@shadow);
}

.clearfix {
  &::after {
    clear: both;
    display: block;
    content: "";
    height: 0;
  }
}

.column-count(@count) {
  column-count: @count;
  -moz-column-count: @count;
  -webkit-column-count: @count;
}

.column-gap(@gap) {
  column-gap: @gap;
  -moz-column-gap: @gap;
  -webkit-column-gap: @gap;
}

.animation (@val) {
	-webkit-animation: @val;
  -moz-animation: @val;
  -o-animation: @val;
  animation: @val;
}

.handler-size(@size) {
  .fr-handler {
    width: @size;
    height: @size;

    &.fr-hnw {
      left: (-@size / 2);
      top: (-@size / 2);
    }

    &.fr-hne {
      right: (-@size / 2);
      top: (-@size / 2);
    }

    &.fr-hsw {
      left: (-@size / 2);
      bottom: (-@size / 2);
    }

    &.fr-hse {
      right: (-@size / 2);
      bottom: (-@size / 2);
    }
  }
}

.font-smoothing() {
  -webkit-font-smoothing: antialiased;
  -moz-osx-font-smoothing: grayscale;
}

.hide-by-clipping {
  position: absolute;
  width: 1px;
  height: 1px;
  padding: 0;
  margin: -1px;
  overflow: hidden;
  clip: rect(0,0,0,0);
  border: 0;
}
.display-inline-flex() {

  display: -webkit-inline-flex;
  display: -ms-inline-flexbox;
  display: inline-flex;
}

/***** core/element.less *****/

.fr-element, .fr-element:focus {
  outline: 0px solid transparent;
}

.fr-box.fr-basic {
  .fr-element {
    color: @editor-text;
    padding: @editor-padding;
    .box-sizing(border-box);
    overflow-x: auto;
    min-height: (20px + (2 * @editor-padding));
  }

  &.fr-rtl {
    .fr-element {
      text-align: right;
    }
  }
}

.fr-element {
  background: transparent;
  position: relative;
  z-index: 2;

  // CSS rule for iPad not being able to select sometimes.
  -webkit-user-select: auto;

  // Fix bootstrap select.
  a {
    .user-select(auto);
  }

  &.fr-disabled {
    .user-select(none);
  }

  [contenteditable="true"] {
    outline: 0px solid transparent;
  }
}

.fr-box {
  a.fr-floating-btn {
    .material-box-shadow (@editor-shadow-level);
    .border-radius(100%);
    height: @floating-btn-size;
    width: @floating-btn-size;
    text-align: center;
    background: @floating-btn-bg;
    color: @floating-btn-text;
    .transition(background @transition-timing, color @transition-timing, transform @transition-timing;);
    outline: none;
    left: 0;
    top: 0;
    line-height: (@floating-btn-size);
    .transform(scale(0));
    text-align: center;
    display: block;
    .box-sizing(border-box);
    border: @floating-btn-border;

    svg {
      .transition(transform @transition-timing;);
      fill: @floating-btn-text;
    }

    i, svg {
      font-size: @floating-btn-font-size;
      line-height: @floating-btn-size;
    }

    &.fr-btn + .fr-btn {
      margin-left: 10px;
    }

    &:hover {
      background: @floating-btn-hover-bg;
      cursor: pointer;

      svg {
        fill: @floating-btn-hover-text;
      }
    }
  }

  .fr-visible {
    a.fr-floating-btn {
      .transform(scale(1));
    }
  }
}

/***** core/iframe.less *****/

iframe.fr-iframe {
  width: 100%;
  border: none;
  position: relative;
  display: block;
  z-index: 2;
  .box-sizing(border-box);
}

/***** core/wrapper.less *****/

.fr-wrapper {
  position: relative;
  z-index: 1;
  .clearfix();

  .fr-placeholder {
    position: absolute;
    font-size: @placeholder-size;
    color: @placeholder-color;
    z-index: 1;
    display: none;
    top: 0;
    left: 0;
    right: 0;
    overflow: hidden;
  }

  &.show-placeholder {
    .fr-placeholder {
      display: block;
    }
  }

  ::-moz-selection {
    background: @selection-bg;
    color: @selection-text;
  }

  ::selection {
    background: @selection-bg;
    color: @selection-text;
  }
}

.fr-box.fr-basic {
  .fr-wrapper {
    background: @editor-bg;
    border: @editor-border;
    border-top: 0;
    top: 0;
    left: 0;
  }
}

.fr-box.fr-basic {
  &.fr-top .fr-wrapper {
    border-top: 0;
    .border-radius(0 0 @border-radius @border-radius);
    .material-box-shadow(@editor-shadow-level);
  }

  &.fr-bottom .fr-wrapper {
    border-bottom: 0;
    .border-radius(@border-radius @border-radius 0 0);
    .material-box-shadow(@editor-shadow-level, -1);
  }
}

@media (min-width: @screen-md) {
  .fr-box.fr-document {
    min-width: 21cm;

    .fr-wrapper {
      text-align: left;
      padding: 30px;
      min-width: 21cm;
      background: #EFEFEF;

      .fr-element {
        text-align: left;
        background: #FFF;
        width: 21cm;
        margin: auto;
        min-height: 26cm !important;
        padding: 1cm 2cm;
        .material-box-shadow(@editor-shadow-level);
        overflow: visible;
        z-index: auto;

        hr {
          margin-left: -2cm;
          margin-right: -2cm;
          background: #EFEFEF;
          height: 1cm;
          outline: none;
          border: none;
        }

        img {
          z-index: 1;
        }
      }
    }
  }
}

/***** tooltip.less *****/

.fr-tooltip {
  position: absolute;
  top: 0;
  left: 0;
  padding: 0 8px;
  .border-radius(@border-radius);
  .material-box-shadow(((@editor-shadow-level + 1) * min(@editor-shadow-level, 1)));
  background: @tooltip-bg;
  color: @tooltip-text;
  font-size: @tooltip-font-size;
  line-height: @tooltip-line-height;
  font-family: @font-family;
  .transition(opacity @transition-timing);
  .opacity(0);
  left: -3000px;
  .user-select(none);
  z-index: 2147483647;
  text-rendering: optimizelegibility;
  .font-smoothing();

  &.fr-visible {
    .opacity(1);
  }
}

/***** ui/buttons.less *****/

// Command button.

.fr-toolbar, .fr-popup {
  .fr-btn-wrap {
    float: left;
    white-space: nowrap;
    position: relative;

    &.fr-hidden {
      display: none;
    }
  }

  .fr-command.fr-btn {
    background: transparent;
    color: @btn-text;
  	-moz-outline: 0;
  	outline: 0;
    border: 0;
    line-height: 1;
  	cursor: pointer;
    text-align: left;
  	margin: 0px @btn-margin;
    .transition(background @transition-timing);
    .border-radius(0);
    z-index: 2;
    position: relative;
    .box-sizing(border-box);
    text-decoration: none;
    .user-select(none);
    float: left;
    padding: 0;
    width: @btn-width;
    height: @btn-height;

    &::-moz-focus-inner {
      border: 0;
      padding: 0
    }

    &.fr-btn-text {
      width: auto;
    }

    i, svg {
      display: block;
      font-size: @btn-font-size;
      width: @btn-font-size;
      margin: ((@btn-height - @btn-font-size) / 2) ((@btn-width - @btn-font-size) / 2);
      text-align: center;
      float: none;
    }

    // Used for accessibility instead of aria-label.
    span.fr-sr-only {
      .hide-by-clipping();
    }

    span {
      font-size: @ui-font-size;
      display: block;
      line-height:  (@ui-font-size + 3px);
      min-width: (@btn-width - 2 * @btn-margin);
      float: left;
      text-overflow: ellipsis;
      overflow: hidden;
      white-space: nowrap;
      height: (@btn-font-size + 3px);
      font-weight: bold;
      padding: 0 @btn-margin;
    }

    img {
      margin: ((@btn-height - @btn-font-size) / 2) ((@btn-width - @btn-font-size) / 2);
      width: @btn-font-size;
    }

    // Button is active.
    &.fr-active {
      color: @btn-active-text;
      background: @btn-active-bg;
    }

    &.fr-dropdown {
      &.fr-selection {
        width: auto;

        span {
          font-weight: normal;
        }
      }

      i, span, img, svg {
        margin-left: (((@btn-width - @btn-font-size) / 2) - @dropdown-arrow-width);
        margin-right: (((@btn-width - @btn-font-size) / 2) + @dropdown-arrow-width);
      }

      // Dropdown is visible.
      &.fr-active {
        color: @btn-text;
        background: @btn-selected-bg;

        &:hover, &:focus {
          background: @btn-selected-bg !important;
          color: @btn-selected-text !important;

          &::after {
            border-top-color: @btn-selected-text !important;
          }
        }
      }

      &::after {
        position: absolute;
        width: 0;
      	height: 0;
      	border-left: @dropdown-arrow-width solid transparent;
      	border-right: @dropdown-arrow-width solid transparent;
      	border-top: @dropdown-arrow-width solid @btn-text;
        right: (((@btn-width - @btn-font-size) / 2 - @dropdown-arrow-width) / 2);
        top: ((@btn-height - @dropdown-arrow-width) / 2);
        content: "";
      }
    }

    &.fr-disabled {
      color: @btn-disabled-color;
      cursor: default;

      &::after {
        border-top-color: @btn-disabled-color !important;
      }
    }

    &.fr-hidden {
      display: none;
    }
  }

  &.fr-disabled {
    .fr-btn, .fr-btn.fr-active {
      color: @btn-disabled-color;

      &.fr-dropdown::after {
        border-top-color: @btn-disabled-color;
      }
    }
  }

  &.fr-rtl {
    .fr-command.fr-btn, .fr-btn-wrap {
      float: right;
    }
  }
}

.fr-toolbar.fr-inline {
  > .fr-command.fr-btn:not(.fr-hidden), > .fr-btn-wrap:not(.fr-hidden) {
    .display-inline-flex();
    float: none;
  }
}

.fr-desktop {
  .fr-command {
    // Hover.
    &:hover, &:focus, &.fr-btn-hover, &.fr-expanded {
      outline: 0;
      color: @btn-hover-text;
      background: @btn-hover-bg;

      &::after {
        border-top-color: @btn-hover-text !important;
      }
    }

    // Button is selected.
    &.fr-selected {
      color: @btn-selected-text;
      background: @btn-selected-bg;
    }

    &.fr-active {
      &:hover, &:focus, &.fr-btn-hover, &.fr-expanded {
        color: @btn-active-hover-text;
        background: @btn-active-hover-bg;
      }

      &.fr-selected {
        color: @btn-active-selected-text;
        background: @btn-active-selected-bg;
      }
    }

    &.fr-disabled {
      &:hover, &:focus, &.fr-selected {
        background: transparent;
      }
    }
  }

  &.fr-disabled {
    .fr-command {
      &:hover, &:focus, &.fr-selected {
        background: transparent;
      }
    }
  }
}

.fr-toolbar.fr-mobile, .fr-popup.fr-mobile {
  .fr-command.fr-blink {
    background: @btn-active-bg;
  }
}

/***** ui/dropdown.less *****/

.fr-command.fr-btn {
  &.fr-options {
    width: @dropdown-options-width;
    margin-left: @dropdown-options-margin-left;

    &.fr-btn-hover, &:hover, &:focus {
      border-left: @dropdown-options-border-left;
    }
  }

  + .fr-dropdown-menu {
    display: inline-block;
    position: absolute;
    right: auto;
    bottom: auto;
    height: auto;
    z-index: 4;
    -webkit-overflow-scrolling: touch;
    overflow: hidden;
    zoom: 1;
    .border-radius(0 0 @border-radius @border-radius);

    &.test-height {
      .fr-dropdown-wrapper {
        .transition(none);
        height: auto;
        max-height: @dropdown-max-height;
      }
    }

    .fr-dropdown-wrapper {
      background: @ui-bg;
      padding: 0;
      margin: auto;
      display: inline-block;
      text-align: left;
      position: relative;
      .box-sizing(border-box);
      .transition(max-height @transition-timing);
      margin-top: 0;
      float: left;
      max-height: 0;
      height: 0;
      margin-top: 0 !important;

      .fr-dropdown-content {
        overflow: auto;
        position: relative;
        max-height: @dropdown-max-height;

        ul.fr-dropdown-list {
          list-style-type: none;
          margin: 0;
          padding: 0;

          li {
            padding: 0;
            margin: 0;
            font-size: 15px;

            a {
              padding: 0 24px;
              line-height: 200%;
              display: block;
              cursor: pointer;
              white-space: nowrap;
              color: inherit;
              text-decoration: none;

              &.fr-active {
                background: @dropdown-item-active-bg;
              }

              &.fr-disabled {
                color: @btn-disabled-color;
                cursor: default;
              }

              .fr-shortcut {
                float: right;
                margin-left: 32px;
                font-weight: bold;
                .opacity(0.75);
              }
            }
          }
        }
      }
    }
  }

  &:not(.fr-active) {
    + .fr-dropdown-menu {
      left: -3000px !important;
    }
  }

  &.fr-active {
    + .fr-dropdown-menu {
      display: inline-block;
      .material-box-shadow(((@editor-shadow-level + 1) * min(@editor-shadow-level, 1)));

      .fr-dropdown-wrapper {
        height: auto;
        max-height: @dropdown-max-height;
      }
    }
  }
}

.fr-bottom > .fr-command.fr-btn {
  + .fr-dropdown-menu {
    .border-radius(@border-radius @border-radius 0 0);
    .material-box-shadow((@editor-shadow-level + 1) * min(@editor-shadow-level, 1), -1);
  }
}

.fr-toolbar, .fr-popup {
  &.fr-rtl {
    .fr-dropdown-wrapper {
      text-align: right !important;
    }
  }
}

/***** ui/modal.less *****/

body.prevent-scroll {
  overflow: hidden;

  &.fr-mobile {
    position: fixed;
    -webkit-overflow-scrolling: touch;
  }
}

.fr-modal {
  color: @ui-text;
  font-family: @font-family;
  position: fixed;
  overflow-x: auto;
  overflow-y: scroll;
  top: 0;
  left: 0;
  bottom: 0;
  right: 0;
  width: 100%;
  z-index: 2147483640;
  text-rendering: optimizelegibility;
  .font-smoothing();
  text-align: center;
  line-height: 1.2;

  &.fr-middle {
    .fr-modal-wrapper {
      margin-top: 0;
      margin-bottom: 0;
      margin-left: auto;
      margin-right: auto;
      top: 50%;
      left: 50%;
      .transform(translate(-50%, -50%));
      position: absolute;
    }
  }

  .fr-modal-wrapper {
    .border-radius(@border-radius);
    margin: 20px auto;
    display: inline-block;
    background: @modal-bg;
    min-width: 300px;
    .material-box-shadow(((@editor-shadow-level + 2) * min(@editor-shadow-level, 1)));
    border: @editor-border;
    border-top: @ui-border-top;
    overflow: hidden;
    width: 90%;
    position: relative;

    @media (min-width: @screen-sm) and (max-width: @screen-sm-max) {
      margin: 30px auto;
      width: 70%;
    }

    @media (min-width: @screen-md) {
      margin: 50px auto;
      width: 960px;
    }

    .fr-modal-head {
      background: @ui-bg;
      .material-box-shadow(((@editor-shadow-level + 1) * min(@editor-shadow-level, 1)));
      border-bottom: @editor-border;
      overflow: hidden;
      position: absolute;
      width: 100%;
      min-height: 42px;
      z-index: 3;
      .transition(height @transition-timing);

      .fr-modal-close {
        padding: 12px;
        width: 20px;
        font-size: 30px;
        cursor: pointer;
        line-height: 18px;
        color: @ui-text;
        .box-sizing(content-box);
        position: absolute;
        top: 0;
        right: 0;
        .transition(color @transition-timing);
      }

      h4 {
        font-size: 18px;
        padding: 12px 10px;
        margin: 0;
        font-weight: 400;
        line-height: 18px;
        display: inline-block;
        float: left;
      }
    }

    div.fr-modal-body {
      height: 100%;
      min-height: 150px;
      overflow-y: scroll;
      padding-bottom: 10px;

      &:focus {
        outline: 0;
      }

      button.fr-command {
        height: 36px;
        line-height: 1;
        color: @ui-color;
        padding: 10px;
        cursor: pointer;
        text-decoration: none;
        border: none;
        background: none;
        font-size: 16px;
        outline: none;
        .transition(background @transition-timing);
        .border-radius(@border-radius);

        + button {
          margin-left: 24px;
        }

        &:hover, &:focus {
          background: @ui-hover-color;
          color: @ui-color;
        }

        &:active {
          background: @ui-focused-color;
          color: @ui-color;
        }
      }

      button::-moz-focus-inner {
        border: 0;
      }
    }
  }
}

&.fr-desktop .fr-modal-wrapper {
  .fr-modal-head {
    i:hover {
      background: @ui-hover-color;
    }
  }
}

// Overlay that appears with modal.
.fr-overlay {
  position: fixed;
  top: 0;
  bottom: 0;
  left: 0;
  right: 0;
  background: @modal-overlay-color;
  .opacity(0.5);
  z-index: 2147483639;
}


/***** ui/popup.less *****/

.fr-popup {
  position: absolute;
  display: none;
  color: @ui-text;
  background: @ui-bg;
  .material-box-shadow(@editor-shadow-level);
  .border-radius(@border-radius);
  font-family: @font-family;
  .box-sizing(border-box);
  .user-select(none);
  margin-top: 10px;
  z-index: 2147483635;
  text-align: left;
  border: @editor-border;
  border-top: @ui-border-top;
  text-rendering: optimizelegibility;
  .font-smoothing();
  line-height: 1.2;

  .fr-input-focus {
    background: @ui-hover-light-color;
  }

  &.fr-above {
    margin-top: -10px;
    border-top: 0;
    border-bottom: @ui-border-top;
    .material-box-shadow(@editor-shadow-level, -1);
  }

  &.fr-active {
    display: block;
  }


  &.fr-hidden {
    .opacity(0);
  }

  &.fr-empty {
    display: none !important;
  }

  .fr-hs {
    display: block !important;

    &.fr-hidden {
      display: none !important;
    }
  }

  .fr-input-line {
    position: relative;
    padding: 8px 0;

    input[type="text"], textarea {
      width: 100%;
      margin: 0px 0 1px 0;
      border: none;
      border-bottom: solid 1px @input-border-color;
      color: @ui-text;
      font-size: 14px;
      padding: 6px 0 2px;
      background: rgba(0, 0, 0, 0);
      position: relative;
      z-index: 2;
      .box-sizing(border-box);

      &:focus {
        border-bottom: solid 2px @ui-color;
        margin-bottom: 0px;
      }
    }

    input + label, textarea + label {
      position: absolute;
      top: 0;
      left: 0;
      font-size: 12px;
      color: rgba(0, 0, 0, 0);
      .transition(color @transition-timing);
      z-index: 3;
      width: 100%;
      display: block;
      background: @ui-bg;
    }

    input.fr-not-empty:focus + label, textarea.fr-not-empty:focus + label {
      color: @ui-color;
    }

    input.fr-not-empty + label, textarea.fr-not-empty + label {
      color: @input-label-color;
    }
  }

  input, textarea {
    .user-select(text);
    .border-radius(0);
    outline: none;
  }

  textarea {
    resize: none;
  }

  .fr-buttons {
    .clearfix();
    .material-box-shadow(@editor-shadow-level);
    padding: 0 @btn-margin;
    white-space: nowrap;
    line-height: 0;
    border-bottom: @editor-border;

    .fr-btn {
      display: inline-block;
      float: none;

      i {
        float: left;
      }
    }

    .fr-separator {
      display: inline-block;
      float: none;
    }
  }

  .fr-layer {
    width: (@popup-layer-width * 0.75);
    @media (min-width: @screen-sm) {
      width: @popup-layer-width;
    }

    .box-sizing(border-box);
    margin: 10px;
    display: none;

    &.fr-active {
      display: inline-block;
    }
  }

  .fr-action-buttons {
    z-index: 7;
    height: 36px;
    text-align: right;

    button.fr-command {
      height: 36px;
      line-height: 1;
      color: @ui-color;
      padding: 10px;
      cursor: pointer;
      text-decoration: none;
      border: none;
      background: none;
      font-size: 16px;
      outline: none;
      .transition(background @transition-timing);
      .border-radius(@border-radius);

      + button {
        margin-left: 24px;
      }

      &:hover, &:focus {
        background: @ui-hover-color;
        color: @ui-color;
      }

      &:active {
        background: @ui-focused-color;
        color: @ui-color;
      }
    }

    button::-moz-focus-inner {
      border: 0;
    }
  }

  .fr-checkbox {
    position: relative;
    display: inline-block;
    width: 16px;
    height: 16px;
    line-height: 1;
    .box-sizing(content-box);
    vertical-align: middle;

    svg {
      margin-left: 2px;
      margin-top: 2px;
      display: none;
      width: 10px;
      height: 10px;
    }

    span {
      border: solid 1px @ui-text;
      .border-radius(@border-radius);
      width: 16px;
      height: 16px;
      display: inline-block;
      position: relative;
      z-index: 1;
      .box-sizing(border-box);
      .transition(background @transition-timing, border-color @transition-timing;);
    }

    input {
      position: absolute;
      z-index: 2;
      .opacity(0);
      border: 0 none;
      cursor: pointer;
      height: 16px;
      margin: 0;
      padding: 0;
      width: 16px;
      top: 1px;
      left: 1px;

      &:checked + span {
        background: @ui-color;
        border-color: @ui-color;

        svg {
          display: block;
        }
      }

      &:focus + span {
        border-color: @ui-color;
      }
    }
  }

  .fr-checkbox-line {
    font-size: 14px;
    line-height: 1.4px;
    margin-top: 10px;

    label {
      cursor: pointer;
      margin: 0 5px;
      vertical-align: middle;
    }
  }

  &.fr-rtl {
    direction: rtl;
    text-align: right;

    .fr-action-buttons {
      text-align: left;
    }

    .fr-input-line {
      input + label, textarea + label {
        left: auto;
        right: 0;
      }
    }

    .fr-buttons .fr-separator.fr-vs {
      float: right;
    }
  }

  .fr-arrow {
    width: 0;
    height: 0;
    border-left: @arrow-size solid transparent;
    border-right: @arrow-size solid transparent;
    border-bottom: @arrow-size solid @ui-border-color;
    position: absolute;
    top: ((-@arrow-size * 2) + 1);
    left: 50%;
    margin-left: (-@arrow-size);
    display: inline-block;
  }

  &.fr-above {
    .fr-arrow {
      top: auto;
      bottom: ((-@arrow-size * 2) + 1);
      border-bottom: 0;
      border-top: @arrow-size solid @ui-border-color;
    }
  }
}

/***** ui/text_edit.less *****/

.fr-text-edit-layer {
  width: 250px;
  .box-sizing(border-box);
  display: block !important;
}


/***** ui/toolbar.less *****/

.fr-toolbar {
  color: @ui-text;
  background: @ui-bg;
  position: relative;
  z-index: 4;
  font-family: @font-family;
  .clearfix();
  .box-sizing(border-box);
  .user-select(none);
  padding: 0 @btn-margin;
  .border-radius(@border-radius);
  .material-box-shadow(@editor-shadow-level);
  text-align: left;
  border: @editor-border;
  border-top: @ui-border-top;
  text-rendering: optimizelegibility;
  .font-smoothing();
  line-height: 1.2;

  &.fr-rtl {
    text-align: right;
  }

  &.fr-inline {
    display: none;

    white-space: nowrap;
    position: absolute;
    margin-top: 10px;

    .fr-arrow {
      width: 0;
      height: 0;
      border-left: @arrow-size solid transparent;
      border-right: @arrow-size solid transparent;
      border-bottom: @arrow-size solid @ui-border-color;
      position: absolute;
      top: ((-@arrow-size * 2) + 1);
      left: 50%;
      margin-left: (-@arrow-size);
      display: inline-block;
    }

    &.fr-above {
      margin-top: -10px;
      .material-box-shadow(@editor-shadow-level, -1);
      border-bottom: @ui-border-top;
      border-top: 0;

      .fr-arrow {
        top: auto;
        bottom: ((-@arrow-size * 2) + 1);
        border-bottom: 0;
        border-top-color: inherit;
        border-top-style: solid;
        border-top-width: @arrow-size;
      }
    }
  }

  &.fr-top {
    top: 0;
    .border-radius(@border-radius @border-radius 0 0);
    .material-box-shadow(@editor-shadow-level);
  }

  &.fr-bottom {
    bottom: 0;
    .border-radius(0 0 @border-radius @border-radius);
    .material-box-shadow(@editor-shadow-level);
  }
}

.fr-separator {
  background: @separator-color;
  display: block;
  vertical-align: top;
  float: left;

  + .fr-separator {
    display: none;
  }

  &.fr-vs {
    height: (@btn-height - 2 * @btn-margin);
    width: @separator-size;
    margin: @btn-margin;
  }

  &.fr-hs {
    clear: both;
    height: @separator-size;
    width: calc(100% - (2 * @btn-margin));
    margin: 0 @btn-margin;
  }

  &.fr-hidden {
    display: none !important;
  }
}

.fr-rtl .fr-separator {
  float: right;
}

.fr-toolbar.fr-inline .fr-separator.fr-hs {
  float: none;
}

.fr-toolbar.fr-inline .fr-separator.fr-vs {
  float: none;
  display: inline-block;
}

/***** helpers.less *****/

.fr-visibility-helper {
  display: none;
  margin-left: 0px !important;

  @media (min-width: @screen-sm) {
    margin-left: 1px !important;
  }

  @media (min-width: @screen-md) {
    margin-left: 2px !important;
  }

  @media (min-width: @screen-lg) {
    margin-left: 3px !important;
  }
}

.fr-opacity-0 {
  .opacity(0);
}

.fr-box {
  position: relative;
}

/**
 * Postion sticky hacks.
 */
.fr-sticky {
  position: -webkit-sticky;
	position: -moz-sticky;
	position: -ms-sticky;
	position: -o-sticky;
	position: sticky;
}

.fr-sticky-off {
	position: relative;
}

.fr-sticky-on {
	position: fixed;

  &.fr-sticky-ios {
    position: absolute;
    left: 0;
    right: 0;
    width: auto !important;
  }
}

.fr-sticky-dummy {
	display: none;
}

.fr-sticky-on + .fr-sticky-dummy, .fr-sticky-box > .fr-sticky-dummy {
	display: block;
}

// Used for accessibility instead of aria-label.
span.fr-sr-only {
  .hide-by-clipping();
}


/***** plugins/colors.less *****/

.fr-popup {
  .fr-colors-tabs {
    .material-box-shadow(@editor-shadow-level);
    margin-bottom: 5px;
    line-height: 16px;
    margin-left: -2px;
    margin-right: -2px;

    .fr-colors-tab {
      display: inline-block;
      width: 50%;
      cursor: pointer;
      text-align: center;
      color: @ui-text;
      font-size: 13px;
      padding: 8px 0;
      position: relative;

      &:hover, &:focus {
        color: @ui-color;
      }

      &[data-param1="background"]::after {
      	position: absolute;
      	bottom: 0;
      	left: 0;
      	width: 100%;
      	height: 2px;
      	background: @ui-color;
      	content: \'\';
        .transition(transform @transition-timing);
      }

      &.fr-selected-tab {
        color: @ui-color;

        &[data-param1="text"] ~ [data-param1="background"]::after {
          .transform(translate3d(-100%,0,0));
        }
      }
    }
  }

  .fr-color-hex-layer {
    width: 100%;
    margin: 0px;
    padding: 10px;

    .fr-input-line {
      float: left;
      width: calc(100% - 50px);
      padding: 8px 0 0;
    }

    .fr-action-buttons {
      float: right;
      width: 50px;
    }

    .fr-action-buttons {
      button.fr-command {
        background-color: @ui-color;
        color: #FFF !important;
        .border-radius(@border-radius);
        font-size: 13px;
        height: 32px;

        &:hover {
          background-color: darken(@ui-color, 10%);
          color: #FFF;
        }
      }
    }
  }

  .fr-separator + .fr-colors-tabs {
    .material-box-shadow(0, 0);
    margin-left: 2px;
    margin-right: 2px;
  }

  .fr-color-set {
    line-height: 0;
    display: none;

    &.fr-selected-set {
      display: block;
    }

    > span {
      display: inline-block;
      width: 32px;
      height: 32px;
      position: relative;
      z-index: 1;

      > i, > svg {
        text-align: center;
        line-height: 32px;
        height: 32px;
        width: 32px;
        font-size: 13px;
        position: absolute;
        bottom: 0;
        cursor: default;
        left: 0;
      }

      .fr-selected-color {
        color: @white;
        font-family: FontAwesome;
        font-size: 13px;
        font-weight: 400;
        line-height: 32px;
        position: absolute;
        top: 0;
        bottom: 0;
        right: 0;
        left: 0;
        text-align: center;
        cursor: default;
      }

      &:hover, &:focus {
        outline: 1px solid @ui-text;
        z-index: 2;
      }
    }
  }
}

.fr-rtl .fr-popup {
  .fr-colors-tabs {
    .fr-colors-tab {
      &.fr-selected-tab {
        &[data-param1="text"] ~ [data-param1="background"]::after {
          .transform(translate3d(100%,0,0));
        }
      }
    }
  }
}


/***** plugins/draggable.less *****/

.fr-drag-helper {
  background: @drag-helper-bg;
  height: 2px;
  margin-top: -1px;
  .opacity(0.2);
  position: absolute;
  z-index: 2147483640;
  display: none;

  &.fr-visible {
    display: block;
  }
}

.fr-dragging {
  .opacity(0.4);
}

/***** plugins/file.less *****/

.fr-popup {
  .fr-file-upload-layer {
    border: dashed 2px @ui-disabled-color;
    padding: 25px 0;
    position: relative;
    font-size: 14px;
    letter-spacing: 1px;
    line-height: 140%;
    .box-sizing(border-box);
    text-align: center;

    &:hover {
      background: @ui-hover-color;
    }

    &.fr-drop {
      background: @ui-hover-color;
      border-color: @ui-color;
    }

    .fr-form {
      .opacity(0);
      position: absolute;
      top: 0;
      bottom: 0;
      left: 0;
      right: 0;
      z-index: 2147483640;
      overflow: hidden;
      margin: 0 !important;
      padding: 0 !important;
      width: 100% !important;

      input {
        cursor: pointer;
        position: absolute;
        right: 0px;
        top: 0px;
        bottom: 0px;
        width: 500%;
        height: 100%;
        margin: 0px;
        font-size: 400px;
      }
    }
  }

  .fr-file-progress-bar-layer {
    .box-sizing(border-box);

    > h3 {
      font-size: 16px;
      margin: 10px 0;
      font-weight: normal;
    }

    > div.fr-action-buttons {
      display: none;
    }

    > div.fr-loader {
      background: mix(@white, @ui-color, 70%);
      height: 10px;
      width: 100%;
      margin-top: 20px;
      overflow: hidden;
      position: relative;

      span {
        display: block;
        height: 100%;
        width: 0%;
        background: @ui-color;
        .transition(width @transition-timing);
      }

      &.fr-indeterminate {
        span {
          width: 30% !important;
          position: absolute;
          top: 0;
          .animation(loading 2s linear infinite);
        }
      }
    }

    &.fr-error {
      > div.fr-loader {
        display: none;
      }

      > div.fr-action-buttons {
        display: block;
      }
    }
  }
}


@keyframes loading {
  from {left: -25%;}
  to {left: 100%}
}

@-webkit-keyframes loading {
  from {left: -25%;}
  to {left: 100%}
}

@-moz-keyframes loading {
  from {left: -25%;}
  to {left: 100%}
}

@-o-keyframes loading {
  from {left: -25%;}
  to {left: 100%}
}

/***** plugins/image.less *****/

.fr-element img {
  cursor: pointer;
}

.fr-image-resizer {
  position: absolute;
  border: solid 1px @ui-color;
  display: none;
  .user-select(none);
  .box-sizing(content-box);

  &.fr-active {
    display: block;
  }

  .fr-handler {
    display: block;
    position: absolute;
    background: @ui-color;
    border: solid 1px @white;
    z-index: 4;
    .box-sizing(border-box);

    &.fr-hnw {
      cursor: nw-resize;
    }

    &.fr-hne {
      cursor: ne-resize;
    }

    &.fr-hsw {
      cursor: sw-resize;
    }

    &.fr-hse {
      cursor: se-resize;
    }
  }

  .handler-size(@handler-size);

  @media(min-width: @screen-lg) {
    .handler-size(@handler-size-lg);
  }
}

.fr-image-overlay {
  position: fixed;
  top: 0;
  left: 0;
  bottom: 0;
  right: 0;
  z-index: 2147483640;
  display: none;
}

.fr-popup {
  .fr-image-upload-layer {
    border: dashed 2px @ui-disabled-color;
    padding: 25px 0;
    position: relative;
    font-size: 14px;
    letter-spacing: 1px;
    line-height: 140%;
    text-align: center;

    &:hover {
      background: @ui-hover-color;
    }

    &.fr-drop {
      background: @ui-hover-color;
      border-color: @ui-color;
    }

    .fr-form {
      .opacity(0);
      position: absolute;
      top: 0;
      bottom: 0;
      left: 0;
      right: 0;
      z-index: 2147483640;
      overflow: hidden;
      margin: 0 !important;
      padding: 0 !important;
      width: 100% !important;

      input {
        cursor: pointer;
        position: absolute;
        right: 0px;
        top: 0px;
        bottom: 0px;
        width: 500%;
        height: 100%;
        margin: 0px;
        font-size: 400px;
      }
    }
  }

  .fr-image-progress-bar-layer {
    > h3 {
      font-size: 16px;
      margin: 10px 0;
      font-weight: normal;
    }

    > div.fr-action-buttons {
      display: none;
    }

    > div.fr-loader {
      background: mix(@white, @ui-color, 70%);
      height: 10px;
      width: 100%;
      margin-top: 20px;
      overflow: hidden;
      position: relative;

      span {
        display: block;
        height: 100%;
        width: 0%;
        background: @ui-color;
        .transition(width @transition-timing);
      }

      &.fr-indeterminate {
        span {
          width: 30% !important;
          position: absolute;
          top: 0;
          .animation(loading 2s linear infinite);
        }
      }
    }

    &.fr-error {
      > div.fr-loader {
        display: none;
      }

      > div.fr-action-buttons {
        display: block;
      }
    }
  }
}

.fr-image-size-layer {
  .fr-image-group {
    .fr-input-line {
      width: calc(50% - 5px);
      display: inline-block;

      + .fr-input-line {
        margin-left: 10px;
      }
    }
  }
}

.fr-uploading {
  .opacity(0.4);
}

@keyframes loading {
  from {left: -25%;}
  to {left: 100%}
}

@-webkit-keyframes loading {
  from {left: -25%;}
  to {left: 100%}
}

@-moz-keyframes loading {
  from {left: -25%;}
  to {left: 100%}
}

@-o-keyframes loading {
  from {left: -25%;}
  to {left: 100%}
}

/***** plugins/table.less *****/

.fr-element {
  table {
    td.fr-selected-cell, th.fr-selected-cell {
      border: 1px double @ui-color;
    }

    // Prevent Firefox selection.
    tr {
      .user-select(none);
    }

    td, th {
      .user-select(text);
    }
  }

  // Prevent Firefox selection.
  .fr-no-selection {
    table {
      td, th {
        .user-select(none);
      }
    }
  }
}

.fr-table-resizer {
  cursor: col-resize;
  position: absolute;
  z-index: 3;
  display: none;

  &.fr-moving {
    z-index: 2;
  }

  div {
    .opacity(0);
    border-right: @table-resizer;
  }
}

.fr-no-selection {
  .user-select(none);
}

// Table popups.
.fr-popup {
  .fr-table-colors-hex-layer {
    width: 100%;
    margin: 0px;
    padding: 10px;

    .fr-input-line {
      float: left;
      width: calc(100% - 50px);
      padding: 8px 0 0;
    }

    .fr-action-buttons {
      float: right;
      width: 50px;
    }

    .fr-action-buttons {
      button {
        background-color: @ui-color;
        color: #FFF;
        .border-radius(@border-radius);
        font-size: 13px;
        height: 32px;

        &:hover {
          background-color: darken(@ui-color, 10%);
          color: #FFF;
        }
      }
    }
  }

  // Insert table.
  .fr-table-size {
    .fr-table-size-info {
      text-align: center;
      font-size: 14px;
      padding: 8px;
    }

    .fr-select-table-size {
      line-height: 0;
      padding: 0 5px 5px;
      white-space: nowrap;

      > span {
        display: inline-block;
        padding: 0px 4px 4px 0;
        background: transparent;

        > span {
          display: inline-block;
          width: 18px;
          height: 18px;
          border: @insert-table-grid;
        }

        &.hover {
          background: transparent;

          > span {
            background: rgba(red(@ui-color), green(@ui-color), blue(@ui-color), 0.3);
            border: solid 1px @ui-color;
          }
        }
      }

      .new-line {
        .clearfix();
      }
    }
  }

  &.fr-above {
    .fr-table-size {
      .fr-select-table-size {
        > span {
          display: inline-block !important;
        }
      }
    }
  }

  // Table colors.
  .fr-table-colors-buttons {
    margin-bottom: 5px;
  }

  .fr-table-colors {
    line-height: 0;
    display: block;

    > span {
      display: inline-block;
      width: 32px;
      height: 32px;
      position: relative;
      z-index: 1;

      > i {
        text-align: center;
        line-height: 32px;
        height: 32px;
        width: 32px;
        font-size: 13px;
        position: absolute;
        bottom: 0;
        cursor: default;
        left: 0;
      }

      &:focus {
        outline: 1px solid @ui-text;
        z-index: 2;
      }
    }
  }
}

.fr-popup.fr-desktop .fr-table-size .fr-select-table-size > span > span {
  width: 12px;
  height: 12px;
}

.fr-insert-helper {
  position: absolute;
  z-index: 9999;
  white-space: nowrap;
}


/***** plugins/video.less *****/

.fr-element {
  .fr-video {
    .user-select(none);

    &::after {
      position: absolute;
      content: \'\';
      z-index: 1;
      top: 0;
      left: 0;
      right: 0;
      bottom: 0;
      cursor: pointer;
      display: block;
      background: rgba(0,0,0,0);
    }

    &.fr-active > * {
      z-index: 2;
      position: relative;
    }

    > * {
      .box-sizing(content-box);
      max-width: 100%;
      border: none;
    }
  }
}

.fr-box .fr-video-resizer {
  position: absolute;
  border: solid 1px @ui-color;
  display: none;
  .user-select(none);

  &.fr-active {
    display: block;
  }

  .fr-handler {
    display: block;
    position: absolute;
    background: @ui-color;
    border: solid 1px @white;
    z-index: 4;
    .box-sizing(border-box);

    &.fr-hnw {
      cursor: nw-resize;
    }

    &.fr-hne {
      cursor: ne-resize;
    }

    &.fr-hsw {
      cursor: sw-resize;
    }

    &.fr-hse {
      cursor: se-resize;
    }
  }

  .handler-size(@handler-size);

  @media(min-width: @screen-lg) {
    .handler-size(@handler-size-lg);
  }
}

.fr-popup {
  .fr-video-size-layer {
    .fr-video-group {
      .fr-input-line {
        width: calc(50% - 5px);
        display: inline-block;

        + .fr-input-line {
          margin-left: 10px;
        }
      }
    }
  }

  .fr-video-upload-layer {
    border: dashed 2px @ui-disabled-color;
    padding: 25px 0;
    position: relative;
    font-size: 14px;
    letter-spacing: 1px;
    line-height: 140%;
    text-align: center;

    &:hover {
      background: @ui-hover-color;
    }

    &.fr-drop {
      background: @ui-hover-color;
      border-color: @ui-color;
    }

    .fr-form {
      .opacity(0);
      position: absolute;
      top: 0;
      bottom: 0;
      left: 0;
      right: 0;
      z-index: 2147483640;
      overflow: hidden;
      margin: 0 !important;
      padding: 0 !important;
      width: 100% !important;

      input {
        cursor: pointer;
        position: absolute;
        right: 0px;
        top: 0px;
        bottom: 0px;
        width: 500%;
        height: 100%;
        margin: 0px;
        font-size: 400px;
      }
    }
  }

  .fr-video-progress-bar-layer {
    > h3 {
      font-size: 16px;
      margin: 10px 0;
      font-weight: normal;
    }

    > div.fr-action-buttons {
      display: none;
    }

    > div.fr-loader {
      background: mix(@white, @ui-color, 70%);
      height: 10px;
      width: 100%;
      margin-top: 20px;
      overflow: hidden;
      position: relative;

      span {
        display: block;
        height: 100%;
        width: 0%;
        background: @ui-color;
        .transition(width @transition-timing);
      }

      &.fr-indeterminate {
        span {
          width: 30% !important;
          position: absolute;
          top: 0;
          .animation(loading 2s linear infinite);
        }
      }
    }

    &.fr-error {
      > div.fr-loader {
        display: none;
      }

      > div.fr-action-buttons {
        display: block;
      }
    }
  }
}

.fr-video-overlay {
  position: fixed;
  top: 0;
  left: 0;
  bottom: 0;
  right: 0;
  z-index: 2147483640;
  display: none;
}

/* Files: froala.less, plugins/colors.less, plugins/draggable.less, plugins/file.less, plugins/image.less, plugins/table.less, plugins/video.less */';
	return $__finalCompiled;
});